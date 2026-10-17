/**
 * FraudGuard - Live SOC Dashboard Controller
 * Real-time 3s polling, Chart.js updates, modals, and CSV exports
 */

class DashboardController {
    constructor() {
        this.pollInterval = 3000; // 3 seconds real-time poll
        this.pollTimer = null;
        this.lastMaxId = 0;
        this.isStreaming = true;
        this.charts = {
            trend: null,
            category: null
        };

        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadInitialData();
        await this.loadCharts();
        this.startStreaming();
    }

    bindEvents() {
        // Search & Filters
        document.getElementById('filter-search')?.addEventListener('input', this.debounce(() => this.reloadTable(), 300));
        document.getElementById('filter-category')?.addEventListener('change', () => this.reloadTable());
        document.getElementById('filter-severity')?.addEventListener('change', () => this.reloadTable());
        document.getElementById('filter-status')?.addEventListener('change', () => this.reloadTable());

        // Stream Pause/Resume Toggle
        const streamToggle = document.getElementById('toggle-stream-btn');
        if (streamToggle) {
            streamToggle.addEventListener('click', () => {
                this.isStreaming = !this.isStreaming;
                if (this.isStreaming) {
                    this.startStreaming();
                    streamToggle.innerHTML = '<i class="bi bi-pause-fill me-1"></i>Pause Stream';
                    streamToggle.className = 'btn btn-sm btn-outline-success';
                    document.getElementById('stream-beacon')?.classList.remove('bg-secondary');
                    document.getElementById('stream-beacon')?.classList.add('bg-success');
                    document.getElementById('stream-status-text').textContent = 'Live Feed Active (3s Polling)';
                } else {
                    clearInterval(this.pollTimer);
                    streamToggle.innerHTML = '<i class="bi bi-play-fill me-1"></i>Resume Stream';
                    streamToggle.className = 'btn btn-sm btn-outline-warning';
                    document.getElementById('stream-beacon')?.classList.remove('bg-success');
                    document.getElementById('stream-beacon')?.classList.add('bg-secondary');
                    document.getElementById('stream-status-text').textContent = 'Stream Paused';
                }
            });
        }

        // Audio Toggle
        const audioToggle = document.getElementById('toggle-audio-btn');
        if (audioToggle) {
            audioToggle.addEventListener('click', () => {
                App.audioEnabled = !App.audioEnabled;
                audioToggle.innerHTML = App.audioEnabled 
                    ? '<i class="bi bi-volume-up-fill me-1"></i>Alarm On' 
                    : '<i class="bi bi-volume-mute-fill me-1"></i>Alarm Muted';
                audioToggle.className = App.audioEnabled ? 'btn btn-sm btn-outline-secondary' : 'btn btn-sm btn-outline-danger';
            });
        }

        // Triage Form Submit
        document.getElementById('form-triage')?.addEventListener('submit', (e) => this.handleTriageSubmit(e));

        // Quick Note Form Submit
        document.getElementById('form-add-note')?.addEventListener('submit', (e) => this.handleAddNoteSubmit(e));

        // Export CSV
        document.getElementById('btn-export-csv')?.addEventListener('click', () => this.exportTableToCSV());
    }

    async loadInitialData() {
        await this.reloadTable();
        await this.refreshKPIs();
    }

    async refreshKPIs() {
        try {
            const res = await fetch('api/stats.php');
            const data = await res.json();
            if (data.success) {
                const s = data.summary;
                document.getElementById('kpi-total').textContent = s.total_incidents.toLocaleString('en-IN');
                document.getElementById('kpi-active').textContent = s.active_incidents.toLocaleString('en-IN');
                document.getElementById('kpi-critical').textContent = s.critical_active.toLocaleString('en-IN');
                document.getElementById('kpi-loss').textContent = App.formatCurrency(s.total_loss, 'INR');

                if (s.critical_active > 0) {
                    document.getElementById('stream-beacon')?.classList.add('critical');
                } else {
                    document.getElementById('stream-beacon')?.classList.remove('critical');
                }
            }
        } catch (e) {
            console.error('Failed to load KPIs:', e);
        }
    }

