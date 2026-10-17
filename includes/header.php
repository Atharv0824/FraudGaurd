<?php
/**
 * FraudGuard - Header Navigation Template
 * Clean Enterprise Cybersecurity Product Architecture
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../auth/auth.php';

$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$user = currentUser();
$isAuth = !empty($user);
$isAdmin = $isAuth && (($user['role'] ?? '') === 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> — <?= APP_TAGLINE ?></title>
    <meta name="description" content="Official Indian digital fraud prevention, real-time SOC incident monitoring, and public incident intake system. Check suspicious messages, UPI IDs, and report fraud.">
    
    <!-- Original Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link rel="alternate icon" href="assets/img/favicon.svg">
    
    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
    <!-- FraudGuard Custom Cybersecurity Theme -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Main Brand Navigation Bar -->
<header>
    <nav class="navbar navbar-expand-lg navbar-fg sticky-top py-2" id="main-nav">
        <div class="container-fluid px-lg-4">
            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center me-3 me-xl-4 py-0" href="index.php" title="FraudGuard Digital Defense">
                <img src="assets/img/fraudguard-logo-dark.svg" alt="FraudGuard Logo" class="navbar-brand-img">
            </a>

            <!-- Mobile Toggler -->
            <button class="navbar-toggler border-0 text-white p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-white"></i>
            </button>

            <!-- Navigation Links & User Controls -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Left Nav Links - strictly single-line, no awkward wrapping -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link-fg <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="index.php">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-fg" href="index.php#check-fraud">
                            <i class="bi bi-shield-check me-1"></i>Verify Scam
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-fg" href="index.php#report-section">
                            <i class="bi bi-file-earmark-plus me-1"></i>Report Incident
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-fg <?= $currentPage === 'track.php' ? 'active' : '' ?>" href="track.php">
                            <i class="bi bi-search me-1"></i>Track Docket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-fg <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i>SOC Console
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-fg <?= $currentPage === 'analytics.php' ? 'active' : '' ?>" href="analytics.php">
                            <i class="bi bi-graph-up me-1"></i>Threat Analytics
                        </a>
                    </li>
                </ul>

                <!-- Right Side Controls: Single-line Flex -->
                <div class="d-flex align-items-center gap-2 flex-nowrap mt-3 mt-lg-0">
                    <!-- Helpline 1930 Pill -->
                    <a href="tel:1930" class="nav-helpline-pill text-nowrap" title="National Cyber Crime Reporting Helpline (Toll-Free 24x7)">
                        <i class="bi bi-telephone-fill text-warning"></i>
                        <span>Helpline <strong>1930</strong></span>
                    </a>

                    <?php if ($isAuth): ?>
                        <!-- USER IS LOGGED IN: Professional User Badge & Dropdown -->
                        <div class="dropdown ms-1">
                            <button class="nav-user-badge-btn dropdown-toggle" type="button" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="nav-user-avatar <?= $isAdmin ? 'admin-avatar' : '' ?>">
                                    <?= htmlspecialchars($user['avatar'] ?? 'U') ?>
                                </span>
                                <div class="nav-user-info d-none d-md-flex flex-column text-start">
                                    <span class="nav-user-name text-truncate"><?= htmlspecialchars($user['name'] ?? 'User') ?></span>
                                    <span class="nav-user-role"><?= $isAdmin ? 'SOC Admin' : 'Investigator' ?></span>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" aria-labelledby="userMenuBtn" style="min-width: 230px; border-radius: 10px;">
                                <li class="px-3 py-2 border-bottom mb-1">
                                    <div class="fw-bold text-dark small"><?= htmlspecialchars($user['name']) ?></div>
                                    <div class="text-secondary small font-monospace"><?= htmlspecialchars($user['email']) ?></div>
                                    <div class="mt-1">
                                        <span class="badge <?= $isAdmin ? 'bg-danger' : 'bg-primary' ?> px-2 py-0" style="font-size: 0.7rem;">
                                            <?= $isAdmin ? 'LEVEL 3 SOC ADMIN' : 'AUTHORIZED INVESTIGATOR' ?>
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 small d-flex align-items-center gap-2" href="dashboard.php">
                                        <i class="bi bi-speedometer2 text-primary"></i>SOC Console
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 small d-flex align-items-center gap-2" href="analytics.php">
                                        <i class="bi bi-graph-up text-info"></i>Threat Analytics
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 small d-flex align-items-center gap-2" href="track.php">
                                        <i class="bi bi-search text-secondary"></i>Track Cases
                                    </a>
                                </li>
                                <?php if ($isAdmin): ?>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <button class="dropdown-item py-2 small d-flex align-items-center gap-2 text-warning" onclick="App.simulateAttack(this)">
                                            <i class="bi bi-lightning-charge-fill"></i>Simulate Attack Test
                                        </button>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item py-2 small d-flex align-items-center gap-2 text-danger fw-semibold" href="login.php?logout=1">
                                        <i class="bi bi-box-arrow-right"></i>Sign Out
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- USER IS LOGGED OUT: Clean, Professional Single-Line Action Buttons -->
                        <a href="login.php" class="btn btn-sm btn-outline-light text-nowrap px-3 py-1 font-semibold" style="font-size: 0.85rem; border-radius: 6px;">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
                        </a>
                        <a href="login.php?mode=register" class="btn-fg-primary btn-sm text-nowrap px-3 py-1" style="font-size: 0.85rem;">
                            <i class="bi bi-person-plus me-1"></i>Sign Up
                        </a>
                        <a href="admin-login.php" class="btn-admin-portal-link text-nowrap" title="Restricted SOC Administrator Login">
                            <i class="bi bi-shield-lock-fill text-warning"></i>
                            <span class="d-none d-xxl-inline">Admin</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
<main class="flex-grow-1">
