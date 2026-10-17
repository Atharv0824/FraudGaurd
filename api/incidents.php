<?php
/**
 * API: Incidents Endpoint
 * GET  /api/incidents.php - List incidents with filters & search
 * POST /api/incidents.php - Create a new fraud incident
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Query filters
        $search = trim($_GET['search'] ?? '');
        $category = trim($_GET['category'] ?? '');
        $severity = trim($_GET['severity'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $limit = max(1, min(100, (int)($_GET['limit'] ?? 50)));
        $offset = max(0, (int)($_GET['offset'] ?? 0));

        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = "(reference_id LIKE :s1 OR title LIKE :s2 OR suspect_details LIKE :s3 OR reporter_name LIKE :s4)";
            $params['s1'] = "%{$search}%";
            $params['s2'] = "%{$search}%";
            $params['s3'] = "%{$search}%";
            $params['s4'] = "%{$search}%";
        }

        if ($category !== '' && $category !== 'all') {
            $where[] = "category = :category";
            $params['category'] = $category;
        }

        if ($severity !== '' && $severity !== 'all') {
            $where[] = "severity = :severity";
            $params['severity'] = $severity;
        }

        if ($status !== '' && $status !== 'all') {
            $where[] = "status = :status";
            $params['status'] = $status;
        }

        $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM incidents {$whereClause}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetch()['total'];

        // Fetch records ordered by newest first
        $sql = "SELECT id, reference_id, title, category, severity, status, 
                       amount_lost, currency, incident_date, reporter_name, 
                       is_anonymous, suspect_details, assigned_investigator, 
                       risk_score, ip_address, created_at, updated_at
                FROM incidents 
                {$whereClause} 
                ORDER BY created_at DESC 
                LIMIT {$limit} OFFSET {$offset}";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $incidents = $stmt->fetchAll();

        // Enrich with category and status meta
        foreach ($incidents as &$inc) {
            $catKey = $inc['category'];
            $inc['category_meta'] = FRAUD_CATEGORIES[$catKey] ?? ['label' => ucfirst($catKey), 'icon' => 'bi-shield', 'color' => 'secondary'];
            $inc['severity_meta'] = SEVERITY_LEVELS[$inc['severity']] ?? ['badge' => 'secondary', 'weight' => 1];
            $inc['status_meta'] = INCIDENT_STATUSES[$inc['status']] ?? ['badge' => 'secondary', 'label' => $inc['status']];
        }

        echo json_encode([
            'success' => true,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'data' => $incidents
        ]);
        exit;
    }

    if ($method === 'POST') {
        // Parse JSON or POST payload
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?? $_POST;

        $title = trim($input['title'] ?? '');
        $category = trim($input['category'] ?? 'other');
        $severity = trim($input['severity'] ?? 'Medium');
        $amountLost = (float)($input['amount_lost'] ?? 0.0);
        $currency = strtoupper(trim($input['currency'] ?? 'INR'));
        $incidentDate = trim($input['incident_date'] ?? date('Y-m-d H:i:s'));
        $description = trim($input['description'] ?? '');
        $suspectDetails = trim($input['suspect_details'] ?? '');
        $isAnonymous = !empty($input['is_anonymous']) ? 1 : 0;
        $reporterName = $isAnonymous ? 'Confidential / Anonymous' : trim($input['reporter_name'] ?? '');
        $reporterEmail = $isAnonymous ? null : trim($input['reporter_email'] ?? '');
        $reporterPhone = $isAnonymous ? null : trim($input['reporter_phone'] ?? '');

        // Validation
        if (empty($title)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Incident title is required.']);
            exit;
        }

        if (empty($description)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Detailed description of the incident is required.']);
            exit;
        }

        if (!array_key_exists($category, FRAUD_CATEGORIES)) {
            $category = 'other';
        }

        if (!array_key_exists($severity, SEVERITY_LEVELS)) {
            $severity = 'Medium';
        }

        // Calculate dynamic risk score
        $riskScore = calculateRiskScore($severity, $amountLost, $category);
        $referenceId = generateReferenceId();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $now = date('Y-m-d H:i:s');

        $stmt = $pdo->prepare("
            INSERT INTO incidents (
                reference_id, title, category, severity, status, amount_lost, currency,
                incident_date, reporter_name, reporter_email, reporter_phone, is_anonymous,
                suspect_details, description, assigned_investigator, risk_score, ip_address, created_at, updated_at
            ) VALUES (
                :ref, :title, :cat, :sev, 'New', :amount, :curr,
                :date, :name, :email, :phone, :anon,
                :suspect, :desc, 'Unassigned', :score, :ip, :created, :updated
            )
        ");

        $stmt->execute([
            'ref' => $referenceId,
            'title' => $title,
            'cat' => $category,
            'sev' => $severity,
            'amount' => $amountLost,
            'curr' => $currency,
            'date' => $incidentDate,
            'name' => $reporterName,
            'email' => $reporterEmail,
            'phone' => $reporterPhone,
            'anon' => $isAnonymous,
            'suspect' => $suspectDetails,
            'desc' => $description,
            'score' => $riskScore,
            'ip' => $ip,
            'created' => $now,
            'updated' => $now
        ]);

        $incidentId = $pdo->lastInsertId();

        // Add timeline creation entry
        $noteStmt = $pdo->prepare("
            INSERT INTO incident_timeline (incident_id, author, action_type, note, created_at)
            VALUES (:id, :author, 'CREATED', :note, :date)
        ");

        $initialNote = "Incident reported by " . ($isAnonymous ? 'Anonymous Victim' : ($reporterName ?: 'User')) . ". Priority assigned as {$severity} with Risk Score {$riskScore}/100.";
        $noteStmt->execute([
            'id' => $incidentId,
            'author' => 'System Engine',
            'note' => $initialNote,
            'date' => $now
        ]);

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Fraud incident filed successfully.',
            'data' => [
                'id' => (int)$incidentId,
                'reference_id' => $referenceId,
                'risk_score' => $riskScore,
                'status' => 'New',
                'severity' => $severity,
                'category' => $category,
                'created_at' => $now
            ]
        ]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