    async reloadTable() {
        const search = document.getElementById('filter-search')?.value || '';
        const category = document.getElementById('filter-category')?.value || '';
        const severity = document.getElementById('filter-severity')?.value || '';
        const status = document.getElementById('filter-status')?.value || '';

        const params = new URLSearchParams({ search, category, severity, status, limit: 50 });
        const tbody = document.getElementById('incidents-table-body');
        if (!tbody) return;

        try {
            const res = await fetch(`api/incidents.php?${params.toString()}`);
            const data = await res.json();

            if (data.success) {
                tbody.innerHTML = '';
                if (data.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-secondary"><i class="bi bi-inbox me-2"></i>No incidents match the active search or filters.</td></tr>`;
                    return;
                }

                data.data.forEach(inc => {
                    if (inc.id > this.lastMaxId) {
                        this.lastMaxId = inc.id;
                    }
                    tbody.appendChild(this.createRowElement(inc));
                });
            }
        } catch (e) {
            console.error('Failed to reload table:', e);
        }
    }

    createRowElement(inc, isNew = false) {
        const tr = document.createElement('tr');
        tr.id = `incident-row-${inc.id}`;
        if (isNew) {
            tr.className = 'row-flash-new';
        }

        const catMeta = inc.category_meta || { label: inc.category, icon: 'bi-shield' };

        tr.innerHTML = `
            <td>
                <span class="font-monospace fw-bold" style="color: var(--fg-navy-primary); font-size: 0.88rem;">${inc.reference_id}</span>
                <div class="text-secondary small" style="font-size: 0.75rem;">${new Date(inc.created_at).toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' })}</div>
            </td>
            <td>
                <div class="fw-semibold" style="color: var(--fg-navy-primary); max-width: 320px;">${this.escapeHtml(inc.title)}</div>
                <div class="text-secondary small" style="font-size: 0.78rem;"><i class="bi ${catMeta.icon} me-1 text-primary"></i>${catMeta.label}</div>
            </td>
            <td>${App.getSeverityBadge(inc.severity)}</td>
            <td>${App.getRiskMeter(inc.risk_score)}</td>
            <td class="font-monospace text-end fw-bold" style="color: var(--fg-accent-green); font-size: 0.9rem;">${App.formatCurrency(inc.amount_lost, inc.currency || 'INR')}</td>
            <td>${App.getStatusBadge(inc.status)}</td>
            <td>
                <span class="small ${inc.assigned_investigator !== 'Unassigned' ? 'fw-semibold text-dark' : 'text-muted'}">
                    <i class="bi bi-person-badge me-1"></i>${this.escapeHtml(inc.assigned_investigator || 'Unassigned')}
                </span>
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-secondary me-1 py-0 px-2" onclick="dashboard.openDetailModal(${inc.id})" title="Inspect Incident Dossier">
                    <i class="bi bi-eye"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" onclick="dashboard.openTriageModal(${inc.id})" title="Triage Incident">
                    <i class="bi bi-pencil-square"></i>
                </button>
            </td>
        `;
        return tr;
    }

    startStreaming() {
        if (this.pollTimer) clearInterval(this.pollTimer);
        this.pollTimer = setInterval(() => this.pollStream(), this.pollInterval);
    }

    async pollStream() {
        if (!this.isStreaming) return;

        try {
            const res = await fetch(`api/stream.php?last_id=${this.lastMaxId}`);
            const data = await res.json();

            if (data.success && data.new_incidents_count > 0) {
                // Audio alert
                if (data.has_critical) {
                    App.playAlertSound('critical');
                } else {
                    App.playAlertSound('ping');
                }

                const tbody = document.getElementById('incidents-table-body');
                data.new_incidents.forEach(inc => {
                    if (inc.id > this.lastMaxId) {
                        this.lastMaxId = inc.id;
                    }

                    // Check if already in DOM
                    if (!document.getElementById(`incident-row-${inc.id}`)) {
                        const row = this.createRowElement(inc, true);
                        if (tbody.firstChild) {
                            tbody.insertBefore(row, tbody.firstChild);
                        } else {
                            tbody.appendChild(row);
                        }

                        // Fire Toast
                        App.showToast(
                            `NEW INCIDENT DETECTED (${inc.severity.toUpperCase()})`,
                            `[${inc.reference_id}] ${inc.title} — Exposure: ${App.formatCurrency(inc.amount_lost, 'INR')}`,
                            inc.severity === 'Critical' ? 'danger' : 'warning',
                            inc.reference_id
                        );
                    }
                });

                // Update metrics & charts
                this.refreshKPIs();
                this.updateChartsIncremental();
            }
        } catch (e) {
            console.warn('Real-time polling error:', e);
        }
    }

