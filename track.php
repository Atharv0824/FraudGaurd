<?php
/**
 * FraudGuard - Public Incident Status Tracking Portal
 * Clean Solid-Color Cybersecurity Product Architecture
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
include __DIR__ . '/includes/header.php';

$pdo = getDB();
$ref = trim($_GET['ref'] ?? '');
$incident = null;
$timeline = [];
$error = null;

if (!empty($ref)) {
    $stmt = $pdo->prepare("SELECT * FROM incidents WHERE reference_id = :ref");
    $stmt->execute(['ref' => $ref]);
    $incident = $stmt->fetch();

    if ($incident) {
        $catKey = $incident['category'];
        $incident['category_meta'] = FRAUD_CATEGORIES[$catKey] ?? ['label' => ucfirst($catKey), 'icon' => 'bi-shield'];
        $incident['severity_meta'] = SEVERITY_LEVELS[$incident['severity']] ?? ['badge' => 'secondary'];
        $incident['status_meta'] = INCIDENT_STATUSES[$incident['status']] ?? ['badge' => 'secondary', 'label' => $incident['status']];

        $tlStmt = $pdo->prepare("SELECT * FROM incident_timeline WHERE incident_id = :id ORDER BY created_at ASC");
        $tlStmt->execute(['id' => $incident['id']]);
        $timeline = $tlStmt->fetchAll();
    } else {
        $error = "No incident docket found matching Reference ID \"{$ref}\". Please verify your Case ID and try again.";
    }
}
?>

<div class="container px-lg-4 py-4">
    <!-- Header -->
    <div class="text-center mb-5">
        <span class="badge badge-status-investigating px-3 py-1 mb-2 text-uppercase">Public Investigation Tracking</span>
        <h1 class="display-6 fw-bold mb-2" style="color: var(--fg-navy-primary);">Track Investigation Docket Status</h1>
        <p class="text-secondary mx-auto" style="max-width: 620px;">
            Enter your official cryptographic Case Reference ID (e.g. <code>FRD-2026-A83F12</code>) to check the active investigation lifecycle, assigned cyber cell officer, and timeline logs.
        </p>

        <!-- Search Bar -->
        <form method="GET" action="track.php" class="row justify-content-center g-2 mt-3">
            <div class="col-md-7 col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border" style="border-color: var(--fg-border); color: var(--fg-text-muted);"><i class="bi bi-hash"></i></span>
                    <input type="text" name="ref" class="form-control form-control-fg font-monospace text-uppercase" 
                           placeholder="FRD-2026-XXXXXX" value="<?= htmlspecialchars($ref) ?>" required>
                    <button class="btn-fg-primary px-4 fw-semibold" type="submit">
                        <i class="bi bi-search me-1"></i>Lookup Docket
                    </button>
                </div>
            </div>
        </form>

        <?php if (empty($ref)): ?>
            <div class="mt-3 text-secondary small">
                <span>Try sample case reference:</span>
                <a href="track.php?ref=FRD-2026-A83F12" class="font-monospace text-primary fw-bold ms-1 me-2">FRD-2026-A83F12</a>
                <a href="track.php?ref=FRD-2026-H82K19" class="font-monospace text-primary fw-bold me-2">FRD-2026-H82K19</a>
                <a href="track.php?ref=FRD-2026-D44B56" class="font-monospace text-primary fw-bold">FRD-2026-D44B56</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($error): ?>
        <div class="alert mx-auto text-center p-3 rounded" style="max-width: 620px; background-color: var(--fg-danger-light); border: 1px solid #F5B8B8; color: var(--fg-danger);">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($incident): ?>
        <!-- Incident Case Dossier Card -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="fg-card mb-4">
                    <div class="fg-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <span class="badge badge-status-neutral font-monospace fs-6 me-2" style="color: var(--fg-navy-primary);"><?= htmlspecialchars($incident['reference_id']) ?></span>
                            <?= App_getStatusBadgePhp($incident['status']) ?>
                        </div>
                        <div class="text-secondary small">
                            <i class="bi bi-calendar3 me-1"></i>Reported on <?= date('d M Y, H:i', strtotime($incident['created_at'])) ?> IST
                        </div>
                    </div>

                    <div class="fg-card-body p-4">
                        <h4 class="fw-bold mb-3" style="color: var(--fg-navy-primary);"><?= htmlspecialchars($incident['title']) ?></h4>

                        <!-- Key Metrics Ribbon -->
                        <div class="row g-3 p-3 rounded mb-4" style="background-color: #F8FAFB; border: 1px solid var(--fg-border);">
                            <div class="col-sm-6 col-md-3">
                                <small class="text-secondary d-block">Threat Vector</small>
                                <span class="fw-semibold" style="color: var(--fg-navy-primary);">
                                    <i class="bi <?= $incident['category_meta']['icon'] ?> me-1 text-primary"></i>
                                    <?= $incident['category_meta']['label'] ?>
                                </span>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <small class="text-secondary d-block">Urgency Level</small>
                                <div><?= App_getSeverityBadgePhp($incident['severity']) ?></div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <small class="text-secondary d-block">Financial Loss</small>
                                <span class="fw-bold font-monospace" style="color: var(--fg-accent-green); font-size: 1.05rem;">
                                    ₹<?= number_format($incident['amount_lost'], 2) ?>
                                </span>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <small class="text-secondary d-block">Assigned Cyber Officer</small>
                                <span class="fw-semibold small" style="color: var(--fg-navy-primary);">
                                    <i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($incident['assigned_investigator']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Progress Bar Pipeline -->
                        <div class="mb-4">
                            <h6 class="small fw-bold text-uppercase mb-2" style="color: var(--fg-navy-primary); letter-spacing: 0.05em;">Investigation Lifecycle Status</h6>
                            <?php
                            $progressPct = match($incident['status']) {
                                'New' => 20,
                                'Under Investigation' => 50,
                                'Escalated' => 75,
                                'Pending Action' => 85,
                                'Resolved' => 100,
                                'False Positive' => 100,
                                default => 20
                            };
                            $barColor = match($incident['status']) {
                                'Resolved' => 'bg-success',
                                'Escalated' => 'bg-warning',
                                'False Positive' => 'bg-secondary',
                                default => 'bg-primary'
                            };
                            ?>
                            <div class="progress" style="height: 10px; background-color: #E2E8EC;">
                                <div class="progress-bar <?= $barColor ?> progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $progressPct ?>%"></div>
                            </div>
                            <div class="d-flex justify-content-between text-secondary small mt-2 font-monospace" style="font-size: 0.75rem;">
                                <span>Report Ingested</span>
                                <span>Triage & Verification</span>
                                <span>Active SOC Analysis</span>
                                <span>Mitigation & Resolution</span>
                            </div>
                        </div>

                        <!-- Narrative & Suspect Artifacts -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-7">
                                <h6 class="small fw-bold text-uppercase mb-2" style="color: var(--fg-navy-primary); letter-spacing: 0.05em;">Incident Narrative</h6>
                                <p class="p-3 rounded border mb-0" style="background-color: #FAFCFD; border-color: var(--fg-border) !important; color: var(--fg-text-main); font-size: 0.92rem; min-height: 120px; line-height: 1.6;">
                                    <?= nl2br(htmlspecialchars($incident['description'])) ?>
                                </p>
                            </div>
                            <div class="col-md-5">
                                <h6 class="small fw-bold text-uppercase mb-2" style="color: var(--fg-navy-primary); letter-spacing: 0.05em;">Suspect Indicators (IOCs)</h6>
                                <div class="evidence-box" style="min-height: 120px;">
                                    <?= htmlspecialchars($incident['suspect_details'] ?: 'No public digital IOCs registered for this case.') ?>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline History -->
                        <h6 class="small fw-bold text-uppercase mb-3 pt-3 border-top" style="color: var(--fg-navy-primary); letter-spacing: 0.05em; border-color: var(--fg-border) !important;">
                            <i class="bi bi-clock-history me-1 text-primary"></i> Official Investigation Timeline
                        </h6>

                        <?php if (empty($timeline)): ?>
                            <p class="text-secondary small">Case ingested. Awaiting first investigator review note.</p>
                        <?php else: ?>
                            <ul class="timeline-list mb-4">
                                <?php foreach ($timeline as $item): ?>
                                    <li class="timeline-item">
                                        <div class="timeline-dot <?= htmlspecialchars($item['action_type']) ?>"></div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="small" style="color: var(--fg-navy-primary);"><?= htmlspecialchars($item['author']) ?></strong>
                                            <span class="text-secondary small" style="font-size: 0.75rem;"><?= date('d M Y, H:i:s', strtotime($item['created_at'])) ?> IST</span>
                                        </div>
                                        <div class="small text-secondary"><?= nl2br(htmlspecialchars($item['note'])) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <!-- Guidance Box -->
                        <div class="p-3 rounded small" style="background-color: #F8FAFB; border: 1px solid var(--fg-border);">
                            <div class="fw-bold mb-1" style="color: var(--fg-navy-primary);"><i class="bi bi-shield-check text-success me-1"></i>Official Law Enforcement Liaison</div>
                            <div class="text-secondary mb-2">
                                If you have additional evidence (bank statement PDFs, WhatsApp screenshots, or call recordings), keep them ready for your state cyber crime cell.
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="https://cybercrime.gov.in" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary py-1 px-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>National Cyber Crime Portal
                                </a>
                                <a href="tel:1930" class="btn btn-sm btn-outline-warning py-1 px-3 text-dark">
                                    <i class="bi bi-telephone-fill me-1 text-warning"></i>Helpline 1930
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
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

function App_getStatusBadgePhp($status) {
    return match($status) {
        'New' => '<span class="badge badge-status-new px-2 py-1">New / Ingested</span>',
        'Under Investigation' => '<span class="badge badge-status-investigating px-2 py-1">Under Investigation</span>',
        'Escalated' => '<span class="badge badge-status-escalated px-2 py-1">Escalated</span>',
        'Pending Action' => '<span class="badge badge-status-investigating px-2 py-1">Pending Action</span>',
        'Resolved' => '<span class="badge badge-status-resolved px-2 py-1"><i class="bi bi-check2 me-1"></i>Resolved</span>',
        default => '<span class="badge badge-status-neutral px-2 py-1">' . htmlspecialchars($status) . '</span>',
    };
}
?>

<?php include __DIR__ . '/includes/footer.php'; ?>
