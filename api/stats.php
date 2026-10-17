<?php
/**
 * API: Dashboard Aggregated Statistics & Analytics
 * GET /api/stats.php
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

$pdo = getDB();

try {
    // Total count & financial exposure
    $summary = $pdo->query("
        SELECT 
            COUNT(*) as total_incidents,
            COALESCE(SUM(amount_lost), 0) as total_loss,
            COALESCE(AVG(risk_score), 0) as avg_risk_score,
            SUM(CASE WHEN status IN ('New', 'Under Investigation', 'Escalated', 'Pending Action') THEN 1 ELSE 0 END) as active_incidents,
            SUM(CASE WHEN status = 'New' THEN 1 ELSE 0 END) as new_incidents,
            SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved_incidents,
            SUM(CASE WHEN status = 'False Positive' THEN 1 ELSE 0 END) as false_positives,
            SUM(CASE WHEN severity = 'Critical' AND status != 'Resolved' THEN 1 ELSE 0 END) as critical_active
        FROM incidents
    ")->fetch();

    // Category distribution
    $catRows = $pdo->query("
        SELECT category, COUNT(*) as count, COALESCE(SUM(amount_lost), 0) as total_loss
        FROM incidents
        GROUP BY category
        ORDER BY count DESC
    ")->fetchAll();

    $categoriesData = [];
    foreach ($catRows as $r) {
        $meta = FRAUD_CATEGORIES[$r['category']] ?? ['label' => ucfirst($r['category']), 'color' => 'secondary'];
        $categoriesData[] = [
            'category' => $r['category'],
            'label' => $meta['label'],
            'count' => (int)$r['count'],
            'loss' => (float)$r['total_loss']
        ];
    }

    // Severity distribution
    $sevRows = $pdo->query("
        SELECT severity, COUNT(*) as count 
        FROM incidents 
        GROUP BY severity
    ")->fetchAll();

    $severitiesData = [
        'Critical' => 0,
        'High' => 0,
        'Medium' => 0,
        'Low' => 0
    ];
    foreach ($sevRows as $r) {
        if (isset($severitiesData[$r['severity']])) {
            $severitiesData[$r['severity']] = (int)$r['count'];
        }
    }

    // Status distribution
    $statRows = $pdo->query("
        SELECT status, COUNT(*) as count 
        FROM incidents 
        GROUP BY status
    ")->fetchAll();

    $statusesData = [];
    foreach ($statRows as $r) {
        $statusesData[$r['status']] = (int)$r['count'];
    }

    // 7-Day Timeline trend
    $timeline = [];
    for ($i = 6; $i >= 0; $i--) {
        $dayDate = date('Y-m-d', strtotime("-{$i} days"));
        $dayLabel = date('M d', strtotime("-{$i} days"));
        $stmt = $pdo->prepare("SELECT COUNT(*) as count, COALESCE(SUM(amount_lost), 0) as loss FROM incidents WHERE DATE(created_at) = :d");
        $stmt->execute(['d' => $dayDate]);
        $row = $stmt->fetch();
        $timeline[] = [
            'date' => $dayDate,
            'label' => $dayLabel,
            'count' => (int)($row['count'] ?? 0),
            'loss' => (float)($row['loss'] ?? 0)
        ];
    }

    // Latest 5 high priority incidents
    $latestHigh = $pdo->query("
        SELECT id, reference_id, title, category, severity, status, amount_lost, risk_score, created_at
        FROM incidents
        WHERE severity IN ('Critical', 'High')
        ORDER BY created_at DESC
        LIMIT 5
    ")->fetchAll();

    echo json_encode([
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'summary' => [
            'total_incidents' => (int)$summary['total_incidents'],
            'total_loss' => (float)$summary['total_loss'],
            'avg_risk_score' => round((float)$summary['avg_risk_score'], 1),
            'active_incidents' => (int)$summary['active_incidents'],
            'new_incidents' => (int)$summary['new_incidents'],
            'resolved_incidents' => (int)$summary['resolved_incidents'],
            'false_positives' => (int)$summary['false_positives'],
            'critical_active' => (int)$summary['critical_active']
        ],
        'charts' => [
            'categories' => $categoriesData,
            'severities' => $severitiesData,
            'statuses' => $statusesData,
            'timeline' => $timeline
        ],
        'recent_critical' => $latestHigh
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