    async openDetailModal(id) {
        try {
            const res = await fetch(`api/incident_detail.php?id=${id}`);
            const json = await res.json();
            if (!json.success) return alert(json.error);

            const inc = json.data;
            document.getElementById('modal-detail-ref').textContent = inc.reference_id;
            document.getElementById('modal-detail-title').textContent = inc.title;
            document.getElementById('modal-detail-cat').innerHTML = `<i class="bi ${inc.category_meta.icon} me-1 text-primary"></i>${inc.category_meta.label}`;
            document.getElementById('modal-detail-sev').innerHTML = App.getSeverityBadge(inc.severity);
            document.getElementById('modal-detail-status').innerHTML = App.getStatusBadge(inc.status);
            document.getElementById('modal-detail-risk').innerHTML = App.getRiskMeter(inc.risk_score);
            document.getElementById('modal-detail-loss').textContent = App.formatCurrency(inc.amount_lost, inc.currency || 'INR');
            document.getElementById('modal-detail-date').textContent = new Date(inc.incident_date).toLocaleString('en-IN');
            document.getElementById('modal-detail-desc').textContent = inc.description;
            document.getElementById('modal-detail-suspect').textContent = inc.suspect_details || 'No digital indicators or suspect artifacts recorded.';
            document.getElementById('modal-detail-reporter').textContent = inc.is_anonymous ? 'Anonymous / Confidential Whistleblower' : `${inc.reporter_name || 'N/A'} (Email: ${inc.reporter_email || 'N/A'} | Phone: ${inc.reporter_phone || 'N/A'})`;
            document.getElementById('modal-detail-investigator').textContent = inc.assigned_investigator || 'Unassigned';
            document.getElementById('modal-detail-ip').textContent = inc.ip_address || '127.0.0.1';

            // Set hidden field for adding note
            document.getElementById('note-incident-id').value = inc.id;

            // Render Timeline
            const tlContainer = document.getElementById('modal-timeline-list');
            tlContainer.innerHTML = '';
            (inc.timeline || []).forEach(tl => {
                const li = document.createElement('li');
                li.className = 'timeline-item';
                li.innerHTML = `
                    <div class="timeline-dot ${tl.action_type}"></div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="small" style="color: var(--fg-navy-primary);">${this.escapeHtml(tl.author)}</strong>
                        <span class="text-secondary small" style="font-size: 0.75rem;">${new Date(tl.created_at).toLocaleString('en-IN')}</span>
                    </div>
                    <div class="small text-secondary">${this.escapeHtml(tl.note)}</div>
                `;
                tlContainer.appendChild(li);
            });

            const modal = new bootstrap.Modal(document.getElementById('modal-incident-detail'));
            modal.show();

        } catch (e) {
            console.error('Error opening detail modal:', e);
        }
    }

    async openTriageModal(id) {
        try {
            const res = await fetch(`api/incident_detail.php?id=${id}`);
            const json = await res.json();
            if (!json.success) return alert(json.error);

            const inc = json.data;
            document.getElementById('triage-incident-id').value = inc.id;
            document.getElementById('triage-ref-badge').textContent = inc.reference_id;
            document.getElementById('triage-incident-title').textContent = inc.title;
            document.getElementById('triage-status').value = inc.status;
            document.getElementById('triage-severity').value = inc.severity;
            document.getElementById('triage-investigator').value = inc.assigned_investigator !== 'Unassigned' ? inc.assigned_investigator : '';
            document.getElementById('triage-note').value = '';

            const modal = new bootstrap.Modal(document.getElementById('modal-triage'));
            modal.show();

        } catch (e) {
            console.error('Error opening triage modal:', e);
        }
    }

    async handleTriageSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('triage-incident-id').value;
        const status = document.getElementById('triage-status').value;
        const severity = document.getElementById('triage-severity').value;
        const investigator = document.getElementById('triage-investigator').value;
        const note = document.getElementById('triage-note').value;
        const submitBtn = document.getElementById('btn-submit-triage');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

