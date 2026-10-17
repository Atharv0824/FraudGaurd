<?php
/**
 * FraudGuard - Login & Analyst Authentication Portal
 * Clean Solid-Color Cybersecurity Product Architecture
 */
require_once __DIR__ . '/auth/auth.php';

// Handle logout
if (isset($_GET['logout'])) {
    logoutUser();
    header('Location: login.php');
    exit;
}

// Already logged in → redirect to SOC Dashboard
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$mode    = $_GET['mode'] ?? 'login';   // 'login' | 'register'
$error   = '';
$success = '';
$msg     = $_GET['msg'] ?? '';

if ($msg === 'auth_required') {
    $error = 'Authentication Required: Please sign in or register to access the FraudGuard Investigation Console.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'login') {
        $result = loginUser($_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($result['success']) {
            $redirect = $_GET['redirect'] ?? 'dashboard.php';
            header('Location: ' . $redirect);
            exit;
        }
        $error = $result['error'];
    } else {
        // Registration
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';

        if (strlen($name) < 2) {
            $error = 'Please enter your full name.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid official or personal email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $result = registerUser($name, $email, $password);
            if ($result['success']) {
                $success = 'Account created successfully! You can now sign in with your credentials.';
                $mode    = 'login';
            } else {
                $error = $result['error'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FraudGuard | <?= $mode === 'login' ? 'Sign In to SOC Console' : 'Create Investigator Account' ?></title>
    
    <!-- Original Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- FraudGuard CSS & Auth Styling -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-body">

    <div class="auth-wrapper">

        <!-- Left Brand Identity Panel -->
        <div class="auth-left">
            <div>
                <div class="mb-4">
                    <a href="index.php">
                        <img src="assets/img/fraudguard-logo-dark.svg" alt="FraudGuard Logo" style="height: 38px; width: auto;">
                    </a>
                </div>

                <h2 class="auth-left-headline">Real-Time Digital Fraud Monitoring & Incident Response</h2>
                <p class="auth-left-sub">
                    Empowering cyber defense teams and law enforcement units across India with real-time stream ingestion, digital IOC correlation, and immediate incident triage.
                </p>

                <div class="auth-feature-list">
                    <div class="auth-feature-item">
                        <div class="auth-feat-icon"><i class="bi bi-broadcast"></i></div>
                        <div>
                            <div class="auth-feat-title">Live Feed Ingestion Stream</div>
                            <div class="auth-feat-desc">Delta polling every 3 seconds for new reports and alerts</div>
                        </div>
                    </div>
                    <div class="auth-feature-item">
                        <div class="auth-feat-icon"><i class="bi bi-shield-check"></i></div>
                        <div>
                            <div class="auth-feat-title">Digital IOC Attribution</div>
                            <div class="auth-feat-desc">Suspect UPI VPAs, spoofed URLs, and mule account tracking</div>
                        </div>
                    </div>
                    <div class="auth-feature-item">
                        <div class="auth-feat-icon"><i class="bi bi-graph-up"></i></div>
                        <div>
                            <div class="auth-feat-title">Threat Exposure Analytics</div>
                            <div class="auth-feat-desc">Financial risk mapping in Indian Rupees (INR) across fraud vectors</div>
                        </div>
                    </div>
                    <div class="auth-feature-item">
                        <div class="auth-feat-icon"><i class="bi bi-lock-fill"></i></div>
                        <div>
                            <div class="auth-feat-title">Strict Audit Trail Logging</div>
                            <div class="auth-feat-desc">Immutable chronological timeline logs for compliance and court records</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="auth-stat-row">
                <div>
                    <div class="auth-stat-val">1930</div>
                    <div class="auth-stat-lbl">National Cyber Helpline</div>
                </div>
                <div>
                    <div class="auth-stat-val">256-Bit</div>
                    <div class="auth-stat-lbl">TLS Encryption</div>
                </div>
                <div>
                    <div class="auth-stat-val">0-Lag</div>
                    <div class="auth-stat-lbl">Real-Time Delta Stream</div>
                </div>
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="auth-right">
            <div class="auth-form-box">

                <!-- Mobile Logo -->
                <div class="d-lg-none mb-4 text-center">
                    <a href="index.php">
                        <img src="assets/img/fraudguard-logo.svg" alt="FraudGuard Logo" style="height: 34px; width: auto;">
                    </a>
                </div>

                <!-- Tab Switcher -->
                <div class="auth-tabs">
                    <a href="login.php?mode=login" class="auth-tab <?= $mode === 'login' ? 'active' : '' ?>">Sign In</a>
                    <a href="login.php?mode=register" class="auth-tab <?= $mode === 'register' ? 'active' : '' ?>">Register</a>
                </div>

                <h4 class="auth-form-title">
                    <?= $mode === 'login' ? 'Sign In to SOC Console' : 'Create Investigator Account' ?>
                </h4>
                <p class="auth-form-sub">
                    <?= $mode === 'login'
                        ? 'Access the live triage console, case dockets, and threat analytics.'
                        : 'Register to manage incident dockets and triage cyber reports.' ?>
                </p>

                <?php if ($error): ?>
                    <div class="auth-alert auth-alert-error">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="auth-alert auth-alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php?mode=<?= $mode ?>" novalidate>

                    <?php if ($mode === 'register'): ?>
                    <div class="auth-field-group">
                        <label class="auth-label">Full Name</label>
                        <input type="text" name="name" class="auth-input" placeholder="e.g. Inspector Priya Nair"
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>
                    <?php endif; ?>

                    <div class="auth-field-group">
                        <label class="auth-label">Official / Personal Email Address</label>
                        <input type="email" name="email" class="auth-input" placeholder="e.g. investigator@fraudguard.in"
                               value="<?= htmlspecialchars($_POST['email'] ?? ($mode === 'login' ? 'investigator@fraudguard.in' : '')) ?>" required autocomplete="email">
                        <?php if ($mode === 'login'): ?>
                            <small class="text-secondary" style="font-size: 0.75rem;">Demo User: <code>investigator@fraudguard.in</code> / <code>User@1234</code></small>
                        <?php endif; ?>
                    </div>

                    <div class="auth-field-group">
                        <label class="auth-label">Password</label>
                        <div class="auth-input-wrap">
                            <input type="password" name="password" id="authPassword" class="auth-input" placeholder="••••••••" value="<?= $mode === 'login' ? 'User@1234' : '' ?>" required>
                            <button type="button" class="auth-eye-btn" onclick="togglePwd('authPassword', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <?php if ($mode === 'register'): ?>
                    <div class="auth-field-group">
                        <label class="auth-label">Confirm Password</label>
                        <div class="auth-input-wrap">
                            <input type="password" name="confirm" id="authConfirm" class="auth-input" placeholder="••••••••" required>
                            <button type="button" class="auth-eye-btn" onclick="togglePwd('authConfirm', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <button type="submit" class="auth-submit-btn mt-3">
                        <?php if ($mode === 'login'): ?>
                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In to Console
                        <?php else: ?>
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        <?php endif; ?>
                    </button>

                </form>

                <!-- Dedicated Admin Portal Link Card -->
                <div class="mt-4 p-3 rounded" style="background-color: #102331; border: 1px solid #1E3B50;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-bold text-warning small"><i class="bi bi-shield-lock-fill me-1"></i>SOC Supervisor / Admin?</div>
                            <div class="text-secondary" style="font-size: 0.76rem; color: #8FA7B8 !important;">Restricted Level 3 command portal</div>
                        </div>
                        <a href="admin-login.php" class="btn btn-sm btn-outline-warning fw-semibold px-3 py-1" style="font-size: 0.78rem;">
                            Admin Login &rarr;
                        </a>
                    </div>
                </div>

                <div class="auth-divider"><span>or public access</span></div>

                <a href="index.php" class="auth-guest-btn">
                    <i class="bi bi-shield-exclamation me-2 text-danger"></i>Report Fraud as Citizen (No Login Needed)
                </a>

                <div class="auth-footer-note">
                    <?php if ($mode === 'login'): ?>
                        Need investigator access? <a href="login.php?mode=register">Create account</a>
                    <?php else: ?>
                        Already have an account? <a href="login.php?mode=login">Sign in</a>
                    <?php endif; ?>
                </div>

                <div class="auth-security-badges">
                    <span><i class="bi bi-lock-fill"></i> TLS 256-Bit</span>
                    <span><i class="bi bi-shield-check"></i> Indian Cyber Grid</span>
                    <span><i class="bi bi-eye-slash"></i> Non-Custodial</span>
                </div>

            </div>
        </div>

    </div>

    <script>
    function togglePwd(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }
    </script>
</body>
</html>
