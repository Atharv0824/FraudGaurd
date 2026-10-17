<?php
/**
 * FraudGuard - Threat Intelligence & Fraud Analytics
 * Clean Solid-Color Cybersecurity Product Architecture
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/auth/auth.php';

// Require login to access Threat Vector Analytics
$user = requireLogin('login.php');
include __DIR__ . '/includes/header.php';

$pdo = getDB();

// Aggregated Category Stats
$catStats = $pdo->query("
    SELECT 
        category,
        COUNT(*) as total_cases,
        COALESCE(SUM(amount_lost), 0) as total_exposure,
        COALESCE(AVG(risk_score), 0) as avg_risk,
        SUM(CASE WHEN severity = 'Critical' THEN 1 ELSE 0 END) as critical_count
    FROM incidents
    GROUP BY category
    ORDER BY total_exposure DESC
")->fetchAll();

// Top high risk recent incidents
$topRisk = $pdo->query("
    SELECT reference_id, title, category, severity, status, amount_lost, risk_score, created_at
    FROM incidents
    ORDER BY risk_score DESC, amount_lost DESC
    LIMIT 6
")->fetchAll();
?>

<div class="container-fluid px-lg-4 py-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-2 border-bottom" style="border-color: var(--fg-border) !important;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="live-beacon"></span>
                <span class="small fw-bold text-uppercase" style="color: var(--fg-accent-green); letter-spacing: 0.05em;">Strategic Threat Intelligence</span>
                <span class="badge badge-status-neutral small">Indian Financial Grid</span>
            </div>
            <h2 class="h3 fw-bold mb-0" style="color: var(--fg-navy-primary);">Fraud Exposure & Threat Vector Analytics</h2>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Print Report
            </button>
            <a href="dashboard.php" class="btn-fg-primary btn-sm py-1 px-3">
                <i class="bi bi-speedometer2 me-1"></i>Open SOC Monitor
            </a>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="fg-card h-100">
                <div class="fg-card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold" style="color: var(--fg-navy-primary);"><i class="bi bi-currency-rupee text-success me-2"></i>Financial Exposure by Category (₹)</span>
                    <span class="badge badge-status-neutral small">Aggregate Loss</span>
                </div>
                <div class="fg-card-body p-3">
                    <div style="height: 260px;">
                        <canvas id="chart-loss-category"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="fg-card h-100">
                <div class="fg-card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold" style="color: var(--fg-navy-primary);"><i class="bi bi-shield-exclamation text-danger me-2"></i>Severity Distribution Breakdown</span>
                    <span class="badge badge-status-neutral small">Threat Tiers</span>
                </div>
                <div class="fg-card-body p-3">
                    <div style="height: 260px;">
                        <canvas id="chart-severity-split"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown Table -->
    <div class="fg-card mb-4">
        <div class="fg-card-header">
            <h5 class="fw-bold mb-0" style="color: var(--fg-navy-primary); font-size: 1rem;"><i class="bi bi-table text-primary me-2"></i>Threat Category Risk & Loss Matrix</h5>
        </div>
        <div class="table-responsive">
            <table class="table-fg mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Threat Category</th>
                        <th class="text-center">Total Ingested</th>
                        <th class="text-center">Critical Severity</th>
                        <th class="text-center">Average Risk</th>
                        <th class="text-end">Total Exposure</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($catStats)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-secondary">No incidents logged yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($catStats as $cs): 
                            $meta = FRAUD_CATEGORIES[$cs['category']] ?? ['label' => ucfirst($cs['category']), 'icon' => 'bi-shield', 'color' => 'secondary'];
                        ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold" style="color: var(--fg-navy-primary);">
                                        <i class="bi <?= $meta['icon'] ?> me-2 text-primary"></i>
                                        <?= $meta['label'] ?>
                                    </div>
                                    <small class="text-secondary font-monospace" style="font-size: 0.75rem;"><?= htmlspecialchars($cs['category']) ?></small>
                                </td>
                                <td class="text-center fw-bold"><?= $cs['total_cases'] ?></td>
                                <td class="text-center">
                                    <?php if ($cs['critical_count'] > 0): ?>
                                        <span class="badge badge-sev-critical"><?= $cs['critical_count'] ?></span>
                                    <?php else: ?>
                                        <span class="text-secondary">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold" style="color: var(--fg-warning);"><?= round($cs['avg_risk'], 1) ?>/100</span>
                                </td>
                                <td class="text-end font-monospace fw-bold" style="color: var(--fg-accent-green);">
                                    ₹<?= number_format($cs['total_exposure'], 2) ?>
                                </td>
                                <td class="text-end">
                                    <a href="dashboard.php?category=<?= urlencode($cs['category']) ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.8rem;">
                                        <i class="bi bi-funnel me-1"></i>Filter In Feed
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top High Risk Incidents -->
    <div class="fg-card">
        <div class="fg-card-header">
            <h5 class="fw-bold mb-0" style="color: var(--fg-navy-primary); font-size: 1rem;"><i class="bi bi-shield-slash text-danger me-2"></i>Highest Risk Incidents Ingested</h5>
        </div>
        <div class="table-responsive">
            <table class="table-fg mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 140px;">Case Ref</th>
                        <th>Headline</th>
                        <th style="width: 110px;">Severity</th>
                        <th style="width: 110px;">Risk Score</th>
                        <th style="width: 140px;" class="text-end">Financial Loss</th>
                        <th style="width: 130px;">Status</th>
                        <th style="width: 90px;" class="text-end">Inspect</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topRisk as $tr): ?>
                        <tr>
                            <td class="font-monospace fw-bold" style="color: var(--fg-navy-primary); font-size: 0.88rem;"><?= htmlspecialchars($tr['reference_id']) ?></td>
                            <td><div class="fw-semibold text-dark" style="max-width: 380px;"><?= htmlspecialchars($tr['title']) ?></div></td>
                            <td><?= App_getSeverityBadgePhp($tr['severity']) ?></td>
                            <td>
                                <span class="fw-bold" style="color: <?= $tr['risk_score'] >= 80 ? 'var(--fg-danger)' : 'var(--fg-warning)' ?>;">
                                    <?= $tr['risk_score'] ?>/100
                                </span>
                            </td>
                            <td class="font-monospace text-end fw-bold" style="color: var(--fg-accent-green);">₹<?= number_format($tr['amount_lost'], 2) ?></td>
                            <td><span class="badge badge-status-neutral"><?= htmlspecialchars($tr['status']) ?></span></td>
                            <td class="text-end">
                                <a href="dashboard.php?search=<?= urlencode($tr['reference_id']) ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Inspect Docket">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
function App_getSeverityBadgePhp($sev) {
    return match($sev) {
        'Critical' => '<span class="badge badge-sev-critical">Critical</span>',
        'High'     => '<span class="badge badge-sev-high">High</span>',
        'Medium'   => '<span class="badge badge-sev-medium">Medium</span>',
        default    => '<span class="badge badge-sev-low">Low</span>',
    };
}
?>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const res = await fetch('api/stats.php');
        const data = await res.json();
        if (!data.success) return;

        // Chart 1: Loss by category
        const lossCtx = document.getElementById('chart-loss-category')?.getContext('2d');
        if (lossCtx) {
            new Chart(lossCtx, {
                type: 'bar',
                data: {
                    labels: data.charts.categories.map(c => c.label),
                    datasets: [{
                        label: 'Financial Exposure (₹)',
                        data: data.charts.categories.map(c => c.loss),
                        backgroundColor: '#2E7D62',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            ticks: {
                                color: '#667580',
                                font: { family: 'Inter', size: 10 },
                                callback: val => '₹' + val.toLocaleString('en-IN')
                            },
                            grid: { color: '#EBF0F3' }
                        },
                        x: {
                            ticks: { color: '#667580', font: { family: 'Inter', size: 10 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Chart 2: Severity Split
        const sevCtx = document.getElementById('chart-severity-split')?.getContext('2d');
        if (sevCtx) {
            new Chart(sevCtx, {
                type: 'pie',
                data: {
                    labels: ['Critical', 'High', 'Medium', 'Low'],
                    datasets: [{
                        data: [
                            data.charts.severities.Critical,
                            data.charts.severities.High,
                            data.charts.severities.Medium,
                            data.charts.severities.Low
                        ],
                        backgroundColor: ['#C74646', '#D99027', '#1E4055', '#8E9EA8'],
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { color: '#24313A', font: { family: 'Inter', size: 11 } }
                        }
                    }
                }
            });
        }
    } catch (e) {
        console.error('Analytics load error:', e);
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
