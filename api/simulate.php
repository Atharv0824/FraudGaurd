<?php
/**
 * API: Simulate Incoming Live Digital Fraud Incident
 * POST /api/simulate.php
 * Injects realistic Indian cyber fraud incidents for live SOC demonstrations and audio alarm tests.
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

$pdo = getDB();

$templates = [
    [
        'title' => 'Digital Arrest Threat Call Impersonating CBI & Mumbai Customs',
        'category' => 'digital_arrest',
        'severity' => 'Critical',
        'amount' => 680000.00,
        'name' => 'Meera Srinivasan (Bengaluru)',
        'email' => 'meera.srinivasan@techconsult.in',
        'phone' => '+91 98450 19283',
        'suspect' => "Skype ID: 'cbi_investigation_desk_hq'\nFake Officer: Sub-Inspector Rakesh Sharma (Spoofed Police ID)\nBeneficiary Bank: Yes Bank Current A/C: 009281726354\nCaller Line: +91 79001 22941",
        'description' => 'Victim received an automated IVR call stating a courier parcel addressed to them contained expired passports and MDMA contraband. Fraudsters forced continuous video call ("Digital Arrest") and coerced a ₹6.80 Lakh security deposit to an alleged RBI verification account.',
        'ip' => '185.220.101.42'
    ],
    [
        'title' => 'UPI Request-Money Trap Disguised as Army Officer Advance on OLX',
        'category' => 'upi_fraud',
        'severity' => 'High',
        'amount' => 45000.00,
        'name' => 'Kunal Deshmukh',
        'email' => 'kunal.deshmukh92@gmail.com',
        'phone' => '+91 97630 84920',
        'suspect' => "Attacker Phone: +91 88002 91820\nSpoofed Military ID: Subedar Ramesh Verma (Canteen Card Spoof)\nUPI VPA: armedforces.canteen@okhdfcbank\nTarget UPI App: Google Pay",
        'description' => 'Scammer posing as an Indian Army officer buying furniture on OLX sent a QR code stating "Scan to receive advance payment". Scanning the QR and entering UPI PIN caused a ₹45,000 debit. Victim reported within 20 minutes to National Helpline 1930.',
        'ip' => '103.21.244.60'
    ],
    [
        'title' => 'Electricity Bill Disconnection Threat SMS with AnyDesk Remote Access',
        'category' => 'kyc_phishing',
        'severity' => 'High',
        'amount' => 120000.00,
        'name' => 'Harish Chandra Verma (Lucknow)',
        'email' => 'harish.verma@bsnl.in',
        'phone' => '+91 94150 33912',
        'suspect' => "Phishing SMS Sender: BZ-UPPCL\nFraud Helpline: +91 82001 99281\nRemote Access APK: QuickSupport-Discom.apk\nBeneficiary UPI: billexecutive@ybl",
        'description' => 'Victim received SMS: "Dear consumer, your electricity will be disconnected tonight at 9:30 PM due to unpaid previous bill. Call official helpline +91 82001... immediately." Fake operator instructed victim to install remote assistance app and process a ₹10 test recharge, capturing NetBanking credentials.',
        'ip' => '45.154.255.19'
    ],
    [
        'title' => 'CEO WhatsApp Spoofing & Urgent RTGS Wire Transfer Request',
        'category' => 'wire_fraud',
        'severity' => 'Critical',
        'amount' => 4850000.00,
        'name' => 'Finance Controller (Sunil Nair)',
        'email' => 'sunil.nair@aerotech-systems.co.in',
        'phone' => '+91 98200 77124',
        'suspect' => "WhatsApp Sender: +91 96001 44021 (Display Name: MD Alok Singhal)\nTarget Bank: IndusInd Bank Current A/C 200192847162\nBeneficiary Name: Matrix Horizon Tech Trading LLP\nApproval Memo: Signed MD approval PDF (Forged)",
        'description' => 'Finance team received urgent WhatsApp messages purportedly from Managing Director directing an emergency RTGS vendor payment of ₹48.5 Lakh for an ongoing confidential defense acquisition. The dual authorization protocol flagged an unfamiliar beneficiary account.',
        'ip' => '195.123.244.77'
    ],
    [
        'title' => 'Fake SEBI Institutional Stock App with Fictitious Upper Circuit Gains',
        'category' => 'fake_trading',
        'severity' => 'Critical',
        'amount' => 2800000.00,
        'name' => 'Vikram Sethi (Chandigarh)',
        'email' => 'vikram.sethi66@gmail.com',
        'phone' => '+91 98880 12450',
        'suspect' => "WhatsApp Group: 'NSE Institutional Bull Club #04'\nAPK Sideload: BharatTradeVIP.apk (Package: org.in.trade.vip)\nMule Current A/C: Bandhan Bank, Account No: 101900284716\nTelegram Handler: @Prof_Anand_SEBI_Advisory",
        'description' => 'Victim was added to an unverified WhatsApp group providing stock market tips. Scammers directed victim to install a customized trading APK showing fake 400% returns on unlisted shares. Victim deposited ₹28 Lakh across 4 transactions. When requesting withdrawal, 25% tax deposit was demanded.',
        'ip' => '104.28.210.88'
    ]
];

try {
    // Pick random template
    $tpl = $templates[array_rand($templates)];

    $ref = generateReferenceId();
    $now = date('Y-m-d H:i:s');
    $riskScore = calculateRiskScore($tpl['severity'], $tpl['amount'], $tpl['category']);

    $stmt = $pdo->prepare("
        INSERT INTO incidents (
            reference_id, title, category, severity, status, amount_lost, currency,
            incident_date, reporter_name, reporter_email, reporter_phone, is_anonymous,
            suspect_details, description, assigned_investigator, risk_score, ip_address, created_at, updated_at
        ) VALUES (
            :ref, :title, :cat, :sev, 'New', :amount, 'INR',
            :date, :name, :email, :phone, 0,
            :suspect, :desc, 'Unassigned', :score, :ip, :created, :updated
        )
    ");

    $stmt->execute([
        'ref' => $ref,
        'title' => $tpl['title'],
        'cat' => $tpl['category'],
        'sev' => $tpl['severity'],
        'amount' => $tpl['amount'],
        'date' => $now,
        'name' => $tpl['name'],
        'email' => $tpl['email'],
        'phone' => $tpl['phone'],
        'suspect' => $tpl['suspect'],
        'desc' => $tpl['description'],
        'score' => $riskScore,
        'ip' => $tpl['ip'],
        'created' => $now,
        'updated' => $now
    ]);

    $id = $pdo->lastInsertId();

    // Timeline event
    $noteStmt = $pdo->prepare("
        INSERT INTO incident_timeline (incident_id, author, action_type, note, created_at)
        VALUES (:id, 'Simulated Attack Sensor', 'CREATED', :note, :date)
    ");
    $noteStmt->execute([
        'id' => $id,
        'note' => "Simulated incident injected into FraudGuard Defense Grid. Threat Vector: {$tpl['category']}. Severity: {$tpl['severity']}. Initial Risk Score: {$riskScore}/100.",
        'date' => $now
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Simulated Indian cyber fraud incident injected successfully!',
        'incident' => [
            'id' => (int)$id,
            'reference_id' => $ref,
            'title' => $tpl['title'],
            'category' => $tpl['category'],
            'severity' => $tpl['severity'],
            'amount_lost' => $tpl['amount'],
            'currency' => 'INR',
            'risk_score' => $riskScore,
            'created_at' => $now
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