        try {
            const res = await fetch('api/update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id,
                    status,
                    severity,
                    assigned_investigator: investigator,
                    note,
                    author: 'Lead SOC Analyst'
                })
            });

            const data = await res.json();
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modal-triage'))?.hide();
                App.showToast('TRIAGE STATUS UPDATED', `Incident ${data.data.reference_id} status updated to ${status}.`, 'info');
                await this.reloadTable();
                await this.refreshKPIs();
            } else {
                alert('Triage update failed: ' + data.error);
            }
        } catch (err) {
            console.error('Triage submit error:', err);
            alert('Failed to connect to triage API.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save Updates';
        }
    }

    async handleAddNoteSubmit(e) {
        e.preventDefault();
        const incidentId = document.getElementById('note-incident-id').value;
        const note = document.getElementById('note-content').value;
        const author = document.getElementById('note-author').value || 'SOC Analyst';
        const submitBtn = document.getElementById('btn-submit-note');

        if (!note.trim()) return;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

        try {
            const res = await fetch('api/incident_notes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ incident_id: incidentId, author, note, action_type: 'NOTE_ADDED' })
            });

            const data = await res.json();
            if (data.success) {
                document.getElementById('note-content').value = '';
                const tlContainer = document.getElementById('modal-timeline-list');
                const li = document.createElement('li');
                li.className = 'timeline-item';
                li.innerHTML = `
                    <div class="timeline-dot NOTE_ADDED"></div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="small" style="color: var(--fg-navy-primary);">${this.escapeHtml(author)}</strong>
                        <span class="text-secondary small" style="font-size: 0.75rem;">Just now</span>
                    </div>
                    <div class="small text-secondary">${this.escapeHtml(note)}</div>
                `;
                tlContainer.appendChild(li);
            } else {
                alert('Failed to add note: ' + data.error);
            }
        } catch (err) {
            console.error('Note add error:', err);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Add Finding';
        }
    }

    async loadCharts() {
        try {
            const res = await fetch('api/stats.php');
            const data = await res.json();
            if (!data.success) return;

            // Trend Chart
            const trendCtx = document.getElementById('chart-trend')?.getContext('2d');
            if (trendCtx) {
                const labels = data.charts.timeline.map(t => t.label);
                const counts = data.charts.timeline.map(t => t.count);

                this.charts.trend = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Incidents Ingested',
                            data: counts,
                            borderColor: '#1E4055',
                            backgroundColor: 'rgba(30, 64, 85, 0.08)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 2.5,
                            pointBackgroundColor: '#2E7D62',
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 1.5,
                            pointRadius: 4.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, color: '#667580', font: { family: 'Inter', size: 11 } },
                                grid: { color: '#EBF0F3' }
                            },
                            x: {
                                ticks: { color: '#667580', font: { family: 'Inter', size: 11 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // Categories Donut Chart
            const catCtx = document.getElementById('chart-category')?.getContext('2d');
            if (catCtx) {
                const catLabels = data.charts.categories.map(c => c.label);
                const catCounts = data.charts.categories.map(c => c.count);

                this.charts.category = new Chart(catCtx, {
                    type: 'doughnut',
                    data: {
                        labels: catLabels,
                        datasets: [{
                            data: catCounts,
                            backgroundColor: [
                                '#C74646', '#D99027', '#1E4055', '#2E7D62', 
                                '#142B3B', '#5A7587', '#3B82F6', '#8E9EA8'
                            ],
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
                                labels: { color: '#24313A', boxWidth: 12, font: { family: 'Inter', size: 11 } }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }

        } catch (e) {
            console.error('Failed to initialize charts:', e);
        }
    }

    async updateChartsIncremental() {
        try {
            const res = await fetch('api/stats.php');
            const data = await res.json();
            if (!data.success) return;

            if (this.charts.trend) {
                this.charts.trend.data.labels = data.charts.timeline.map(t => t.label);
                this.charts.trend.data.datasets[0].data = data.charts.timeline.map(t => t.count);
                this.charts.trend.update();
            }

            if (this.charts.category) {
                this.charts.category.data.labels = data.charts.categories.map(c => c.label);
                this.charts.category.data.datasets[0].data = data.charts.categories.map(c => c.count);
                this.charts.category.update();
            }
        } catch (e) {
            console.warn('Incremental chart update failed:', e);
        }
    }

    exportTableToCSV() {
        const rows = document.querySelectorAll('#incidents-table tr');
        let csv = [];
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll('td, th');
            for (let j = 0; j < cols.length - 1; j++) { // exclude actions column
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/(\s\s+)/gm, ' ');
                data = data.replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            csv.push(row.join(','));
        }
        const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
        const downloadLink = document.createElement('a');
        downloadLink.download = `fraudguard-incidents-${new Date().toISOString().slice(0, 10)}.csv`;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        downloadLink.remove();
    }

    debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
}

// Global instance
let dashboard;
document.addEventListener('DOMContentLoaded', () => {
    dashboard = new DashboardController();
});
