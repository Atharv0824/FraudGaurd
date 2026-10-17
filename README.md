# 🛡️ FraudGuard Sentinel — Real-Time Digital Fraud Reporting & SOC Incident Monitoring System (Beta Prototype)

> **Trial / Evaluation Version 1.0.0-Beta**  
> Built with **PHP (PDO)**, **JavaScript (ES6)**, **HTML5**, **CSS3 (Cyber Defense Theme)**, **Bootstrap 5.3**, and **Chart.js**.

---

## 🌟 Key Capabilities & Features

### 1. 🚨 Real-Time SOC Incident Monitoring Console (`dashboard.php`)
- **Live Feed Streaming**: Automatic background polling every 3 seconds for new incoming fraud reports and threat detections.
- **Synthesized Audio Sirens**: Web Audio API cyber siren fires for Critical and High severity fraud alerts without requiring external audio files.
- **Visual Pulse Beacon & Toast Notifications**: Glowing status indicators and live pop-up alerts with direct drill-down links.
- **Executive Metric Tiles**: Real-time counters for Total Ingested Cases, Active Triage Queue, Critical Threat Alerts, and Cumulative Financial Loss.
- **Dynamic Charts (Chart.js)**: 7-day ingestion velocity trend and category distribution doughnut chart.
- **Interactive Triage Table**: Search by reference ID, title, suspect indicator, or victim; filter by category, severity, and workflow status.
- **Incident Dossier Modal**: Comprehensive view of narrative, digital IOCs (IP, crypto wallet, suspect phone, URLs), and full chronological audit timeline.
- **Live Case Routing / Triage Modal**: Transition statuses (`New`, `Under Investigation`, `Escalated`, `Pending Action`, `Resolved`, `False Positive`), reassign investigators, and append timestamped notes.
- **1-Click Export to CSV**: Instantly export filtered fraud cases for compliance or regulatory reporting.

### 2. 📝 Incident Reporting & Triage Intake Portal (`index.php`)
- **Multi-Vector Fraud Reporting**: Phishing, Card Fraud, Account Takeover (ATO), Crypto Scams, Wire/BEC Fraud, Ransomware, and E-commerce scams.
- **Automatic Risk Scoring Engine**: Calculates initial 0–100 risk score based on severity weights, monetary loss, and vector danger.
- **Digital Artifacts & IOC Collection**: Specialized fields for suspect phone numbers, spoofed domains, phishing URLs, wallet addresses, and attacker IPs.
- **Confidential / Whistleblower Mode**: Toggle to submit anonymously without exposing personal contact info.
- **Cryptographic Case Reference ID**: Instant unique tracking identifier generated upon submission (e.g., `FRD-2026-A83F12`).

### 3. 🔍 Victim / Public Case Status Tracker (`track.php`)
- Look up investigation dockets by Case Reference ID.
- Visual 5-stage progress pipeline (`Ingested` → `Triage` → `SOC Analysis` → `Mitigation` → `Resolution`).
- Live chronological timeline showing public investigator logs and milestone updates.

### 4. 📊 Strategic Threat Intelligence & Analytics (`analytics.php`)
- Financial exposure breakdown by fraud vector (interactive bar chart).
- Severity distribution breakdown (pie chart).
- Category risk and financial loss matrix table.
- Direct quick-filter buttons into the live monitoring console.

### 5. ⚡ Live Fraud Attack Simulation Engine
- Built-in simulation trigger in the header and dashboard (`Simulate Live Fraud`).
- Injects randomized realistic attacks (e.g., Deepfake CEO audio spoofing, credential stuffing bot, ransomware extortion) to demonstrate real-time alerts, sirens, and live table updates during demos.

### 6. 🔐 Tiered Authentication & Role Separation
- **Dedicated SOC Administrator Portal (`admin-login.php`)**: Isolated Level 3 clearance sign-in designed strictly for supervisor and command staff.
  - **Admin ID**: `ADMIN-SOC-01` or `admin@fraudguard.in`
  - **Passkey**: `Admin@FraudGuard#2026` (or `Admin@1234`)
- **Investigator & Citizen Portal (`login.php` / `register.php`)**: User registration and sign-in for cyber analysts and citizens.
  - **Investigator Email**: `investigator@fraudguard.in`
  - **Password**: `User@1234`
- **Session-Aware Navbar**: Real-time profile badge displaying user initials avatar, full name, role badge (`SOC Admin` / `Investigator`), and an action dropdown with 1-click Sign Out.
- **Route Guards**: Critical monitoring centers (`dashboard.php`, `analytics.php`) strictly require active authenticated sessions.

---

## 🗄️ Database Architecture & Connectivity

The system includes dual database compatibility:

