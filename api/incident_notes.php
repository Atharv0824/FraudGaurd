<?php
/**
 * API: Add Investigation Note / Timeline Entry
 * POST /api/incident_notes.php
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../config/database.php';

$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

try {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true) ?? $_POST;

    $incidentId = (int)($input['incident_id'] ?? 0);
    $author = trim($input['author'] ?? 'SOC Analyst');
    $note = trim($input['note'] ?? '');
    $actionType = trim($input['action_type'] ?? 'NOTE_ADDED');

    if (!$incidentId || empty($note)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Incident ID and note content are required.']);
        exit;
    }

    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("
        INSERT INTO incident_timeline (incident_id, author, action_type, note, created_at)
        VALUES (:id, :author, :action, :note, :date)
    ");

    $stmt->execute([
        'id' => $incidentId,
        'author' => $author ?: 'SOC Analyst',
        'action' => $actionType,
        'note' => $note,
        'date' => $now
    ]);

    // Update incident updated_at
    $pdo->prepare("UPDATE incidents SET updated_at = :now WHERE id = :id")->execute(['now' => $now, 'id' => $incidentId]);

    echo json_encode([
        'success' => true,
        'message' => 'Investigation note recorded.',
        'data' => [
            'id' => (int)$pdo->lastInsertId(),
            'incident_id' => $incidentId,
            'author' => $author,
            'action_type' => $actionType,
            'note' => $note,
            'created_at' => $now
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
