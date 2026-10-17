<?php
/**
 * FraudGuard - Cybersecurity & Real-Time Fraud Incident Intelligence System
 * Global Application Constants, Indian Fraud Taxonomy & Risk Algorithms
 */

define('APP_NAME', 'FraudGuard');
define('APP_TAGLINE', 'Digital Fraud Detection & Incident Monitoring');
define('APP_VERSION', '2.0.0');
define('APP_TIMEZONE', 'Asia/Kolkata');
date_default_timezone_set(APP_TIMEZONE);

// Official Indian Cybercrime Reporting Reference
define('NATIONAL_CYBER_HELPLINE', '1930');
define('NATIONAL_CYBER_PORTAL', 'cybercrime.gov.in');
define('NATIONAL_CYBER_PORTAL_URL', 'https://cybercrime.gov.in');
define('DEFAULT_CURRENCY', 'INR');
define('DEFAULT_CURRENCY_SYMBOL', '₹');

// Comprehensive Indian Fraud Vector Categories
const FRAUD_CATEGORIES = [
    'upi_fraud' => [
        'label' => 'UPI & QR Code Payment Scam',
        'icon'  => 'bi-qr-code-scan',
        'color' => 'danger',
        'description' => 'Collect requests disguised as refunds, fake QR codes, or money-request fraud'
    ],
    'digital_arrest' => [
        'label' => 'Digital Arrest & Law Enforcement Impersonation',
        'icon'  => 'bi-camera-video',
        'color' => 'danger',
        'description' => 'Fraudsters impersonating CBI, ED, Police, or Customs via video calls'
    ],
    'kyc_phishing' => [
        'label' => 'Fake KYC Update & Disconnection SMS',
        'icon'  => 'bi-chat-left-dots',
        'color' => 'warning',
        'description' => 'Urgent SMS threatening bank block or electricity disconnection'
    ],
    'phishing' => [
        'label' => 'Phishing & Malicious Spoofed Links',
        'icon'  => 'bi-envelope-exclamation',
        'color' => 'warning',
        'description' => 'Fake net banking portals, credential harvesters, and spoofed domains'
    ],
    'account_takeover' => [
        'label' => 'NetBanking Takeover & SIM Swap',
        'icon'  => 'bi-shield-slash',
        'color' => 'danger',
        'description' => 'Unauthorized SIM porting, OTP redirection, and banking account takeover'
    ],
    'fake_trading' => [
        'label' => 'Fake Investment & Trading Scheme',
        'icon'  => 'bi-graph-down-arrow',
        'color' => 'danger',
        'description' => 'Fictitious SEBI tips, bogus stock platforms, and fake crypto gains'
    ],
    'crypto_scam' => [
        'label' => 'Cryptocurrency & Forex App Fraud',
        'icon'  => 'bi-currency-bitcoin',
        'color' => 'warning',
        'description' => 'Ponzi schemes, unauthorized crypto exchanges, and fake forex bots'
    ],
    'job_scam' => [
        'label' => 'Part-Time Job & Telegram Task Scam',
        'icon'  => 'bi-briefcase',
        'color' => 'info',
        'description' => 'YouTube like/subscribe tasks, prepaid ratings, and deposit traps'
    ],
    'fake_helpline' => [
        'label' => 'Fake Customer Care Helpline Number',
        'icon'  => 'bi-telephone-x',
        'color' => 'warning',
        'description' => 'Manipulated search engine phone numbers for banks, airlines, or couriers'
    ],
    'card_fraud' => [
        'label' => 'Debit / Credit Card & POS Fraud',
        'icon'  => 'bi-credit-card-2-front',
        'color' => 'danger',
        'description' => 'Card cloning, international gateway bypass, and automated carding'
    ],
    'wire_fraud' => [
        'label' => 'Corporate RTGS / BEC Wire Transfer',
        'icon'  => 'bi-bank2',
        'color' => 'danger',
        'description' => 'Executive WhatsApp impersonation and falsified vendor invoice routing'
    ],
    'ransomware' => [
        'label' => 'Ransomware & Cyber Extortion',
        'icon'  => 'bi-file-earmark-lock2',
        'color' => 'dark',
        'description' => 'Enterprise data encryption and extortion demands'
    ],
    'ecommerce_scam' => [
        'label' => 'Online Shopping & Delivery Scam',
        'icon'  => 'bi-cart-x',
        'color' => 'secondary',
        'description' => 'Fake parcel delivery SMS, unverified social media merchants, COD scams'
    ],
    'other' => [
        'label' => 'Other Cyber Fraud Anomaly',
        'icon'  => 'bi-question-diamond',
        'color' => 'secondary',
        'description' => 'Emerging cyber fraud tactics or unclassified anomalies'
    ]
];

// Severity Levels
const SEVERITY_LEVELS = [
    'Critical' => [
        'badge' => 'danger',
        'weight' => 4,
        'description' => 'Confirmed massive financial drain or active account compromise'
    ],
    'High' => [
        'badge' => 'warning',
        'weight' => 3,
        'description' => 'High-loss attempt, compromised credentials, or active impersonation'
    ],
    'Medium' => [
        'badge' => 'info',
        'weight' => 2,
        'description' => 'Suspicious transaction pattern or unverified phishing attempt'
    ],
    'Low' => [
        'badge' => 'secondary',
        'weight' => 1,
        'description' => 'Informational report, spam message, or low-exposure inquiry'
    ]
];

// Workflow Incident Statuses
const INCIDENT_STATUSES = [
    'New' => [
        'badge' => 'danger',
        'label' => 'New / Ingested'
    ],
    'Under Investigation' => [
        'badge' => 'primary',
        'label' => 'Under Investigation'
    ],
    'Escalated' => [
        'badge' => 'warning',
        'label' => 'Escalated to Cyber Cell'
    ],
    'Pending Action' => [
        'badge' => 'info',
        'label' => 'Pending Bank Recall'
    ],
    'Resolved' => [
        'badge' => 'success',
        'label' => 'Resolved / Mitigated'
    ],
    'False Positive' => [
        'badge' => 'secondary',
        'label' => 'Verified Legitimate / False Alarm'
    ]
];

/**
 * Calculates a dynamic risk score between 10 and 100 based on Indian fraud indicators
 */
function calculateRiskScore(string $severity, float $amount, string $category): int {
    $base = match(strtolower($severity)) {
        'critical' => 75,
        'high'     => 55,
        'medium'   => 35,
        default    => 15,
    };

    // Amount weight in Indian Rupees
    if ($amount >= 1000000) { // ₹10 Lakh+
        $base += 20;
    } elseif ($amount >= 100000) { // ₹1 Lakh+
        $base += 15;
    } elseif ($amount >= 10000) { // ₹10,000+
        $base += 10;
    } elseif ($amount > 0) {
        $base += 5;
    }

    // High danger vectors in India
    if (in_array($category, ['digital_arrest', 'account_takeover', 'wire_fraud', 'fake_trading', 'ransomware'])) {
        $base += 5;
    }

    return min(100, max(10, $base));
}

/**
 * Generate unique Case Reference ID (Indian standard format)
 */
function generateReferenceId(): string {
    $year = date('Y');
    $rand = strtoupper(bin2hex(random_bytes(3))); // 6 hex chars
    return "FRD-{$year}-{$rand}";
}
