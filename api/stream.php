<?php
/**
 * API: Real-Time Incident Stream & Delta Monitor
 * GET /api/stream.php?last_id=12&since=2026-10-14+12:00:00
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

$pdo = getDB();

$lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
$since = trim($_GET['since'] ?? '');

try {
    $now = date('Y-m-d H:i:s');

    // Query incidents newer than lastId
    $stmt = $pdo->prepare("
        SELECT id, reference_id, title, category, severity, status, 
               amount_lost, currency, incident_date, reporter_name, 
               is_anonymous, suspect_details, assigned_investigator, 
               risk_score, ip_address, created_at
        FROM incidents
        WHERE id > :last_id
        ORDER BY id DESC
        LIMIT 20
    ");
    $stmt->execute(['last_id' => $lastId]);
    $newIncidents = $stmt->fetchAll();

    $hasCritical = false;
    foreach ($newIncidents as &$inc) {
        $catKey = $inc['category'];
        $inc['category_meta'] = FRAUD_CATEGORIES[$catKey] ?? ['label' => ucfirst($catKey), 'icon' => 'bi-shield', 'color' => 'secondary'];
        $inc['severity_meta'] = SEVERITY_LEVELS[$inc['severity']] ?? ['badge' => 'secondary', 'weight' => 1];
        $inc['status_meta'] = INCIDENT_STATUSES[$inc['status']] ?? ['badge' => 'secondary', 'label' => $inc['status']];

        if (in_array($inc['severity'], ['Critical', 'High'])) {
            $hasCritical = true;
        }
    }

    // High level quick counts for realtime badge sync
    $totals = $pdo->query("
        SELECT 
            COUNT(*) as total,
            MAX(id) as max_id,
            SUM(CASE WHEN status = 'New' THEN 1 ELSE 0 END) as new_count,
            SUM(CASE WHEN severity = 'Critical' AND status != 'Resolved' THEN 1 ELSE 0 END) as critical_count
        FROM incidents
    ")->fetch();

    echo json_encode([
        'success' => true,
        'server_time' => $now,
        'max_id' => (int)($totals['max_id'] ?? 0),
        'new_incidents_count' => count($newIncidents),
        'has_critical' => $hasCritical,
        'new_incidents' => $newIncidents,
        'live_stats' => [
            'total' => (int)$totals['total'],
            'new_pending' => (int)$totals['new_count'],
            'critical_active' => (int)$totals['critical_count']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
