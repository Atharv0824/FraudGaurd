/**
 * FraudGuard - Core Application Helpers, Audio Synthesis & Fraud Verifier
 */

const App = {
    audioEnabled: true,
    audioCtx: null,

    /**
     * Initializes the Web Audio Context on first user interaction
     */
    initAudio() {
        if (!this.audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                this.audioCtx = new AudioContext();
            }
        }
        if (this.audioCtx && this.audioCtx.state === 'suspended') {
            this.audioCtx.resume();
        }
    },

    /**
     * Synthesize clean, non-obtrusive cybersecurity chime
     */
    playAlertSound(type = 'alarm') {
        if (!this.audioEnabled) return;
        try {
            this.initAudio();
            if (!this.audioCtx) return;

            const now = this.audioCtx.currentTime;

            if (type === 'critical' || type === 'alarm') {
                const osc1 = this.audioCtx.createOscillator();
                const gain1 = this.audioCtx.createGain();
                osc1.type = 'triangle';
                osc1.frequency.setValueAtTime(880, now);
                osc1.frequency.setValueAtTime(1174.66, now + 0.12);
                gain1.gain.setValueAtTime(0.2, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);

                osc1.connect(gain1);
                gain1.connect(this.audioCtx.destination);
                osc1.start(now);
                osc1.stop(now + 0.35);

                setTimeout(() => {
                    if (!this.audioCtx) return;
                    const osc2 = this.audioCtx.createOscillator();
                    const gain2 = this.audioCtx.createGain();
                    const t = this.audioCtx.currentTime;
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(1318.51, t);
                    gain2.gain.setValueAtTime(0.15, t);
                    gain2.gain.exponentialRampToValueAtTime(0.001, t + 0.25);
                    osc2.connect(gain2);
                    gain2.connect(this.audioCtx.destination);
                    osc2.start(t);
                    osc2.stop(t + 0.25);
                }, 180);

            } else {
                const osc = this.audioCtx.createOscillator();
                const gain = this.audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(659.25, now);
                osc.frequency.exponentialRampToValueAtTime(880, now + 0.15);
                gain.gain.setValueAtTime(0.15, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.28);
                osc.connect(gain);
                gain.connect(this.audioCtx.destination);
                osc.start(now);
                osc.stop(now + 0.28);
            }
        } catch (e) {
            console.warn('Audio playback error:', e);
        }
    },

    /**
     * Show Bootstrap 5 Toast using clean solid color scheme
     */
    showToast(title, message, variant = 'danger', incidentRef = null) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Math.random().toString(36).substring(2, 9);
        const icon = variant === 'danger' ? 'bi-shield-exclamation text-danger' : (variant === 'success' ? 'bi-shield-check text-success' : 'bi-info-circle text-primary');
        const borderClass = variant === 'danger' ? 'toast-fg' : (variant === 'success' ? 'toast-fg toast-success' : 'toast-fg toast-info');

        const toastHtml = `
            <div id="${toastId}" class="toast ${borderClass} align-items-center mb-2 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex p-3">
                    <div class="toast-body p-0 flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi ${icon} me-2 fs-5"></i>
                            <strong class="me-auto text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em; color: #142B3B;">${this.escapeHtml(title)}</strong>
                            <small class="text-secondary ms-2" style="font-size: 0.75rem;">${new Date().toLocaleTimeString()}</small>
                        </div>
                        <div class="small mb-2" style="color: #667580;">${this.escapeHtml(message)}</div>
                        ${incidentRef ? `<a href="dashboard.php?search=${incidentRef}" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 0.75rem;"><i class="bi bi-box-arrow-up-right me-1"></i>Inspect Case</a>` : ''}
                    </div>
                    <button type="button" class="btn-close ms-2 me-0 mt-0" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', toastHtml);
        const toastEl = document.getElementById(toastId);
        setTimeout(() => {
            if (toastEl) {
                toastEl.classList.remove('show');
                setTimeout(() => toastEl.remove(), 350);
            }
        }, 7000);
    },

    /**
     * Trigger Live Simulation Attack
     */
    async simulateAttack(btnElement = null) {
        if (btnElement) {
            btnElement.disabled = true;
            btnElement.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Injecting...';
        }

        try {
            const res = await fetch('api/simulate.php', { method: 'POST' });
            const data = await res.json();

            if (data.success) {
                App.playAlertSound('critical');
                App.showToast(
                    'SIMULATED ATTACK DETECTED', 
                    `[${data.incident.reference_id}] ${data.incident.title} (₹${Number(data.incident.amount_lost).toLocaleString('en-IN')})`, 
                    'danger', 
                    data.incident.reference_id
                );
            } else {
                alert('Simulation failed: ' + (data.error || 'Unknown error'));
            }
        } catch (err) {
            console.error('Simulation error:', err);
            alert('Failed to connect to simulation API.');
        } finally {
            if (btnElement) {
                btnElement.disabled = false;
                btnElement.innerHTML = '<i class="bi bi-lightning-charge-fill text-warning me-1"></i>Simulate Attack';
            }
        }
    },

    /**
     * Format currency in Indian Rupees format (e.g. ₹72,00,000)
     */
    formatCurrency(amount, currency = 'INR') {
        const num = parseFloat(amount || 0);
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: currency || 'INR',
            maximumFractionDigits: 0
        }).format(num);
    },

    /**
     * Get Severity Badge HTML
     */
    getSeverityBadge(sev) {
        switch ((sev || '').toLowerCase()) {
            case 'critical':
                return `<span class="badge badge-sev-critical"><i class="bi bi-exclamation-octagon-fill me-1"></i>Critical</span>`;
            case 'high':
                return `<span class="badge badge-sev-high"><i class="bi bi-exclamation-triangle-fill me-1"></i>High</span>`;
            case 'medium':
                return `<span class="badge badge-sev-medium"><i class="bi bi-info-circle me-1"></i>Medium</span>`;
            default:
                return `<span class="badge badge-sev-low">Low</span>`;
        }
    },

    /**
     * Get Status Badge HTML
     */
    getStatusBadge(status) {
        switch (status) {
            case 'New':
                return `<span class="badge badge-status-new px-2 py-1">${status}</span>`;
            case 'Under Investigation':
                return `<span class="badge badge-status-investigating px-2 py-1">${status}</span>`;
            case 'Escalated':
                return `<span class="badge badge-status-escalated px-2 py-1">${status}</span>`;
            case 'Pending Action':
                return `<span class="badge badge-status-investigating px-2 py-1">${status}</span>`;
            case 'Resolved':
                return `<span class="badge badge-status-resolved px-2 py-1"><i class="bi bi-check2 me-1"></i>Resolved</span>`;
            case 'False Positive':
                return `<span class="badge badge-status-neutral px-2 py-1">${status}</span>`;
            default:
                return `<span class="badge badge-status-neutral px-2 py-1">${status}</span>`;
        }
    },

    /**
     * Render Risk Score Bar
     */
    getRiskMeter(score) {
        const num = parseInt(score, 10) || 0;
        let color = '#2E7D62';
        if (num >= 80) color = '#C74646';
        else if (num >= 60) color = '#D99027';
        else if (num >= 40) color = '#1E4055';

        return `
            <div class="risk-meter" title="Risk Score: ${num}/100">
                <span style="color: ${color}">${num}</span>
                <div class="risk-bar">
                    <div class="risk-fill" style="width: ${num}%; background-color: ${color}"></div>
                </div>
            </div>
        `;
    },

    escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    },

    /**
     * Interactive Fraud Verification Algorithm for Indian Scenarios
     */
    verifyIndicator(type, inputVal) {
        const val = (inputVal || '').trim().toLowerCase();
        if (!val) {
            return {
                status: 'empty',
                message: 'Please enter details to verify.'
            };
        }

        let riskLevel = 'safe'; // 'safe', 'suspicious', 'danger'
        let reasons = [];
        let score = 15;

        if (type === 'message') {
            // Check for Digital Arrest
            if (val.includes('digital arrest') || val.includes('cbi') || val.includes('mumbai police') || val.includes('customs department') || val.includes('parcel containing drugs') || val.includes('illegal passport')) {
                riskLevel = 'danger';
                score = 98;
                reasons.push("Matches known 'Digital Arrest' scam pattern. Indian law enforcement NEVER arrests or conducts trial over Skype, WhatsApp, or video call.");
            }
            // Check for Electricity Disconnection Scams
            if (val.includes('electricity') && (val.includes('disconnect') || val.includes('cut') || val.includes('unpaid bill') || val.includes('9:30 pm') || val.includes('tonight'))) {
                riskLevel = 'danger';
                score = 92;
                reasons.push("Matches fraudulent electricity bill disconnection threat. DISCOMs do not send SMS from personal numbers or demand instant remote APK installations.");
            }
            // Check for KYC Expiry Scams
            if ((val.includes('kyc') || val.includes('pan card') || val.includes('aadhaar')) && (val.includes('block') || val.includes('suspended') || val.includes('expire') || val.includes('click here'))) {
                riskLevel = 'danger';
                score = 90;
                reasons.push("Classic KYC phishing pattern. Banks never send links with countdown timers threatening account suspension.");
            }
            // Check for Part-Time Task Scams
            if (val.includes('like youtube') || val.includes('telegram task') || val.includes('part-time job') || val.includes('earn 3000') || val.includes('prepaid rating')) {
                riskLevel = 'danger';
                score = 94;
                reasons.push("Matches Telegram task / prepaid deposit fraud. Scammers pay small sums initially before demanding deposits.");
            }
            // Sideloaded APK warning
            if (val.includes('.apk') || val.includes('install app') || val.includes('quicksupport') || val.includes('anydesk') || val.includes('teamviewer')) {
                riskLevel = 'danger';
                score = 95;
                reasons.push("Requesting third-party APK installation or remote screen sharing is a primary vector for complete banking account takeover.");
            }
            // Stock Trading tips
            if (val.includes('sebi registered') || val.includes('guaranteed 200%') || val.includes('institutional allotment') || val.includes('upper circuit tips')) {
                riskLevel = 'suspicious';
                score = 80;
                reasons.push("Unregistered trading scheme indicators. High promise of guaranteed profits violates SEBI regulatory guidelines.");
            }

        } else if (type === 'upi') {
            // Check UPI ID or QR pattern
            if (val.includes('scan to receive') || val.includes('receive money') || val.includes('enter pin to receive') || val.includes('cashback')) {
                riskLevel = 'danger';
                score = 96;
                reasons.push("CRITICAL RULE: You NEVER need to enter your UPI PIN or scan a QR code to RECEIVE money. UPI PIN is only entered to SEND money.");
            }
            // Suspicious handles
            if (val.includes('refund') || val.includes('helpline') || val.includes('customer-care') || val.includes('kyc-verify')) {
                riskLevel = 'suspicious';
                score = 75;
                reasons.push("VPA handle contains deceptive keywords commonly used by imposters spoofing merchant support desks.");
            }
            // Check valid VPA syntax
            if (val.includes('@') && !val.includes(' ')) {
                if (reasons.length === 0) {
                    reasons.push("UPI VPA structure is formally valid. Always verify beneficiary name on your banking app before authorizing any funds.");
                }
            }

        } else if (type === 'phone') {
            const clean = val.replace(/[^0-9]/g, '');
            // Common scam prefixes or virtual numbers
            if (clean.startsWith('917') || clean.startsWith('918') || clean.startsWith('919') || clean.length === 10) {
                if (val.includes('customer care') || val.includes('helpline') || val.includes('support')) {
                    riskLevel = 'suspicious';
                    score = 70;
                    reasons.push("Customer care numbers displayed on search engine summaries are frequently spoofed by fraudsters. Only use phone numbers printed on your physical ATM card or official bank website.");
                } else {
                    reasons.push("Mobile number format valid. If this caller asks for OTP, UPI PIN, or bank passwords, terminate the call immediately.");
                }
            } else if (clean.startsWith('1800') || clean.startsWith('1930')) {
                riskLevel = 'safe';
                score = 10;
                reasons.push("Toll-free or official emergency helpline format.");
            }
        }

        if (reasons.length === 0) {
            return {
                status: 'safe',
                score: 15,
                title: 'No Direct High-Risk Threat Signatures Detected',
                details: 'This input did not match our catalog of known Indian fraud patterns. However, always exercise caution: never share OTP, never enter UPI PIN to receive money, and never install APK files from unknown senders.'
            };
        }

        return {
            status: riskLevel,
            score: score,
            title: riskLevel === 'danger' ? 'High Risk Scam Indicator Detected' : 'Suspicious Indicators Found',
            reasons: reasons
        };
    }
};

// Global audio activation listener on user click
document.addEventListener('click', () => {
    App.initAudio();
}, { once: true });
