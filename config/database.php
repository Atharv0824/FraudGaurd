<?php
/**
 * FraudGuard - Database Connection Handler
 * Supports SQLite (Default out-of-the-box zero-configuration) and MySQL.
 */

// Choose 'sqlite' or 'mysql' (Supports Environment variables for Cloud Hosting)
define('DB_DRIVER', getenv('DB_DRIVER') ?: 'sqlite');

// MySQL Credentials (used if DB_DRIVER is 'mysql')
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));
define('DB_NAME', getenv('DB_NAME') ?: 'fraudguard');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

// SQLite Database Path
define('DB_SQLITE_PATH', __DIR__ . '/../database/fraud_sentinel.sqlite');

/**
 * Get the PDO Database Instance
 * @return PDO
 */
function getDB(): PDO {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    try {
        if (DB_DRIVER === 'sqlite') {
            $dbDir = dirname(DB_SQLITE_PATH);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0777, true);
            }

            $pdo = new PDO('sqlite:' . DB_SQLITE_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec('PRAGMA foreign_keys = ON;');
            $pdo->exec('PRAGMA journal_mode = WAL;');

            // Initialize schema if newly created or table missing
            initializeSchemaIfNeeded($pdo);

        } else {
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return $pdo;
    } catch (PDOException $e) {
        // Return clear JSON if accessed via API
        if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
            header('Content-Type: application/json', true, 500);
            echo json_encode([
                'success' => false,
                'error'   => 'Database connection failed: ' . $e->getMessage()
            ]);
            exit;
        }

        // Friendly error display for web page
        die('<div style="font-family:sans-serif;padding:2rem;background:#fee2e2;color:#991b1b;border:1px solid #f87171;border-radius:8px;max-width:600px;margin:3rem auto;">
            <h3 style="margin-top:0;">Database Connection Error</h3>
            <p>' . htmlspecialchars($e->getMessage()) . '</p>
            <p><small>Ensure write permissions for SQLite or correct credentials for MySQL in <code>config/database.php</code>.</small></p>
        </div>');
    }
}

/**
 * Initializes SQLite schema tables and indices if not already present
 */
