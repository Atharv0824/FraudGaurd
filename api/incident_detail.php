<?php
/**
 * API: Single Incident Detail & Timeline
 * GET /api/incident_detail.php?id=1 or ?ref=FRD-2026-XXXX
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

$pdo = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$ref = trim($_GET['ref'] ?? '');

if (!$id && empty($ref)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please provide an incident id or reference_id.']);
    exit;
}

try {
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM incidents WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM incidents WHERE reference_id = :ref");
        $stmt->execute(['ref' => $ref]);
    }

    $incident = $stmt->fetch();

    if (!$incident) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Incident not found.']);
        exit;
    }

    // Attach metadata
    $catKey = $incident['category'];
    $incident['category_meta'] = FRAUD_CATEGORIES[$catKey] ?? ['label' => ucfirst($catKey), 'icon' => 'bi-shield', 'color' => 'secondary'];
    $incident['severity_meta'] = SEVERITY_LEVELS[$incident['severity']] ?? ['badge' => 'secondary', 'weight' => 1];
    $incident['status_meta'] = INCIDENT_STATUSES[$incident['status']] ?? ['badge' => 'secondary', 'label' => $incident['status']];

    // Fetch timeline
    $tlStmt = $pdo->prepare("
        SELECT id, author, action_type, note, created_at 
        FROM incident_timeline 
        WHERE incident_id = :inc_id 
        ORDER BY created_at ASC, id ASC
    ");
    $tlStmt->execute(['inc_id' => $incident['id']]);
    $incident['timeline'] = $tlStmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $incident
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
