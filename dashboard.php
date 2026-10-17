<?php
/**
 * FraudGuard - SOC Real-Time Digital Fraud Incident Monitoring Center
 * Clean Solid-Color Cybersecurity Product Architecture
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/auth/auth.php';

// Require login to access the SOC Incident Monitoring Center
$user = requireLogin('login.php');
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid px-lg-4 py-3">
    <!-- Dashboard Command Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom" style="border-color: var(--fg-border) !important;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span id="stream-beacon" class="live-beacon"></span>
                <span id="stream-status-text" class="fw-bold small text-uppercase" style="color: var(--fg-accent-green); letter-spacing: 0.05em;">Live Feed Active (3s Polling)</span>
                <span class="badge badge-status-neutral ms-2">SOC Triage Center</span>
                <span class="badge badge-status-investigating small">Indian Financial Grid</span>
            </div>
            <h2 class="h3 fw-bold mb-0" style="color: var(--fg-navy-primary);">Incident Monitoring & SOC Triage Console</h2>
            <div class="text-secondary small mt-1">
                <i class="bi bi-info-circle me-1 text-primary"></i>Demonstration SOC Queue &bull; Verified Sample Incidents for Testing & Evaluation
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Audio Alarm Toggle -->
            <button id="toggle-audio-btn" type="button" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-volume-up-fill me-1"></i>Alarm On
            </button>

            <!-- Stream Pause/Resume -->
            <button id="toggle-stream-btn" type="button" class="btn btn-sm btn-outline-success">
                <i class="bi bi-pause-fill me-1"></i>Pause Stream
            </button>

            <!-- Export CSV -->
            <button id="btn-export-csv" type="button" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
            </button>

            <!-- Live Simulation Button -->
            <button type="button" class="btn-fg-primary btn-sm py-1 px-3" onclick="App.simulateAttack(this)" title="Injects a realistic attack to demonstrate live alert stream and synthesized audio">
                <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Simulate Live Fraud
            </button>
        </div>
    </div>

    <!-- Executive KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-tile kpi-navy">
                <div>
                    <div class="kpi-label">Total Ingested Incidents</div>
                    <div class="kpi-value" id="kpi-total">&mdash;</div>
                </div>
                <div class="kpi-foot">
                    <i class="bi bi-shield-check text-primary"></i>All-time logged cases
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="kpi-tile kpi-amber">
                <div>
                    <div class="kpi-label">Active / Under Triage</div>
                    <div class="kpi-value text-warning" id="kpi-active">&mdash;</div>
                </div>
                <div class="kpi-foot">
                    <i class="bi bi-clock-history text-warning"></i>Requires investigator action
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="kpi-tile kpi-danger">
                <div>
                    <div class="kpi-label">Critical Threat Alerts</div>
                    <div class="kpi-value text-danger" id="kpi-critical">&mdash;</div>
                </div>
                <div class="kpi-foot">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>High-loss or active threat
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="kpi-tile kpi-green">
                <div>
                    <div class="kpi-label">Total Financial Exposure</div>
                    <div class="kpi-value text-green" id="kpi-loss">&mdash;</div>
                </div>
                <div class="kpi-foot">
                    <i class="bi bi-currency-rupee text-success"></i>Cumulative at-risk (INR)
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="fg-card h-100">
                <div class="fg-card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold" style="color: var(--fg-navy-primary);">
                        <i class="bi bi-graph-up text-primary me-2"></i>7-Day Ingestion Velocity & Anomaly Trends
                    </span>
                    <span class="badge badge-status-neutral small">Real-Time Aggregates</span>
                </div>
                <div class="fg-card-body p-3">
                    <div style="height: 220px;">
                        <canvas id="chart-trend"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="fg-card h-100">
                <div class="fg-card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold" style="color: var(--fg-navy-primary);">
                        <i class="bi bi-pie-chart text-primary me-2"></i>Threat Vector Distribution
                    </span>
                    <span class="badge badge-status-neutral small">Category Split</span>
                </div>
                <div class="fg-card-body p-3">
                    <div style="height: 220px;">
                        <canvas id="chart-category"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Incidents Monitoring Table Card -->
    <div class="fg-card mb-4">
        <div class="fg-card-header">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border"><i class="bi bi-search text-secondary"></i></span>
                        <input type="text" id="filter-search" class="form-control form-control-fg form-control-sm" placeholder="Search Ref, Suspect, IP, Title...">
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <select id="filter-category" class="form-select form-select-fg form-select-sm">
                        <option value="all">All Threat Categories</option>
                        <?php foreach (FRAUD_CATEGORIES as $k => $c): ?>
                            <option value="<?= $k ?>"><?= $c['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-3 col-md-2">
                    <select id="filter-severity" class="form-select form-select-fg form-select-sm">
                        <option value="all">All Severities</option>
                        <?php foreach (SEVERITY_LEVELS as $lvl => $m): ?>
                            <option value="<?= $lvl ?>"><?= $lvl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-3 col-md-2">
                    <select id="filter-status" class="form-select form-select-fg form-select-sm">
                        <option value="all">All Statuses</option>
                        <?php foreach (INCIDENT_STATUSES as $st => $m): ?>
                            <option value="<?= $st ?>"><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 text-md-end">
                    <span class="badge badge-status-resolved py-2 px-3 small">
                        <span class="live-beacon me-1"></span>Auto-Streaming
                    </span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-fg" id="incidents-table">
                <thead>
                    <tr>
                        <th style="width: 140px;">Reference ID</th>
                        <th>Incident Headline & Category</th>
                        <th style="width: 110px;">Severity</th>
                        <th style="width: 110px;">Risk Score</th>
                        <th style="width: 130px;" class="text-end">Loss Exposure</th>
                        <th style="width: 150px;">Status</th>
                        <th style="width: 170px;">Assigned Agent</th>
                        <th style="width: 100px;" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="incidents-table-body">
                    <tr>
                        <td colspan="8" class="text-center py-5 text-secondary">
                            <span class="spinner-border spinner-border-sm me-2 text-primary"></span>Loading real-time incidents feed...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Incident Dossier Modal -->
<div class="modal fade" id="modal-incident-detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content modal-content-fg">
            <div class="modal-header modal-header-fg">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge badge-status-neutral font-monospace" id="modal-detail-ref"></span>
                        <span id="modal-detail-sev"></span>
                        <span id="modal-detail-status"></span>
                    </div>
                    <h5 class="modal-title fw-bold mb-0" style="color: var(--fg-navy-primary);" id="modal-detail-title"></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- Quick Meta Grid -->
                <div class="row g-3 p-3 rounded mb-4" style="background-color: #F8FAFB; border: 1px solid var(--fg-border);">
                    <div class="col-sm-6 col-md-3">
                        <small class="text-secondary d-block">Threat Category</small>
                        <span class="fw-semibold" style="color: var(--fg-navy-primary);" id="modal-detail-cat"></span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <small class="text-secondary d-block">Financial Loss</small>
                        <span class="fw-bold font-monospace" style="color: var(--fg-accent-green);" id="modal-detail-loss"></span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <small class="text-secondary d-block">Calculated Risk</small>
                        <span id="modal-detail-risk"></span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <small class="text-secondary d-block">Incident Timestamp</small>
                        <span class="small fw-semibold" style="color: var(--fg-navy-primary);" id="modal-detail-date"></span>
                    </div>
                </div>

                <!-- Incident Narrative -->
                <h6 class="fw-bold text-uppercase mb-2" style="color: var(--fg-navy-primary); letter-spacing: 0.05em; font-size: 0.85rem;">Incident Narrative & Impact</h6>
                <p class="p-3 rounded border mb-4" style="background-color: #FAFCFD; border-color: var(--fg-border) !important; color: var(--fg-text-main); font-size: 0.92rem; line-height: 1.6;" id="modal-detail-desc"></p>

                <!-- IOC Suspect Indicators Box -->
                <h6 class="fw-bold text-uppercase mb-2" style="color: var(--fg-navy-primary); letter-spacing: 0.05em; font-size: 0.85rem;">Digital IOCs & Suspect Intelligence</h6>
                <div class="evidence-box mb-4" id="modal-detail-suspect"></div>

                <!-- Attribution & Reporter -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded border" style="background-color: #FAFCFD; border-color: var(--fg-border) !important;">
                            <small class="text-secondary d-block mb-1">Reporter / Source</small>
                            <span class="fw-semibold small" style="color: var(--fg-navy-primary);" id="modal-detail-reporter"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded border" style="background-color: #FAFCFD; border-color: var(--fg-border) !important;">
                            <small class="text-secondary d-block mb-1">Assigned Investigator</small>
                            <span class="fw-semibold small" style="color: var(--fg-navy-primary);" id="modal-detail-investigator"></span>
                            <div class="text-muted small mt-1">Report IP: <span class="font-monospace" id="modal-detail-ip"></span></div>
                        </div>
                    </div>
                </div>

                <!-- Chronological Audit Timeline -->
                <h6 class="fw-bold text-uppercase mb-3" style="color: var(--fg-navy-primary); letter-spacing: 0.05em; font-size: 0.85rem;">Investigation Timeline & Audit Trail</h6>
                <ul class="timeline-list mb-4" id="modal-timeline-list"></ul>

                <!-- Add Note Form -->
                <div class="p-3 rounded" style="background-color: #F8FAFB; border: 1px solid var(--fg-border);">
                    <h6 class="fw-bold mb-2" style="color: var(--fg-navy-primary); font-size: 0.9rem;"><i class="bi bi-chat-left-text me-1 text-primary"></i>Append Investigation Finding</h6>
                    <form id="form-add-note">
                        <input type="hidden" id="note-incident-id" value="">
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <input type="text" id="note-author" class="form-control form-control-fg form-control-sm" placeholder="Analyst Name" value="Lead SOC Analyst">
                            </div>
                            <div class="col-md-8">
                                <input type="text" id="note-content" class="form-control form-control-fg form-control-sm" placeholder="Enter findings, bank lien status, or IOC updates..." required>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" id="btn-submit-note" class="btn-fg-primary btn-sm py-1 px-3">
                                <i class="bi bi-plus-circle me-1"></i>Add Finding
                            </button>
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer modal-footer-fg">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Triage / Update Status Modal -->
<div class="modal fade" id="modal-triage" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-content-fg">
            <form id="form-triage">
                <div class="modal-header modal-header-fg">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge badge-status-investigating font-monospace" id="triage-ref-badge"></span>
                            <span class="fw-semibold small" style="color: var(--fg-navy-primary);">Triage & Case Assignment</span>
                        </div>
                        <h5 class="modal-title fw-bold" style="color: var(--fg-navy-primary);" id="triage-incident-title"></h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="triage-incident-id" value="">

                    <div class="mb-3">
                        <label for="triage-status" class="form-label-fg">Investigation Status</label>
                        <select id="triage-status" class="form-select form-select-fg" required>
                            <?php foreach (INCIDENT_STATUSES as $st => $m): ?>
                                <option value="<?= $st ?>"><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="triage-severity" class="form-label-fg">Adjust Urgency / Severity</label>
                        <select id="triage-severity" class="form-select form-select-fg" required>
                            <?php foreach (SEVERITY_LEVELS as $lvl => $m): ?>
                                <option value="<?= $lvl ?>"><?= $lvl ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="triage-investigator" class="form-label-fg">Assigned Cyber Cell Unit / Officer</label>
                        <input type="text" id="triage-investigator" class="form-control form-control-fg" 
                               placeholder="e.g. Inspector Priya Nair (Cyber Crime Cell, Mumbai)">
                    </div>

                    <div class="mb-3">
                        <label for="triage-note" class="form-label-fg">Triage Action Note & Rationale</label>
                        <textarea id="triage-note" class="form-control form-control-fg" rows="3" 
                                  placeholder="Document reasoning for status update, 1930 liaison, or bank account freezing action..."></textarea>
                    </div>
                </div>
                <div class="modal-footer modal-footer-fg">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn-submit-triage" class="btn-fg-primary btn-sm py-1 px-3">Save Updates</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/dashboard.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