### Option A: SQLite (Default — Zero-Configuration & Ready Immediately)
- **Zero setup required**: Automatically creates `database/fraudguard.db` on first launch.
- Auto-initializes schema, creates foreign keys and performance indexes, and seeds realistic demo cases and authenticated accounts.
- Configured in [config/database.php](file:///c:/Users/ADMIN/Downloads/ezyZip/config/database.php).

### Option B: MySQL / MariaDB (XAMPP / WAMP / Remote Server)
To switch to MySQL:
1. Open [config/database.php](file:///c:/Users/ADMIN/Downloads/ezyZip/config/database.php).
2. Change `define('DB_DRIVER', 'sqlite');` to `define('DB_DRIVER', 'mysql');`.
3. Set your MySQL host, database name, user, and password:
   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_PORT', 3306);
   define('DB_NAME', 'fraudguard');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
4. Import the provided schema file [database/schema.sql](file:///c:/Users/ADMIN/Downloads/ezyZip/database/schema.sql) into MySQL using phpMyAdmin or MySQL CLI.

---

## 🚀 How to Run the Trial Prototype

### Quick Launch (Windows):
Double-click `start.bat` in this folder:
```cmd
start.bat
```
*(Or in PowerShell: `./start.ps1`)*

### Manual Launch with PHP CLI:
Run from this directory:
```bash
php -S 127.0.0.1:8000
```
Then visit in your web browser:
- **Public Fraud Prevention & Intake**: `http://127.0.0.1:8000/index.php`
- **Investigator Login & Signup**: `http://127.0.0.1:8000/login.php`
- **Dedicated SOC Administrator Portal**: `http://127.0.0.1:8000/admin-login.php`
- **Incident Monitoring Dashboard**: `http://127.0.0.1:8000/dashboard.php` *(Requires Login)*
- **Threat Analytics**: `http://127.0.0.1:8000/analytics.php` *(Requires Login)*
- **Case Tracking Portal**: `http://127.0.0.1:8000/track.php`


---

## 📁 Project Structure

```text
fraud-incident-system/
│
├── config/
│   ├── database.php        # PDO connection (SQLite auto-init + MySQL toggle)
│   └── constants.php       # Categories, severities, risk calculation formula
│
├── api/
│   ├── incidents.php       # GET (list with filters) / POST (create incident)
│   ├── incident_detail.php # GET single incident dossier + timeline
│   ├── update_status.php   # POST triage update (status, severity, assignee)
│   ├── incident_notes.php  # POST investigation note to timeline
│   ├── stats.php           # Aggregated statistics & Chart.js payloads
│   ├── stream.php          # Real-time delta streaming & alert monitor
│   └── simulate.php        # Synthetic attack generator for live demonstration
│
├── assets/
│   ├── css/
│   │   └── style.css       # Custom modern cybersecurity dark theme
│   └── js/
│       ├── app.js          # Audio synth, toast manager, formatting helpers
│       ├── dashboard.js    # Live stream controller, Chart.js, triage modals
│       └── report.js       # Reporting form intake & receipt generator
│
├── database/
│   ├── schema.sql          # Clean DDL schema for MySQL & MariaDB
│   ├── seed.php            # CLI database seeding & health verification
│   └── fraud_sentinel.sqlite # Auto-created local SQLite database
│
├── includes/
│   ├── header.php          # Responsive Bootstrap 5 navbar & live beacon
│   └── footer.php          # System footer & toast alert anchor
│
├── index.php               # Public Fraud Reporting Portal
├── dashboard.php           # SOC Real-Time Incident Monitoring Center
├── track.php               # Public Case Status & Timeline Tracker
├── analytics.php           # Threat Intelligence & Fraud Vector Analytics
├── start.bat               # 1-Click Windows batch launcher
├── start.ps1               # 1-Click PowerShell launcher
└── README.md               # Complete documentation
```

---

## 📡 REST API Reference

| Endpoint | Method | Description |
|---|---|---|
| `/api/incidents.php` | `GET` | List incidents with `search`, `category`, `severity`, `status`, `limit`, `offset` |
| `/api/incidents.php` | `POST` | Create a new fraud incident report (returns Case Reference ID & Risk Score) |
| `/api/incident_detail.php?id={id}` | `GET` | Retrieve full dossier including digital IOCs and chronological audit trail |
| `/api/update_status.php` | `POST` | Triage case (updates status, adjusts severity, reassigns investigator, logs note) |
| `/api/incident_notes.php` | `POST` | Append investigation note or finding to case timeline |
| `/api/stats.php` | `GET` | Returns aggregated statistics, loss totals, and chart datasets |
| `/api/stream.php?last_id={id}` | `GET` | Real-time delta feed returning newly ingested cases and high-severity flags |
| `/api/simulate.php` | `POST` | Injects synthetic realistic fraud incident to test alerts & live stream |

---

## 🔒 Security & Best Practices Implemented
- **Parameterized SQL Statements**: 100% prepared statements via PDO to prevent SQL Injection.
- **Cross-Site Scripting (XSS) Prevention**: Output sanitization using `htmlspecialchars()` on all user-supplied data.
- **RESTful JSON Contracts**: Strict HTTP status codes (`200`, `201`, `400`, `404`, `405`, `500`).
- **Zero-Dependency Real-Time**: Clean vanilla JavaScript delta polling (3-second cadence) requiring no external WebSockets or Redis server for trial.
- **Web Audio API**: Clean synthesizer sound effects with zero external MP3 dependencies.
