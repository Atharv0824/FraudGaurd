<?php
/**
 * FraudGuard - Dedicated Administrator & SOC Command Portal
 * Restricted Level 3 Clearance Authentication
 */
require_once __DIR__ . '/auth/auth.php';

// If already logged in as admin, redirect to SOC Console
if (!empty($_SESSION['user_id']) && ($_SESSION['user']['role'] ?? '') === 'admin') {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$msg = $_GET['msg'] ?? '';

if ($msg === 'admin_required') {
    $error = 'Administrator privileges required to access that section.';
} elseif ($msg === 'access_denied') {
    $error = 'Access denied: Your account does not have administrator clearance.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        $error = 'Please provide both Administrator ID and Security Passkey.';
    } else {
        $result = loginUser($identifier, $password, 'admin');
        if ($result['success']) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FraudGuard | SOC Administrator Authentication Portal</title>
    
    <!-- Original Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- FraudGuard Styling -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">

    <style>
        .admin-auth-body {
            background-color: #0E1E2A;
            color: #EBF0F3;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .admin-card {
            width: 100%;
            max-width: 480px;
            background-color: #142B3B;
            border: 1px solid #23465F;
            border-radius: 14px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.4);
            padding: 2.75rem 2.25rem;
            position: relative;
            overflow: hidden;
        }
        .admin-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #D99027, #C74646, #2E7D62);
        }
        .admin-badge-top {
            background-color: rgba(217, 144, 39, 0.15);
            border: 1px solid rgba(217, 144, 39, 0.4);
            color: #F8B446;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 1.25rem;
        }
        .admin-input {
            background-color: #0E1E2A !important;
            border: 1px solid #2A4F6B !important;
            color: #FFFFFF !important;
            border-radius: 6px;
            padding: 0.7rem 0.9rem;
            font-size: 0.92rem;
            width: 100%;
        }
        .admin-input:focus {
            border-color: #2E7D62 !important;
            box-shadow: 0 0 0 3px rgba(46, 125, 98, 0.25) !important;
            outline: none;
        }
        .admin-btn {
            background-color: #2E7D62;
            color: #FFFFFF;
            border: 1px solid #2E7D62;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-weight: 700;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.18s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-btn:hover {
            background-color: #256650;
            border-color: #256650;
            color: #FFFFFF;
        }
        .demo-cred-box {
            background-color: #0B1720;
            border: 1px dashed #2A4F6B;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            color: #9AB0BE;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body class="admin-auth-body">

    <div class="admin-card">
        <!-- Logo -->
        <div class="text-center mb-3">
            <a href="index.php">
                <img src="assets/img/fraudguard-logo-dark.svg" alt="FraudGuard Logo" style="height: 42px; width: auto;">
            </a>
        </div>

        <div class="text-center mb-4">
            <div class="admin-badge-top">
                <i class="bi bi-shield-lock-fill text-warning"></i>
                <span>Restricted SOC Administrator Portal</span>
            </div>
            <h4 class="fw-bold text-white mb-1">Security Command Sign-In</h4>
            <p class="small text-secondary mb-0" style="color: #9AB0BE !important;">
                Authorized Law Enforcement & SOC Lead Personnel Only
            </p>
        </div>

        <?php if ($error): ?>
            <div class="p-3 mb-3 rounded small" style="background-color: rgba(199, 70, 70, 0.2); border: 1px solid #C74646; color: #FCA5A5;">
                <i class="bi bi-exclamation-octagon-fill me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin-login.php" novalidate>
            <div class="mb-3">
                <label class="form-label small fw-bold text-white mb-1">Admin ID or Official Email</label>
                <div class="input-group">
                    <span class="input-group-text border-0" style="background-color: #1A364A; color: #8E9EA8;"><i class="bi bi-person-badge"></i></span>
                    <input type="text" name="identifier" class="admin-input font-monospace" placeholder="e.g. ADMIN-SOC-01 or admin@fraudguard.in" 
                           value="<?= htmlspecialchars($_POST['identifier'] ?? 'ADMIN-SOC-01') ?>" required autocomplete="username">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-white mb-1">Security Passkey / Password</label>
                <div class="input-group">
                    <span class="input-group-text border-0" style="background-color: #1A364A; color: #8E9EA8;"><i class="bi bi-key-fill"></i></span>
                    <input type="password" name="password" id="adminPwd" class="admin-input" placeholder="••••••••••••" 
                           value="Admin@FraudGuard#2026" required autocomplete="current-password">
                    <button type="button" class="btn btn-outline-secondary border-0" style="background-color: #1A364A; color: #8E9EA8;" onclick="toggleAdminPwd()">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="admin-btn mt-4">
                <i class="bi bi-shield-check me-2"></i>Authenticate Admin Session
            </button>
        </form>

        <!-- Pre-configured Credentials for Evaluation -->
        <div class="demo-cred-box">
            <div class="fw-bold text-warning mb-1"><i class="bi bi-info-circle me-1"></i>Official Admin Credentials:</div>
            <div>&bull; Admin ID: <code class="text-white">ADMIN-SOC-01</code> or <code class="text-white">admin@fraudguard.in</code></div>
            <div>&bull; Passkey: <code class="text-white">Admin@FraudGuard#2026</code> (or <code>Admin@1234</code>)</div>
        </div>

        <div class="text-center mt-4 pt-3 border-top" style="border-color: #1E4055 !important;">
            <a href="login.php" class="text-decoration-none small text-secondary" style="color: #9AB0BE !important;">
                <i class="bi bi-arrow-left me-1"></i>Switch to Regular User / Citizen Portal
            </a>
        </div>
    </div>

    <script>
    function toggleAdminPwd() {
        const inp = document.getElementById('adminPwd');
        const eye = document.getElementById('eyeIcon');
        if (inp.type === 'password') {
            inp.type = 'text';
            eye.className = 'bi bi-eye-slash';
        } else {
            inp.type = 'password';
            eye.className = 'bi bi-eye';
        }
    }
    </script>
</body>
</html>
