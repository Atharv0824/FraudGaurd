<?php
/**
 * FraudGuard - Database Seed & Verification Script
 * Can be run via CLI: php database/seed.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

echo "=== FraudGuard Database Setup ===\n";
$pdo = getDB();

$count = $pdo->query("SELECT count(*) FROM incidents")->fetchColumn();
echo "Status: Connected to Database successfully!\n";
echo "Driver: " . DB_DRIVER . "\n";
echo "Total Incidents currently in DB: " . $count . "\n";

$rows = $pdo->query("SELECT reference_id, title, category, severity, status, amount_lost FROM incidents LIMIT 5")->fetchAll();
foreach ($rows as $r) {
    echo sprintf(" - [%s] [%s] %s (₹%s) - %s\n", 
        $r['reference_id'], 
        $r['severity'], 
        substr($r['title'], 0, 35), 
        number_format($r['amount_lost'], 2),
        $r['status']
    );
}
echo "==========================================\n";