function initializeSchemaIfNeeded(PDO $pdo): void {
    // Check if incidents table exists
    $check = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='incidents'")->fetch();
    if ($check) {
        return; // Schema already created
    }

    $schemaSql = <<<SQL
    CREATE TABLE IF NOT EXISTS incidents (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        reference_id VARCHAR(32) NOT NULL UNIQUE,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(64) NOT NULL,
        severity VARCHAR(32) NOT NULL DEFAULT 'Medium',
        status VARCHAR(32) NOT NULL DEFAULT 'New',
        amount_lost DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
        currency VARCHAR(10) NOT NULL DEFAULT 'INR',
        incident_date DATETIME NOT NULL,
        reporter_name VARCHAR(128) NULL,
        reporter_email VARCHAR(160) NULL,
        reporter_phone VARCHAR(64) NULL,
        is_anonymous INTEGER NOT NULL DEFAULT 0,
        suspect_details TEXT NULL,
        description TEXT NOT NULL,
        assigned_investigator VARCHAR(128) NOT NULL DEFAULT 'Unassigned',
        risk_score INTEGER NOT NULL DEFAULT 50,
        ip_address VARCHAR(45) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS incident_timeline (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        incident_id INTEGER NOT NULL,
        author VARCHAR(128) NOT NULL,
        action_type VARCHAR(64) NOT NULL,
        note TEXT NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE
    );

    CREATE INDEX IF NOT EXISTS idx_incidents_ref ON incidents(reference_id);
    CREATE INDEX IF NOT EXISTS idx_incidents_status ON incidents(status);
    CREATE INDEX IF NOT EXISTS idx_incidents_severity ON incidents(severity);
    CREATE INDEX IF NOT EXISTS idx_incidents_created ON incidents(created_at);
    CREATE INDEX IF NOT EXISTS idx_timeline_incident ON incident_timeline(incident_id);
SQL;

    $pdo->exec($schemaSql);

    // Auto-seed initial demo incidents
    seedInitialDemoData($pdo);
}

/**
 * Seed initial realistic Indian cybersecurity incidents
 */
function seedInitialDemoData(PDO $pdo): void {
    $now = date('Y-m-d H:i:s');
    $yesterday = date('Y-m-d H:i:s', strtotime('-1 day'));
    $twoDaysAgo = date('Y-m-d H:i:s', strtotime('-2 days'));
    $threeDaysAgo = date('Y-m-d H:i:s', strtotime('-3 days'));
    $fourDaysAgo = date('Y-m-d H:i:s', strtotime('-4 days'));
    $fiveDaysAgo = date('Y-m-d H:i:s', strtotime('-5 days'));

    $samples = [
        [
            'ref' => 'FRD-2026-A83F12',
            'title' => 'Managing Director Impersonation via WhatsApp — Emergency RTGS Transfer',
            'cat' => 'wire_fraud',
            'sev' => 'Critical',
            'status' => 'Under Investigation',
            'amount' => 7200000.00,
            'curr' => 'INR',
            'date' => $now,
            'name' => 'Rajesh Kulkarni (Finance Head, InfraVision)',
            'email' => 'rajesh.k@infravision.co.in',
            'phone' => '+91 98201 44823',
            'anon' => 0,
            'suspect' => "Spoofed WhatsApp No: +91 88001 09932 (MD's display image)\nBeneficiary A/C: 5092817364, IFSC: HDFC0001234, Kotak Mahindra Bank Mumbai\nFake Approval Portal: infravision-approval-portal.in",
            'desc' => 'Attacker impersonated company MD on WhatsApp using executive display photo and instructed accounts manager to urgently transfer ₹72,00,000 for a time-sensitive government tender deposit. Bank recall initiated via 1930 portal; beneficiary account currently frozen.',
            'inv' => 'Inspector Priya Nair (Cyber Crime Cell, Mumbai)',
            'score' => 95,
            'ip' => '185.220.101.5'
        ],
        [
            'ref' => 'FRD-2026-C71E90',
            'title' => 'Corporate NetBanking Takeover via Franchisee SIM-Swap Fraud',
            'cat' => 'account_takeover',
            'sev' => 'High',
            'status' => 'Escalated',
            'amount' => 1050000.00,
            'curr' => 'INR',
            'date' => $yesterday,
            'name' => 'IT Security Team, Bharat Finserv Ltd',
            'email' => 'infosec@bharatfinserv.in',
            'phone' => '+91 80471 33921',
            'anon' => 0,
            'suspect' => "Attacker Proxy IP: 45.154.255.89 (Tor Node)\nSIM Swap Circle: Pune East, Maharashtra Circle\nBeneficiary UPI: pay9948@axisbank\nTarget NetBanking ID: corp_admin_99482",
            'desc' => 'Perpetrators executed unauthorized SIM swap on the authorized financial signatory\'s mobile connection via fraudulent physical Aadhaar copy at a third-party kiosk. OTPs intercepted, resulting in ₹10,50,000 debited across 3 rapid IMPS transfers within 12 minutes.',
            'inv' => 'ACP Arun Sharma (Cyber Crime Wing, Pune)',
            'score' => 88,
            'ip' => '45.154.255.89'
        ],
        [
            'ref' => 'FRD-2026-D44B56',
            'title' => 'Fake SBI KYC Expiry SMS with Credential Harvester Link',
            'cat' => 'kyc_phishing',
            'sev' => 'Medium',
            'status' => 'New',
            'amount' => 85000.00,
            'curr' => 'INR',
            'date' => $yesterday,
            'name' => 'Anonymous Citizen',
            'email' => 'victim_blr@gmail.com',
            'phone' => '+91 94490 71144',
            'anon' => 1,
            'suspect' => "Phishing URL: https://sbi-kyc-update-secure.xyz\nSMS Header: VM-SBIBNK (Spoofed Sender ID)\nHost: Hostinger Netherlands, Cloudflare protected\nDebit UTR: 412093847120",
            'desc' => 'Victim received SMS claiming "SBI Account will be blocked today due to pending KYC update. Click here to verify." Victim entered NetBanking credentials and transaction OTP. ₹85,000 debited through UPI collect. Reported to 1930 National Cyber Crime Helpline.',
            'inv' => 'Unassigned',
            'score' => 60,
            'ip' => '104.28.210.14'
        ],
        [
            'ref' => 'FRD-2026-F19A03',
            'title' => 'Bogus Institutional Stock Trading Scheme & Sideloaded APK Scam',
            'cat' => 'fake_trading',
            'sev' => 'Critical',
            'status' => 'Under Investigation',
            'amount' => 12500000.00,
            'curr' => 'INR',
            'date' => $twoDaysAgo,
            'name' => 'Kishore Venkatraman (Retired Govt Officer)',
            'email' => 'kishore.venkat@outlook.com',
            'phone' => '+91 98401 23091',
            'anon' => 0,
            'suspect' => "Telegram Channel: 'Institutional NSE Wealth Signals'\nSideloaded Android APK: StockPro-SEBI-VIP.apk\nMule Current A/C: 98120021000918, ICICI Bank, Delhi Chandni Chowk\nUSDT TRC20 Wallet: TXqB78mKq91Z...x2Np",
            'desc' => 'Victim was invited into a WhatsApp group claiming SEBI institutional allotment privileges. Directed to download an unverified APK replicating a real trading platform. Transferred retirement corpus of ₹1.25 Crore over 3 months. When requesting withdrawal, scammers demanded ₹22 Lakh advance tax. Complaint filed on cybercrime.gov.in.',
            'inv' => 'Cyber Cell South Zone, New Delhi',
            'score' => 98,
            'ip' => '103.145.12.90'
        ],
        [
            'ref' => 'FRD-2026-H82K19',
            'title' => 'Digital Arrest Scam Impersonating Mumbai Police & CBI Officers',
            'cat' => 'digital_arrest',
            'sev' => 'Critical',
            'status' => 'Escalated',
            'amount' => 450000.00,
            'curr' => 'INR',
            'date' => $threeDaysAgo,
            'name' => 'Sunita Aggarwal',
            'email' => 'sunita.aggarwal74@gmail.com',
            'phone' => '+91 98110 55621',
            'anon' => 0,
            'suspect' => "Skype Handle: 'cbi_hq_investigation_unit2'\nImpersonated Officer: Fake ID card of DCP Balsing Rajput\nBeneficiary A/C: Canara Bank, IFSC CNRB0002194\nThreat Call No: +91 79901 88210",
            'desc' => 'Victim received an automated IVR call alleging a parcel seized at Mumbai International Airport with illegal contraband and counterfeit passports. Kept on continuous Skype video call for 18 hours ("Digital Arrest") by men in police uniform. Coerced into transferring ₹4.50 Lakh to a "Reserve Bank Verification Escrow" account.',
            'inv' => 'Inspector Priya Nair (Cyber Crime Cell, Mumbai)',
            'score' => 96,
            'ip' => '178.62.204.88'
        ],
        [
            'ref' => 'FRD-2026-B92E44',
            'title' => 'UPI QR Code "Scan to Receive Money" Fraud on OLX Classifieds',
            'cat' => 'upi_fraud',
            'sev' => 'Medium',
            'status' => 'Resolved',
            'amount' => 38000.00,
            'curr' => 'INR',
            'date' => $fourDaysAgo,
            'name' => 'Aman Verma',
            'email' => 'aman.verma.tech@gmail.com',
            'phone' => '+91 97200 48819',
            'anon' => 0,
            'suspect' => "Suspect Phone: +91 76002 91823\nFraudulent UPI VPA: merchant-refund-desk@paytm\nFake Military ID: Naik Ramesh Kumar (Army badge spoof)",
            'desc' => 'Seller listed household furniture on OLX. Buyer claiming to be an Indian Army personnel requested to pay advance via UPI. Sent a QR code claiming "Scan this QR code to receive ₹38,000 into your account". Scanning and entering UPI PIN resulted in money deduction instead of deposit. Bank alerted and chargeback approved.',
            'inv' => 'ACP Arun Sharma (Cyber Crime Wing, Pune)',
            'score' => 52,
            'ip' => '194.26.29.112'
        ],
        [
            'ref' => 'FRD-2026-M77P91',
            'title' => 'Part-Time "YouTube Video Liking" Telegram Task Scam',
            'cat' => 'job_scam',
            'sev' => 'High',
            'status' => 'Under Investigation',
            'amount' => 210000.00,
            'curr' => 'INR',
            'date' => $fiveDaysAgo,
            'name' => 'Deepika Joshi',
            'email' => 'deepika.j@yahoo.co.in',
            'phone' => '+91 99100 87234',
            'anon' => 0,
            'suspect' => "Telegram Recruiter: @Priya_HR_GlobalMedia\nTask Dashboard: http://media-boost-tasks-in.top\nMule UPI ID: 9918273645@ybl\nBeneficiary Bank: Bandhan Bank, Kolkata",
            'desc' => 'Victim contacted on WhatsApp offering work-from-home earning ₹3,000–₹8,000/day by liking YouTube videos. Initially paid ₹150 for 3 tasks to build trust. Then coerced into "prepaid crypto rating tasks" requiring deposits. Deposited ₹2.10 Lakh in tiers before scammers ceased communication.',
            'inv' => 'Cyber Crime PS, Hyderabad',
            'score' => 82,
            'ip' => '104.21.32.18'
        ]
    ];

    $stmt = $pdo->prepare("
        INSERT INTO incidents (
            reference_id, title, category, severity, status, amount_lost, currency,
            incident_date, reporter_name, reporter_email, reporter_phone, is_anonymous,
            suspect_details, description, assigned_investigator, risk_score, ip_address, created_at, updated_at
        ) VALUES (
            :ref, :title, :cat, :sev, :status, :amount, :curr,
            :date, :name, :email, :phone, :anon,
            :suspect, :desc, :inv, :score, :ip, :date, :date
        )
    ");

    $noteStmt = $pdo->prepare("
        INSERT INTO incident_timeline (incident_id, author, action_type, note, created_at)
        VALUES (:id, :author, :action, :note, :date)
    ");

    foreach ($samples as $s) {
        $stmt->execute($s);
        $incidentId = $pdo->lastInsertId();

        // Add initial timeline note
        $noteStmt->execute([
            'id' => $incidentId,
            'author' => 'FraudGuard Intake Engine',
            'action' => 'CREATED',
            'note' => "Incident reported and ingested into FraudGuard Defense Grid. Initial Risk Score calculated at {$s['score']}/100.",
            'date' => $s['date']
        ]);

        if ($s['status'] !== 'New') {
            $noteStmt->execute([
                'id' => $incidentId,
                'author' => $s['inv'] !== 'Unassigned' ? $s['inv'] : 'SOC Triage Specialist',
                'action' => 'STATUS_CHANGE',
                'note' => "Case triaged and transitioned to '{$s['status']}'. Lead officer assigned: {$s['inv']}.",
                'date' => date('Y-m-d H:i:s', strtotime($s['date'] . ' +1 hour'))
            ]);
        }
    }
}
