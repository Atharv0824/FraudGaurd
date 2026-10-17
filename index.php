<?php
/**
 * FraudGuard - Homepage & Incident Intake Portal
 * Human-designed, clean cybersecurity architecture tailored for India
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
include __DIR__ . '/includes/header.php';
?>

<!-- Section 1: Hero Section -->
<section class="fg-hero position-relative overflow-hidden">
    <div class="container-fluid px-lg-4">
        <div class="row align-items-center g-4 g-lg-5">
            <!-- Left Hero Copy -->
            <div class="col-lg-6">
                <div class="fg-hero-badge">
                    <span class="live-beacon"></span>
                    <span>National Cyber Crime Defense Grid</span>
                </div>
                <h1 class="display-5 fw-bold text-white mb-3">
                    Stay One Step Ahead of Online Fraud.
                </h1>
                <p class="lead mb-4">
                    Identify deceptive SMS links, suspicious UPI payment requests, and fraudulent caller indicators before you lose money. Report cyber incidents directly into the triage monitoring stream.
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="#check-fraud" class="btn-fg-primary py-2 px-4 fs-6">
                        <i class="bi bi-shield-check"></i>Check for Fraud
                    </a>
                    <a href="#report-section" class="btn btn-outline-light py-2 px-4 fs-6 fw-semibold" style="border-radius: var(--fg-radius-sm);">
                        <i class="bi bi-file-earmark-plus me-1"></i>Report Incident
                    </a>
                </div>
                <div class="d-flex align-items-center gap-3 pt-2 text-secondary small" style="color: #8E9EA8 !important;">
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-shield-lock-fill text-success"></i>
                        <span>No account needed to check</span>
                    </div>
                    <span>&bull;</span>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-telephone-fill text-warning"></i>
                        <span>Emergency Helpline: <strong>1930</strong></span>
                    </div>
                </div>
            </div>

            <!-- Right Hero Visual: Pure Cyber Defense Shield Matrix (No humans) -->
            <div class="col-lg-6">
                <div class="fg-hero-media">
                    <img src="assets/img/hero-cyber-shield.jpg" alt="FraudGuard Digital Banking Protection Network and Shield Matrix" loading="eager">
                    <div class="fg-hero-overlay-tag">
                        <div class="d-flex align-items-center gap-2">
                            <!-- Restrained Shield Draw SVG -->
                            <svg class="shield-draw-svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <path class="shield-outline" d="M16 3L5 7.5V14C5 21.2 9.7 27.8 16 29.5C22.3 27.8 27 21.2 27 14V7.5L16 3Z" stroke="#2E7D62" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path class="shield-check" d="M11 15L14.5 18.5L21.5 11.5" stroke="#E7F2EC" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div>
                                <div class="text-white fw-bold small">Digital Banking Protection Network</div>
                                <div class="small" style="color: #9AB0BE; font-size: 0.75rem;">Active Real-Time Threat Shield &bull; Multi-Vector Triage</div>
                            </div>
                        </div>
                        <a href="dashboard.php" class="btn btn-sm btn-outline-light py-1 px-2" style="font-size: 0.75rem;">
                            SOC Feed <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Interactive Fraud Indicator Verifier Tool -->
<section id="check-fraud" class="py-5">
    <div class="container-fluid px-lg-4">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">
                <div class="verifier-card">
                    <!-- Tab Switcher -->
                    <ul class="nav nav-tabs verifier-nav-tabs" id="verifierTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-msg-btn" data-bs-toggle="tab" data-bs-target="#tab-msg" type="button" role="tab">
                                <i class="bi bi-chat-left-text me-1 text-primary"></i>Check SMS / Message Text
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-upi-btn" data-bs-toggle="tab" data-bs-target="#tab-upi" type="button" role="tab">
                                <i class="bi bi-qr-code-scan me-1 text-primary"></i>Check UPI ID / Payment Link
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-phone-btn" data-bs-toggle="tab" data-bs-target="#tab-phone" type="button" role="tab">
                                <i class="bi bi-telephone me-1 text-primary"></i>Check Phone / Helpline
                            </button>
                        </li>
                    </ul>

                    <div class="p-4 p-md-5">
                        <div class="tab-content" id="verifierTabContent">
                            <!-- Tab 1: Check Message / SMS -->
                            <div class="tab-pane fade show active" id="tab-msg" role="tabpanel">
                                <h5 class="fw-bold mb-1" style="color: var(--fg-navy-primary);">Analyze Suspicious Message or Alert</h5>
                                <p class="text-secondary small mb-3">Paste the text of an SMS, WhatsApp forward, or email you received to test against known Indian scam patterns.</p>
                                
                                <div class="mb-3">
                                    <textarea id="verify-msg-input" class="form-control form-control-fg" rows="3" 
                                              placeholder="Paste message here (e.g. 'Dear customer, your electricity will be disconnected tonight at 9:30 PM...' or 'CBI summons issued for parcel containing contraband...')"></textarea>
                                </div>
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="d-flex gap-2 flex-wrap small">
                                        <span class="text-secondary">Try sample:</span>
                                        <button type="button" class="btn btn-sm btn-light border py-0 px-2 small" onclick="setVerifierSample('msg', 'Dear consumer, your electricity power will be disconnected at 9:30 PM tonight due to unpaid bill. Call official officer +91 8200199281 immediately.')">Electricity Bill</button>
                                        <button type="button" class="btn btn-sm btn-light border py-0 px-2 small" onclick="setVerifierSample('msg', 'Important notice: CBI Cyber Crime Cell has put you under Digital Arrest for illegal money laundering package seized at Mumbai Airport.')">Digital Arrest</button>
                                        <button type="button" class="btn btn-sm btn-light border py-0 px-2 small" onclick="setVerifierSample('msg', 'SBI Alert: Your NetBanking account is blocked due to KYC document expiry. Click https://sbi-kyc-secure.xyz to update within 24h.')">KYC Phishing</button>
                                    </div>
                                    <button type="button" class="btn-fg-primary" onclick="runVerification('message')">
                                        <i class="bi bi-shield-check"></i>Verify Message
                                    </button>
                                </div>
                            </div>

                            <!-- Tab 2: Check UPI ID / Payment Link -->
                            <div class="tab-pane fade" id="tab-upi" role="tabpanel">
                                <h5 class="fw-bold mb-1" style="color: var(--fg-navy-primary);">Verify UPI VPA or Payment Request</h5>
                                <p class="text-secondary small mb-3">Enter the Virtual Payment Address (e.g. <code>username@okhdfcbank</code>) or QR payment description you were asked to authorize.</p>
                                
                                <div class="mb-3">
                                    <input type="text" id="verify-upi-input" class="form-control form-control-fg" 
                                           placeholder="e.g. merchant-refund-desk@paytm or 'Scan QR code to receive ₹35,000 refund'">
                                </div>
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="d-flex gap-2 flex-wrap small">
                                        <span class="text-secondary">Try sample:</span>
                                        <button type="button" class="btn btn-sm btn-light border py-0 px-2 small" onclick="setVerifierSample('upi', 'Scan this QR code and enter your UPI PIN to receive ₹25,000 cashback')">Scan QR to Receive</button>
                                        <button type="button" class="btn btn-sm btn-light border py-0 px-2 small" onclick="setVerifierSample('upi', 'customer-care-helpline@ybl')">Fake Support VPA</button>
                                    </div>
                                    <button type="button" class="btn-fg-primary" onclick="runVerification('upi')">
                                        <i class="bi bi-shield-check"></i>Verify UPI / QR
                                    </button>
                                </div>
                            </div>

                            <!-- Tab 3: Check Phone / Helpline -->
                            <div class="tab-pane fade" id="tab-phone" role="tabpanel">
                                <h5 class="fw-bold mb-1" style="color: var(--fg-navy-primary);">Verify Support Number or Caller ID</h5>
                                <p class="text-secondary small mb-3">Check unknown caller numbers claiming to represent banks, courier services, or government departments.</p>
                                
                                <div class="mb-3">
                                    <input type="text" id="verify-phone-input" class="form-control form-control-fg" 
                                           placeholder="e.g. +91 98201 44823 or 'HDFC Customer Care Helpline'">
                                </div>
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="d-flex gap-2 flex-wrap small">
                                        <span class="text-secondary">Try sample:</span>
                                        <button type="button" class="btn btn-sm btn-light border py-0 px-2 small" onclick="setVerifierSample('phone', 'Google search result for Airline Refund Helpline +91 7990188210')">Google Search Helpline</button>
                                        <button type="button" class="btn btn-sm btn-light border py-0 px-2 small" onclick="setVerifierSample('phone', '1930')">Official Helpline 1930</button>
                                    </div>
                                    <button type="button" class="btn-fg-primary" onclick="runVerification('phone')">
                                        <i class="bi bi-shield-check"></i>Verify Phone Number
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Verification Result Card (Dynamic) -->
                        <div id="verifier-result" class="verifier-result-box">
                            <div class="d-flex align-items-start gap-3">
                                <div id="verifier-icon-wrap" class="fs-3"></div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <h6 id="verifier-title" class="fw-bold mb-0"></h6>
                                        <span id="verifier-risk-tag" class="badge"></span>
                                    </div>
                                    <div id="verifier-desc" class="small mb-2"></div>
                                    <div id="verifier-action" class="pt-2 border-top border-opacity-25 small"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Official Incident Intake & Reporting Docket Section -->
<section id="report-section" class="py-5" style="background-color: #FFFFFF; border-top: 1px solid var(--fg-border); border-bottom: 1px solid var(--fg-border);">
    <div class="container-fluid px-lg-4">
        <!-- Section Header -->
        <div class="row align-items-center justify-content-between mb-4 g-3">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background-color: var(--fg-danger-light); border: 1px solid #F5B8B8;">
                    <span class="live-beacon critical"></span>
                    <span class="small fw-bold text-uppercase" style="color: var(--fg-danger); letter-spacing: 0.05em;">Official Fraud Intake Center</span>
                </div>
                <h2 class="h3 fw-bold mb-2" style="color: var(--fg-navy-primary);">File a Digital Fraud Incident Docket</h2>
                <p class="text-secondary mb-0">
                    Submit fraudulent transactions, unauthorized UPI debits, phishing messages, or cyber extortion. Reports are immediately routed into the SOC real-time incident monitoring queue.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="p-3 text-start d-inline-block rounded" style="background-color: #FAFCFD; border: 1px solid var(--fg-border);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle p-2 text-primary" style="background-color: var(--fg-info-light);">
                            <i class="bi bi-search fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold small" style="color: var(--fg-navy-primary);">Already have a Case ID?</div>
                            <div class="text-secondary small">Track progress & notes</div>
                            <a href="track.php" class="small fw-semibold text-primary">
                                Track Case Status <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Intake Form Container -->
        <div class="row justify-content-center">
            <div class="col-xl-10" id="report-card-container">
                <div class="fg-card">
                    <div class="fg-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: var(--fg-navy-primary);"><i class="bi bi-shield-lock me-2 text-primary"></i>Incident Intake Dossier</h5>
                            <small class="text-secondary">Provide digital indicators (IOCs) to assist cyber crime investigators</small>
                        </div>
                        <span class="badge badge-status-neutral font-monospace"><i class="bi bi-lock-fill me-1 text-success"></i>TLS 256-Bit Encrypted</span>
                    </div>

                    <div class="fg-card-body p-4">
                        <?php if ($isAuth): ?>
                            <div class="p-3 mb-4 rounded border d-flex align-items-center justify-content-between flex-wrap gap-2" style="background-color: var(--fg-green-light); border-color: #B8DACB !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-check-fill text-success fs-5"></i>
                                    <div>
                                        <div class="fw-bold small" style="color: #1E5C46;">Logged In as Verified Reporter</div>
                                        <div class="text-secondary small"><?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>) &bull; <?= $isAdmin ? 'SOC Admin' : 'Investigator' ?></div>
                                    </div>
                                </div>
                                <span class="badge badge-status-resolved px-3 py-1">Direct SOC Routing Active</span>
                            </div>
                        <?php else: ?>
                            <div class="p-3 mb-4 rounded border d-flex align-items-center justify-content-between flex-wrap gap-3" style="background-color: #FAFCFD; border-color: var(--fg-border) !important;">
                                <div>
                                    <div class="fw-bold small" style="color: var(--fg-navy-primary);"><i class="bi bi-person-lock me-1 text-primary"></i>Sign In Recommended for Case Management</div>
                                    <div class="text-secondary small">Sign in to auto-link this incident to your account and receive real-time investigator notifications.</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="login.php" class="btn btn-sm btn-outline-primary px-3 py-1 fw-semibold">Sign In</a>
                                    <a href="login.php?mode=register" class="btn-fg-primary btn-sm px-3 py-1">Create Account</a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form id="fraud-report-form" novalidate>
                            
                            <!-- Subsection 1: Core Incident Overview -->
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge rounded-circle p-2" style="background-color: var(--fg-navy-primary); color: #fff;">1</span>
                                <h6 class="fw-bold text-uppercase mb-0" style="color: var(--fg-navy-primary); letter-spacing: 0.05em;">Incident Overview</h6>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label for="title" class="form-label-fg">Incident Headline / Summary <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-fg" id="title" name="title" required 
                                           placeholder="e.g. Unauthorised ₹85,000 debit via fake SBI KYC update SMS phishing">
                                    <div class="invalid-feedback">Please enter a concise descriptive headline.</div>
                                </div>

                                <div class="col-md-4">
                                    <label for="category" class="form-label-fg">Fraud Category <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-fg" id="category" name="category" required>
                                        <option value="" disabled selected>Select Category...</option>
                                        <?php foreach (FRAUD_CATEGORIES as $k => $c): ?>
                                            <option value="<?= $k ?>"><?= $c['label'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select an Indian fraud category.</div>
                                </div>

                                <div class="col-md-4">
                                    <label for="severity" class="form-label-fg">Reported Urgency / Severity <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-fg" id="severity" name="severity" required>
                                        <?php foreach (SEVERITY_LEVELS as $lvl => $meta): ?>
                                            <option value="<?= $lvl ?>" <?= $lvl === 'High' ? 'selected' : '' ?>>
                                                <?= $lvl ?> &mdash; <?= $meta['description'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="amount_lost" class="form-label-fg">Direct Financial Loss / Amount at Risk</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border" style="border-color: var(--fg-border); color: var(--fg-navy-primary); font-weight: 700;">₹</span>
                                        <input type="number" step="1" min="0" class="form-control form-control-fg" id="amount_lost" name="amount_lost" placeholder="0" value="0">
                                        <input type="hidden" name="currency" value="INR">
                                    </div>
                                    <small class="text-secondary" style="font-size: 0.75rem;">Enter 0 if attempted fraud with no financial debit</small>
                                </div>

                                <div class="col-md-4">
                                    <label for="incident_date" class="form-label-fg">Date & Time of Occurrence <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control form-control-fg" id="incident_date" name="incident_date" required>
                                </div>
                            </div>

                            <!-- Subsection 2: Narrative & Digital Artifacts -->
                            <div class="d-flex align-items-center gap-2 mb-3 pt-3 border-top" style="border-color: var(--fg-border-light) !important;">
                                <span class="badge rounded-circle p-2" style="background-color: var(--fg-navy-primary); color: #fff;">2</span>
                                <h6 class="fw-bold text-uppercase mb-0" style="color: var(--fg-navy-primary); letter-spacing: 0.05em;">Narrative & Digital Indicators (IOCs)</h6>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label for="description" class="form-label-fg">Comprehensive Incident Narrative <span class="text-danger">*</span></label>
                                    <textarea class="form-control form-control-fg" id="description" name="description" rows="4" required
                                              placeholder="Explain chronologically what happened, communication channels used (WhatsApp, call, Telegram, SMS), bank accounts or payment gateways involved, and whether you called 1930..."></textarea>
                                    <div class="invalid-feedback">Please describe how the fraud occurred.</div>
                                </div>

                                <div class="col-12">
                                    <label for="suspect_details" class="form-label-fg">Suspect Indicators & Evidence Artifacts (IOCs)</label>
                                    <textarea class="form-control form-control-fg font-monospace" id="suspect_details" name="suspect_details" rows="3"
                                              placeholder="List suspect phone numbers, fraudulent UPI IDs, phishing URLs, mule bank account numbers, or UTR transaction numbers..."></textarea>
                                    <small class="text-secondary" style="font-size: 0.75rem;">Example: <code>Suspect Phone: +91 98000 11220 | Phishing URL: https://sbi-kyc-verify.xyz | Beneficiary UPI: scammer@axisbank | UTR: 412093847120</code></small>
                                </div>
                            </div>

                            <!-- Subsection 3: Reporter Contact Details -->
                            <div class="d-flex align-items-center gap-2 mb-3 pt-3 border-top" style="border-color: var(--fg-border-light) !important;">
                                <span class="badge rounded-circle p-2" style="background-color: var(--fg-navy-primary); color: #fff;">3</span>
                                <h6 class="fw-bold text-uppercase mb-0" style="color: var(--fg-navy-primary); letter-spacing: 0.05em;">Reporter Contact Details</h6>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" value="1">
                                    <label class="form-check-label fw-semibold" for="is_anonymous" style="color: var(--fg-navy-primary);">
                                        <i class="bi bi-incognito me-1 text-warning"></i> File Anonymously / Confidential Whistleblower
                                    </label>
                                </div>
                                <small class="text-secondary" style="font-size: 0.75rem;">Toggle ON if you do not want your personal name attached to the investigation docket.</small>
                            </div>

                            <div class="row g-3 mb-4" id="reporter-contact-fields">
                                <div class="col-md-4">
                                    <label for="reporter_name" class="form-label-fg">Full Name</label>
                                    <input type="text" class="form-control form-control-fg" id="reporter_name" name="reporter_name" 
                                           placeholder="Rahul Mehta" value="<?= htmlspecialchars($user['name'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="reporter_email" class="form-label-fg">Contact Email</label>
                                    <input type="email" class="form-control form-control-fg" id="reporter_email" name="reporter_email" 
                                           placeholder="rahul.m@example.com" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="reporter_phone" class="form-label-fg">Indian Mobile Number</label>
                                    <input type="tel" class="form-control form-control-fg" id="reporter_phone" name="reporter_phone" placeholder="+91 98XXX XXXXX">
                                </div>
                            </div>

                            <!-- Submission Footer -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top" style="border-color: var(--fg-border) !important;">
                                <div class="text-secondary small">
                                    <i class="bi bi-info-circle me-1 text-primary"></i>
                                    Upon submission, an official cryptographic Case Reference ID (e.g. <code>FRD-2026-XXXXXX</code>) is issued immediately.
                                </div>
                                <button type="submit" id="btn-submit-report" class="btn-fg-danger px-4 py-2">
                                    <i class="bi bi-shield-check me-2"></i>Submit Official Fraud Report
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Indian Cybercrime Awareness Section -->
<section id="awareness" class="py-5">
    <div class="container-fluid px-lg-4">
        <div class="text-center mb-5">
            <span class="badge badge-status-investigating px-3 py-1 mb-2 text-uppercase">Threat Intelligence Briefing</span>
            <h2 class="display-6 fw-bold mb-2" style="color: var(--fg-navy-primary);">Common Online Fraud Vectors in India</h2>
            <p class="text-secondary mx-auto" style="max-width: 650px;">
                Learn how perpetrators engineer trust and manipulate fear. Understand the technical signatures of prevalent Indian cyber financial attacks.
            </p>
        </div>

        <div class="row g-4 mb-5">
            <!-- Scam 1: Digital Arrest -->
            <div class="col-md-6 col-lg-4">
                <div class="scam-guide-card">
                    <div class="scam-guide-icon danger">
                        <i class="bi bi-camera-video-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: var(--fg-navy-primary);">Digital Arrest Scam</h5>
                    <p class="text-secondary small mb-3 flex-grow-1">
                        Imposters posing as Mumbai Police, CBI, ED, or Customs place victims under continuous Skype or WhatsApp video calls ("Digital Arrest") claiming parcels contain drugs or passports.
                    </p>
                    <div class="p-2 rounded bg-light border text-secondary small">
                        <strong class="text-danger d-block mb-1"><i class="bi bi-shield-x me-1"></i>Crucial Rule:</strong>
                        Indian law enforcement NEVER conducts court hearings or arrests citizens via video call. Hang up and dial 1930.
                    </div>
                </div>
            </div>

            <!-- Scam 2: UPI QR Scam -->
            <div class="col-md-6 col-lg-4">
                <div class="scam-guide-card">
                    <div class="scam-guide-icon danger">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: var(--fg-navy-primary);">UPI "Scan to Receive" Fraud</h5>
                    <p class="text-secondary small mb-3 flex-grow-1">
                        Scammers on OLX or marketplace apps send QR codes claiming scanning them will credit money into your account for goods or advance deposits.
                    </p>
                    <div class="p-2 rounded bg-light border text-secondary small">
                        <strong class="text-danger d-block mb-1"><i class="bi bi-shield-x me-1"></i>Crucial Rule:</strong>
                        Entering your UPI PIN or scanning a QR code ALWAYS DEBITS your account. You NEVER enter a PIN to receive funds.
                    </div>
                </div>
            </div>

            <!-- Scam 3: Fake KYC & Bill Disconnection -->
            <div class="col-md-6 col-lg-4">
                <div class="scam-guide-card">
                    <div class="scam-guide-icon warning">
                        <i class="bi bi-chat-left-dots-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: var(--fg-navy-primary);">Fake KYC & Electricity SMS</h5>
                    <p class="text-secondary small mb-3 flex-grow-1">
                        Phishing SMS claiming your bank account or electricity supply will be terminated tonight unless you call an unauthorized helpline or click an APK link.
                    </p>
                    <div class="p-2 rounded bg-light border text-secondary small">
                        <strong class="text-warning d-block mb-1" style="color: #8C5911 !important;"><i class="bi bi-shield-exclamation me-1"></i>Crucial Rule:</strong>
                        Never install remote support apps (AnyDesk, QuickSupport) or click links from 10-digit mobile numbers claiming to be DISCOMs or banks.
                    </div>
                </div>
            </div>

            <!-- Scam 4: Bogus Trading APKs -->
            <div class="col-md-6 col-lg-4">
                <div class="scam-guide-card">
                    <div class="scam-guide-icon danger">
                        <i class="bi bi-graph-down-arrow"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: var(--fg-navy-primary);">Bogus Stock & Crypto APKs</h5>
                    <p class="text-secondary small mb-3 flex-grow-1">
                        Victims are lured into WhatsApp / Telegram groups by fake SEBI analysts and instructed to sideload unverified APKs showing fictitious 400% profits.
                    </p>
                    <div class="p-2 rounded bg-light border text-secondary small">
                        <strong class="text-danger d-block mb-1"><i class="bi bi-shield-x me-1"></i>Crucial Rule:</strong>
                        Only trade through SEBI-registered brokers listed on BSE / NSE portals. Never download investment APKs outside Google Play.
                    </div>
                </div>
            </div>

            <!-- Scam 5: Search Engine Helpline Spoofing -->
            <div class="col-md-6 col-lg-4">
                <div class="scam-guide-card">
                    <div class="scam-guide-icon warning">
                        <i class="bi bi-telephone-x-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: var(--fg-navy-primary);">Fake Customer Care Numbers</h5>
                    <p class="text-secondary small mb-3 flex-grow-1">
                        Cybercriminals manipulate Google business listings for banks, airlines, couriers, and wine shops with their personal mobile numbers.
                    </p>
                    <div class="p-2 rounded bg-light border text-secondary small">
                        <strong class="text-warning d-block mb-1" style="color: #8C5911 !important;"><i class="bi bi-shield-exclamation me-1"></i>Crucial Rule:</strong>
                        Only dial numbers printed on the back of your physical ATM card or verified official banking websites.
                    </div>
                </div>
            </div>

            <!-- Scam 6: Telegram Part-Time Tasks -->
            <div class="col-md-6 col-lg-4">
                <div class="scam-guide-card">
                    <div class="scam-guide-icon info">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: var(--fg-navy-primary);">Part-Time Job / Like Tasks</h5>
                    <p class="text-secondary small mb-3 flex-grow-1">
                        Offers claiming ₹3,000–₹8,000 daily for liking YouTube videos or rating hotels. Scammers pay initial micro-rewards, then demand deposit investments.
                    </p>
                    <div class="p-2 rounded bg-light border text-secondary small">
                        <strong class="text-primary d-block mb-1"><i class="bi bi-info-circle me-1"></i>Crucial Rule:</strong>
                        Legitimate companies do not recruit via unsolicited WhatsApp messages or require deposits to withdraw earned wages.
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency 3-Step Action Guide -->
        <div class="fg-card p-4 p-lg-5" style="background-color: var(--fg-navy-primary); color: #FFFFFF;">
            <div class="row align-items-center g-4">
                <div class="col-lg-4">
                    <span class="badge" style="background-color: var(--fg-danger); color: #fff;">Emergency Protocol</span>
                    <h3 class="fw-bold text-white mt-2 mb-2">What to do if money was stolen?</h3>
                    <p class="small text-secondary mb-0" style="color: #B6C6D0 !important;">
                        The first 2 hours ("Golden Hour") are critical to freeze funds before they pass through multiple mule accounts.
                    </p>
                </div>
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 rounded h-100" style="background-color: #1E4055; border: 1px solid #2B5773;">
                                <div class="fw-bold text-warning mb-1"><i class="bi bi-1-circle me-1"></i>Step 1: Call 1930</div>
                                <div class="small" style="color: #DCE3E7;">Dial 1930 immediately to trigger an automatic lien on the beneficiary bank account.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded h-100" style="background-color: #1E4055; border: 1px solid #2B5773;">
                                <div class="fw-bold text-white mb-1"><i class="bi bi-2-circle me-1"></i>Step 2: cybercrime.gov.in</div>
                                <div class="small" style="color: #DCE3E7;">Register an official FIR acknowledgement number on the National Cybercrime Portal.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded h-100" style="background-color: #1E4055; border: 1px solid #2B5773;">
                                <div class="fw-bold text-success mb-1"><i class="bi bi-3-circle me-1"></i>Step 3: FraudGuard Docket</div>
                                <div class="small" style="color: #DCE3E7;">Ingest IOCs, suspect numbers, and UTRs on FraudGuard for SOC intelligence analysis.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 5: Trust, Capabilities & Verification Architecture -->
<section id="capabilities" class="py-5" style="background-color: #FFFFFF; border-top: 1px solid var(--fg-border);">
    <div class="container-fluid px-lg-4">
        <div class="row align-items-center g-4 justify-content-between mb-5">
            <div class="col-lg-6">
                <span class="badge badge-status-resolved px-3 py-1 mb-2 text-uppercase">Platform Architecture</span>
                <h2 class="display-6 fw-bold mb-3" style="color: var(--fg-navy-primary);">Transparent Security Intelligence</h2>
                <p class="text-secondary">
                    FraudGuard is engineered as an honest, human-built cybersecurity tool. We do not make exaggerated claims of automated miracles or fabricated success metrics.
                </p>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-3">
                        <div class="text-success fs-5"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <strong style="color: var(--fg-navy-primary);">Real-Time SOC Triage Stream:</strong>
                            <div class="small text-secondary">Every reported case is automatically indexed with severity risk scoring and streamed live into the SOC monitoring console every 3 seconds.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="text-success fs-5"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <strong style="color: var(--fg-navy-primary);">Cryptographic Reference Dockets:</strong>
                            <div class="small text-secondary">Reporters receive an immutable Reference ID (e.g. <code>FRD-2026-A83F12</code>) to follow the public investigation lifecycle and officer updates.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="text-success fs-5"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <strong style="color: var(--fg-navy-primary);">Zero-Phishing Heuristic Verifier:</strong>
                            <div class="small text-secondary">Rapid client-side parsing of text, phone lines, and UPI VPAs against documented Indian fraud vectors without collecting private credentials.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="p-4 rounded" style="background-color: #F8FAFB; border: 1px solid var(--fg-border);">
                    <h5 class="fw-bold mb-3" style="color: var(--fg-navy-primary);"><i class="bi bi-building me-2 text-primary"></i>Official Indian Authorities</h5>
                    <p class="small text-secondary mb-4">
                        FraudGuard references standards and guidelines issued by official statutory bodies:
                    </p>
                    <div class="d-flex flex-column gap-2 small">
                        <div class="d-flex justify-content-between p-2 rounded bg-white border">
                            <span class="fw-semibold">I4C (Indian Cyber Crime Coordination Centre)</span>
                            <span class="text-muted">MHA, New Delhi</span>
                        </div>
                        <div class="d-flex justify-content-between p-2 rounded bg-white border">
                            <span class="fw-semibold">Reserve Bank of India (RBI Sachet)</span>
                            <span class="text-muted">Banking Regulations</span>
                        </div>
                        <div class="d-flex justify-content-between p-2 rounded bg-white border">
                            <span class="fw-semibold">NPCI (National Payments Corporation of India)</span>
                            <span class="text-muted">UPI Security</span>
                        </div>
                        <div class="d-flex justify-content-between p-2 rounded bg-white border">
                            <span class="fw-semibold">CERT-In (Indian Computer Emergency Response Team)</span>
                            <span class="text-muted">National Advisory</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="assets/js/report.js"></script>

<!-- Interactive Verifier Client Script -->
<script>
function setVerifierSample(type, text) {
    if (type === 'msg') {
        document.getElementById('verify-msg-input').value = text;
        runVerification('message');
    } else if (type === 'upi') {
        document.getElementById('verify-upi-input').value = text;
        runVerification('upi');
    } else if (type === 'phone') {
        document.getElementById('verify-phone-input').value = text;
        runVerification('phone');
    }
}

function runVerification(type) {
    let inputVal = '';
    if (type === 'message') inputVal = document.getElementById('verify-msg-input')?.value;
    else if (type === 'upi') inputVal = document.getElementById('verify-upi-input')?.value;
    else if (type === 'phone') inputVal = document.getElementById('verify-phone-input')?.value;

    const res = App.verifyIndicator(type, inputVal);
    const box = document.getElementById('verifier-result');
    const iconWrap = document.getElementById('verifier-icon-wrap');
    const titleEl = document.getElementById('verifier-title');
    const tagEl = document.getElementById('verifier-risk-tag');
    const descEl = document.getElementById('verifier-desc');
    const actEl = document.getElementById('verifier-action');

    if (!box) return;

    if (res.status === 'empty') {
        alert('Please enter text to verify.');
        return;
    }

    box.className = 'verifier-result-box ' + res.status;
    box.style.display = 'block';

    if (res.status === 'danger') {
        iconWrap.innerHTML = '<i class="bi bi-exclamation-octagon-fill text-danger"></i>';
        titleEl.textContent = res.title;
        tagEl.className = 'badge badge-sev-critical';
        tagEl.textContent = `Risk Score: ${res.score}/100 (HIGH RISK)`;
        descEl.innerHTML = res.reasons.map(r => `<div class="mb-1">&bull; ${r}</div>`).join('');
        actEl.innerHTML = `<strong>Immediate Guidance:</strong> Do NOT transfer funds, do NOT share OTP, and do NOT install any remote apps. <a href="#report-section" class="fw-bold text-danger ms-2"><i class="bi bi-file-earmark-plus"></i> Ingest Incident Docket Below</a>`;
        App.playAlertSound('critical');
    } else if (res.status === 'suspicious') {
        iconWrap.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-warning"></i>';
        titleEl.textContent = res.title;
        tagEl.className = 'badge badge-sev-high';
        tagEl.textContent = `Risk Score: ${res.score}/100 (SUSPICIOUS)`;
        descEl.innerHTML = res.reasons.map(r => `<div class="mb-1">&bull; ${r}</div>`).join('');
        actEl.innerHTML = `<strong>Guidance:</strong> Verify with the official entity directly using verified phone numbers before proceeding. <a href="#report-section" class="fw-bold text-primary ms-2">File Docket if Defrauded</a>`;
        App.playAlertSound('ping');
    } else {
        iconWrap.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
        titleEl.textContent = res.title;
        tagEl.className = 'badge badge-sev-low';
        tagEl.textContent = `Risk Score: ${res.score}/100 (NO MATCH)`;
        descEl.textContent = res.details || res.reasons?.join(' ') || 'No known fraudulent threat signatures detected.';
        actEl.innerHTML = `<strong>Security Tip:</strong> Legitimate banks and government officials never demand immediate funds transfer or OTP over phone calls.`;
        App.playAlertSound('ping');
    }

    box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
