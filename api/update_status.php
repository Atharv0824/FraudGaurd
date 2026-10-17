<?php
/**
 * API: Update Incident Status & Triage Metadata
 * POST /api/update_status.php
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

try {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true) ?? $_POST;

    $id = (int)($input['id'] ?? 0);
    $status = trim($input['status'] ?? '');
    $severity = trim($input['severity'] ?? '');
    $investigator = trim($input['assigned_investigator'] ?? '');
    $note = trim($input['note'] ?? '');
    $author = trim($input['author'] ?? 'SOC Analyst');

    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Valid incident ID is required.']);
        exit;
    }

    // Fetch existing incident
    $fetchStmt = $pdo->prepare("SELECT * FROM incidents WHERE id = :id");
    $fetchStmt->execute(['id' => $id]);
    $existing = $fetchStmt->fetch();

    if (!$existing) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Incident not found.']);
        exit;
    }

    $updates = [];
    $params = ['id' => $id];
    $changesLogged = [];

    if (!empty($status) && array_key_exists($status, INCIDENT_STATUSES) && $status !== $existing['status']) {
        $updates[] = "status = :status";
        $params['status'] = $status;
        $changesLogged[] = "Status changed from '{$existing['status']}' to '{$status}'";
    }

    if (!empty($severity) && array_key_exists($severity, SEVERITY_LEVELS) && $severity !== $existing['severity']) {
        $updates[] = "severity = :severity";
        $params['severity'] = $severity;
        $changesLogged[] = "Severity adjusted from '{$existing['severity']}' to '{$severity}'";
        
        // Recalculate risk score
        $newScore = calculateRiskScore($severity, (float)$existing['amount_lost'], $existing['category']);
        $updates[] = "risk_score = :risk_score";
        $params['risk_score'] = $newScore;
    }

    if ($investigator !== '' && $investigator !== $existing['assigned_investigator']) {
        $updates[] = "assigned_investigator = :investigator";
        $params['investigator'] = $investigator;
        $changesLogged[] = "Investigator assigned: '{$investigator}'";
    }

    $now = date('Y-m-d H:i:s');
    $updates[] = "updated_at = :updated_at";
    $params['updated_at'] = $now;

    // Apply updates if any
    $updateSql = "UPDATE incidents SET " . implode(', ', $updates) . " WHERE id = :id";
    $upStmt = $pdo->prepare($updateSql);
    $upStmt->execute($params);

    // Record timeline log
    if (!empty($changesLogged) || !empty($note)) {
        $actionDesc = count($changesLogged) > 0 ? implode('; ', $changesLogged) : 'Investigation note added';
        $fullNote = $actionDesc;
        if (!empty($note)) {
            $fullNote .= "\nNotes: " . $note;
        }

        $tlStmt = $pdo->prepare("
            INSERT INTO incident_timeline (incident_id, author, action_type, note, created_at)
            VALUES (:id, :author, :action, :note, :date)
        ");

        $tlStmt->execute([
            'id' => $id,
            'author' => $author ?: 'SOC Analyst',
            'action' => !empty($status) ? 'STATUS_CHANGE' : 'NOTE_ADDED',
            'note' => $fullNote,
            'date' => $now
        ]);
    }

    // Return updated record
    $refreshedStmt = $pdo->prepare("SELECT * FROM incidents WHERE id = :id");
    $refreshedStmt->execute(['id' => $id]);
    $updatedIncident = $refreshedStmt->fetch();

    echo json_encode([
        'success' => true,
        'message' => 'Incident updated successfully.',
        'data' => $updatedIncident
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
