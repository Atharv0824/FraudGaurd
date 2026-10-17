/**
 * FraudGuard - Incident Reporting Controller
 */

document.addEventListener('DOMContentLoaded', () => {
    const reportForm = document.getElementById('fraud-report-form');
    const anonCheckbox = document.getElementById('is_anonymous');
    const reporterFields = document.getElementById('reporter-contact-fields');
    const submitBtn = document.getElementById('btn-submit-report');

    // Toggle anonymous fields
    if (anonCheckbox && reporterFields) {
        anonCheckbox.addEventListener('change', () => {
            if (anonCheckbox.checked) {
                reporterFields.classList.add('opacity-50');
                reporterFields.querySelectorAll('input').forEach(input => {
                    input.disabled = true;
                    input.value = '';
                });
            } else {
                reporterFields.classList.remove('opacity-50');
                reporterFields.querySelectorAll('input').forEach(input => {
                    input.disabled = false;
                });
            }
        });
    }

    // Set default datetime-local to current local time in India (IST)
    const dateInput = document.getElementById('incident_date');
    if (dateInput && !dateInput.value) {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        dateInput.value = now.toISOString().slice(0, 16);
    }

    // Handle Form Submission
    if (reportForm) {
        reportForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!reportForm.checkValidity()) {
                reportForm.classList.add('was-validated');
                return;
            }

            const formData = new FormData(reportForm);
            const payload = {
                title: formData.get('title'),
                category: formData.get('category'),
                severity: formData.get('severity'),
                amount_lost: parseFloat(formData.get('amount_lost') || 0),
                currency: formData.get('currency') || 'INR',
                incident_date: formData.get('incident_date'),
                description: formData.get('description'),
                suspect_details: formData.get('suspect_details'),
                is_anonymous: anonCheckbox?.checked ? 1 : 0,
                reporter_name: formData.get('reporter_name'),
                reporter_email: formData.get('reporter_email'),
                reporter_phone: formData.get('reporter_phone')
            };

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Encrypting & Submitting Incident...';

            try {
                const res = await fetch('api/incidents.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (data.success) {
                    App.playAlertSound('ping');
                    showSubmissionSuccess(data.data);
                } else {
                    alert('Submission error: ' + (data.error || 'Unknown error occurred.'));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-shield-check me-2"></i>Submit Official Fraud Report';
                }
            } catch (err) {
                console.error('Report submission error:', err);
                alert('Failed to connect to the fraud reporting server.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-shield-check me-2"></i>Submit Official Fraud Report';
            }
        });
    }

    function showSubmissionSuccess(incident) {
        const formCard = document.getElementById('report-card-container');
        if (!formCard) return;

        formCard.innerHTML = `
            <div class="fg-card p-4 p-md-5 text-center">
                <div class="mb-3">
                    <div class="d-inline-flex p-3 rounded-circle" style="background-color: var(--fg-green-light); border: 1px solid #B8DACB;">
                        <i class="bi bi-shield-check text-success display-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-2" style="color: var(--fg-navy-primary);">Fraud Incident Docket Ingested</h3>
                <p class="text-secondary mx-auto" style="max-width: 540px;">
                    Your report has been securely registered in the FraudGuard incident database and prioritized for triage investigation.
                </p>

                <div class="p-3 my-4 mx-auto rounded" style="max-width: 460px; background-color: #FAFCFD; border: 1px solid var(--fg-border);">
                    <div class="text-uppercase small fw-bold mb-1" style="color: var(--fg-text-secondary); letter-spacing: 0.05em;">Official Case Reference ID</div>
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <span id="case-ref-id" class="fs-2 font-monospace fw-bold" style="color: var(--fg-navy-primary);">${incident.reference_id}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('${incident.reference_id}'); alert('Reference ID copied to clipboard: ${incident.reference_id}');" title="Copy Case ID">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <div class="mt-2 small" style="color: var(--fg-text-muted);">Please save this Case ID to track investigation progress, investigator notes, and resolutions.</div>
                </div>

                <div class="row g-3 justify-content-center mb-4" style="max-width: 460px; margin: 0 auto;">
                    <div class="col-6">
                        <div class="p-2 border rounded" style="border-color: var(--fg-border); background-color: #FAFCFD;">
                            <small class="text-secondary d-block">Urgency Level</small>
                            <span class="fw-bold">${App.getSeverityBadge(incident.severity)}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded" style="border-color: var(--fg-border); background-color: #FAFCFD;">
                            <small class="text-secondary d-block">Initial Risk Score</small>
                            <span class="fw-bold" style="color: var(--fg-warning); font-size: 1.1rem;">${incident.risk_score}/100</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="track.php?ref=${incident.reference_id}" class="btn-fg-primary px-4 py-2">
                        <i class="bi bi-clock-history me-1"></i>Track Investigation Docket
                    </a>
                    <a href="dashboard.php" class="btn-fg-navy px-4 py-2">
                        <i class="bi bi-speedometer2 me-1"></i>Open SOC Monitor
                    </a>
                    <button type="button" class="btn-fg-outline px-3 py-2" onclick="location.reload()">
                        <i class="bi bi-plus-circle me-1"></i>File Another Incident
                    </button>
                </div>
            </div>
        `;
    }
});
