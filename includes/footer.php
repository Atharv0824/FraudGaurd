</main>

<!-- Human-Designed Trustworthy Cybersecurity Footer -->
<footer class="footer-fg">
    <div class="container-fluid px-lg-4">
        <div class="row g-4 justify-content-between">
            <!-- Brand & Mission Column -->
            <div class="col-lg-4 col-md-6">
                <div class="mb-3">
                    <img src="assets/img/fraudguard-logo-dark.svg" alt="FraudGuard Logo" style="height: 36px; width: auto;">
                </div>
                <p class="small text-secondary mb-3" style="color: #9AB0BE !important; max-width: 380px;">
                    <?= APP_NAME ?> provides real-time fraud triage, public incident intake, and threat vector intelligence to protect citizens, students, and businesses from cyber financial crimes across India.
                </p>
                <div class="d-flex align-items-center gap-2 small">
                    <span class="live-beacon"></span>
                    <span class="font-monospace text-white small">SYSTEM ACTIVE | ENGINE: <?= strtoupper(DB_DRIVER) ?></span>
                </div>
            </div>

            <!-- Quick Navigation -->
            <div class="col-lg-2 col-md-3 col-6">
                <h6>Platform</h6>
                <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                    <li><a href="index.php"><i class="bi bi-chevron-right me-1 small"></i>Home</a></li>
                    <li><a href="index.php#check-fraud"><i class="bi bi-chevron-right me-1 small"></i>Verify Indicator</a></li>
                    <li><a href="index.php#report-section"><i class="bi bi-chevron-right me-1 small"></i>Report Incident</a></li>
                    <li><a href="track.php"><i class="bi bi-chevron-right me-1 small"></i>Track Case Status</a></li>
                    <li><a href="dashboard.php"><i class="bi bi-chevron-right me-1 small"></i>SOC Monitor</a></li>
                    <li><a href="analytics.php"><i class="bi bi-chevron-right me-1 small"></i>Threat Analytics</a></li>
                </ul>
            </div>

            <!-- Indian Cybercrime Authority Links -->
            <div class="col-lg-3 col-md-3 col-6">
                <h6>Official Authorities</h6>
                <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                    <li>
                        <a href="https://cybercrime.gov.in" target="_blank" rel="noopener" class="d-flex align-items-center gap-1">
                            <i class="bi bi-box-arrow-up-right me-1 small"></i>cybercrime.gov.in
                        </a>
                    </li>
                    <li>
                        <a href="tel:1930" class="d-flex align-items-center gap-1">
                            <i class="bi bi-telephone-fill me-1 text-warning small"></i>Helpline: 1930 (MHA / I4C)
                        </a>
                    </li>
                    <li>
                        <a href="https://sachet.rbi.org.in" target="_blank" rel="noopener" class="d-flex align-items-center gap-1">
                            <i class="bi bi-bank me-1 small"></i>RBI Sachet Portal
                        </a>
                    </li>
                    <li>
                        <a href="https://www.npci.org.in" target="_blank" rel="noopener" class="d-flex align-items-center gap-1">
                            <i class="bi bi-qr-code me-1 small"></i>NPCI UPI Security
                        </a>
                    </li>
                    <li>
                        <a href="https://www.cert-in.org.in" target="_blank" rel="noopener" class="d-flex align-items-center gap-1">
                            <i class="bi bi-shield-check me-1 small"></i>CERT-In Advisory
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Emergency Guidance Column -->
            <div class="col-lg-3 col-md-12">
                <h6>Emergency Protocol</h6>
                <div class="p-3 rounded bg-dark border border-secondary border-opacity-25 small" style="background-color: #0E1E2A !important;">
                    <div class="text-white fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill text-danger me-1"></i>Financial Loss Emergency?</div>
                    <p class="small text-secondary mb-2" style="color: #8E9EA8 !important;">
                        Dial <strong>1930</strong> immediately within the <em>Golden Hour</em> to initiate inter-bank freeze on victimized accounts.
                    </p>
                    <a href="index.php#report-section" class="btn btn-sm btn-outline-warning w-100 fw-bold">
                        <i class="bi bi-file-earmark-text me-1"></i>Ingest Incident Docket
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright Bar -->
        <div class="footer-bottom-bar d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                &copy; <?= date('Y') ?> <strong><?= APP_NAME ?></strong> &mdash; Cyber Financial Crime Intelligence & Incident Monitoring. Built for digital security in India.
            </div>
            <div class="d-flex gap-3 text-secondary">
                <span><i class="bi bi-lock me-1"></i>256-Bit TLS Intake</span>
                <span><i class="bi bi-shield-check me-1"></i>Strict Non-Custodial IOCs</span>
                <span><i class="bi bi-geo-alt me-1"></i>India Grid</span>
            </div>
        </div>
    </div>
</footer>

<!-- Toast Alert Container -->
<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;"></div>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Core App JS -->
<script src="assets/js/app.js"></script>
</body>
</html>
