# Driver Permit Management System (DPMS)

**ZAFFICO PLC — Internal documentation**

| | |
|---|---|
| **Application** | Driver Permit Management System (DPMS) |
| **Organisation** | Zambia Forestry And Forest Industries Corporation PLC (ZAFFICO) |
| **Purpose** | Issue, track, audit, and verify employee driving permits |
| **Stack** | Laravel 12, PHP 8.2+, Tailwind CSS, Alpine.js, DomPDF, SQLite/MySQL |

---

## Table of contents

1. [Overview](#1-overview)
2. [Who uses the system](#2-who-uses-the-system)
3. [Roles and permissions](#3-roles-and-permissions)
4. [Core concepts](#4-core-concepts)
5. [Installation and setup](#5-installation-and-setup)
6. [Running on a local network](#6-running-on-a-local-network)
7. [Day-to-day workflows](#7-day-to-day-workflows)
8. [Permit certificates and verification](#8-permit-certificates-and-verification)
9. [Administration](#9-administration)
10. [Reports and renewals](#10-reports-and-renewals)
11. [Technical architecture](#11-technical-architecture)
12. [Configuration reference](#12-configuration-reference)
13. [Maintenance and operations](#13-maintenance-and-operations)
14. [Security notes](#14-security-notes)
15. [Known limitations](#15-known-limitations)
16. [Troubleshooting](#16-troubleshooting)

---

## 1. Overview

DPMS is an internal web portal for ZAFFICO’s Transport / Fleet Office. Staff use it to:

- Maintain a **driver register** (employees authorised to drive company vehicles)
- **Issue permits** through a guided wizard
- **Print or email PDF certificates** to drivers (distribution is manual today)
- **Revoke or track expiry** of permits
- **Verify permits** publicly via QR code or permit number
- **Audit** changes to drivers, permits, and user accounts

Drivers **do not log in**. They receive their certificate from fleet staff (typically by email or print). Public self-registration is disabled.

---

## 2. Who uses the system

| Audience | Access |
|----------|--------|
| **System Administrator** | Full configuration, user accounts, audit logs, archives, certificate signature |
| **Fleet Management Officer** | Daily operations: drivers, issuance, renewals, reports, permit revocation |
| **Management** | Read-only oversight: dashboards, drivers, permits, reports |
| **Drivers (employees)** | No portal access; receive PDF certificate from officers |
| **Public / security** | Unauthenticated permit verification page only |

---

## 3. Roles and permissions

### Assignable roles

Administrators create accounts with one of three roles:

| Role | Code | Typical use |
|------|------|-------------|
| Administrator | `admin` | IT / system owner |
| Fleet Management Officer | `fleet_officer` | Transport desk issuing permits |
| Management | `management` | Oversight and reporting |

> **Note:** A legacy `driver` role exists in the database for old records only. Those accounts **cannot log in** and the role is not offered when creating new users.

### Permission matrix

| Feature | Admin | Fleet Officer | Management |
|---------|:-----:|:-------------:|:----------:|
| Dashboard (role-specific) | ✓ | ✓ | ✓ |
| View drivers & permits | ✓ | ✓ | ✓ |
| Register / edit / archive drivers | ✓ | ✓ | — |
| Issue permits (wizard) | ✓ | ✓ | — |
| Revoke permits | ✓ | ✓ | — |
| Renewals workspace | ✓ | ✓ | ✓ |
| Reports | ✓ | ✓ | ✓ |
| User accounts (Accounts) | ✓ | — | — |
| Settings & certificate signature | ✓ | — | — |
| Archives | ✓ | — | — |
| Audit logs | ✓ | — | — |
| Public verification (`/permits/verify`) | ✓ (anyone) | ✓ | ✓ |

---

## 4. Core concepts

### Users vs drivers

These are **separate**:

- **User** — A staff member who logs into DPMS (admin, fleet officer, or management).
- **Driver** — An employee record in the driver register. Used for permit issuance. **No login.**

A driver record holds: employee ID, full name, NRC, department, licence number/class, phone, and archive status.

### Permit lifecycle

```
[Issue wizard] → VALID → (expiry date passes) → EXPIRED
                    ↓
              [Revoke action] → REVOKED
```

- **VALID** — Active permit; expiry date is today or in the future.
- **EXPIRED** — Past expiry date (updated nightly by scheduler or on verification).
- **REVOKED** — Manually revoked by fleet staff; not auto-restored.

Each permit has a unique **permit number** (e.g. `DPMS-2026-0012`), issue/expiry dates, issuing officer, and QR code.

### Certificate vs permit record

- The **permit record** is the authoritative data in the database.
- The **PDF certificate** is a formatted export for printing or emailing. It includes holder details, status, issuer signature (if configured), and a QR code.

Certificates **do not include a driver photograph**. Identity is established through named fields, NRC, permit number, and QR verification.

---

## 5. Installation and setup

### Requirements

- PHP 8.2 or newer
- Composer
- Node.js 18+ (for building frontend assets)
- PHP extensions: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, **`gd`** (recommended for QR rendering in PDFs)
- Database: **SQLite** (default, single-server) or **MySQL/MariaDB** (recommended for multi-user production)

### First-time setup

From the project root:

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed          # Development/demo data only
php artisan storage:link       # Required for uploaded signature images
npm install && npm run build
php artisan serve
```

Open the login page in a browser. Default seeded accounts (development only — **change passwords before real use**) are documented in `database/seeders/DatabaseSeeder.php`:

| Email | Role | Default password |
|-------|------|------------------|
| `admin@zaffico.test` | Administrator | `password` |
| `fleet@zaffico.test` | Fleet Officer | `password` |
| `management@zaffico.test` | Management | `password` |

### Production checklist

- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Set a strong `APP_KEY` (from `key:generate`)
- [ ] Configure `APP_URL` to the real server address (critical for QR codes)
- [ ] Use MySQL instead of SQLite for concurrent staff usage
- [ ] Change all default passwords; create real admin accounts
- [ ] Run `npm run build` (do not rely on `npm run dev` on the server)
- [ ] Run `php artisan storage:link`
- [ ] Schedule `php artisan schedule:run` (see [Maintenance](#13-maintenance-and-operations))
- [ ] Configure backups for database and `storage/app/public`

---

## 6. Running on a local network

For a **LAN pilot** on one Windows/Linux machine:

### 1. Bind to all interfaces

`php artisan serve` listens on `127.0.0.1` by default. Other PCs cannot reach it. Use:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. Set APP_URL

In `.env`, use the host machine’s LAN IP — **not** `localhost`:

```env
APP_URL=http://192.168.1.50:8000
```

QR codes and certificate links use this URL. If it is wrong, phones and other PCs will fail verification.

### 3. Firewall

Allow inbound TCP on the chosen port (e.g. 8000) for private network profiles.

### 4. Access from other devices

Staff browse to: `http://<server-ip>:8000`

### 5. Beyond pilot

`artisan serve` is suitable for trials. For permanent deployment, use **IIS**, **Apache**, or **Nginx** with PHP-FPM on a dedicated internal server.

---

## 7. Day-to-day workflows

### Register a new driver

**Navigation:** Drivers → Add Driver

1. Enter employee number, full name, **NRC** (`NNNNNN/NN/N`, e.g. `123456/78/1` — slashes are added automatically), department, licence details, and phone.
2. Save. The driver appears in the active register.

### Issue a permit (5-step wizard)

**Navigation:** Permits → Issue Permit

| Step | Action |
|------|--------|
| **1. Select driver** | Choose an active driver from the list |
| **2. Validity** | Set issue date, expiry date, optional notes |
| **3. Approve** | Review details; officer ticks approval checkbox |
| **4. Generate** | Confirm generation; system assigns permit number and QR |
| **5. Complete** | Print PDF, view permit, or issue another |

Cancel at any time via **Cancel workflow** (clears the in-progress session).

### Distribute the certificate

On the completion screen:

1. Click **Print PDF certificate** — opens the landscape A4 PDF in a new tab.
2. Save or print the PDF.
3. **Email or hand the certificate to the driver** (no automated email in the system today).

### View or manage an existing permit

**Navigation:** Permits → All Permits (filter by status if needed)

- Open a permit for details, issuer, dates, and QR preview.
- **Revoke** — fleet officers and admins only; permanent for that permit.
- **Download certificate** — same PDF as the issuance completion step.

### Archive a driver

When an employee leaves or is suspended:

**Navigation:** Driver profile → **Archive driver**

Archived drivers are excluded from the issuance wizard. Restore from **Suspended / Archived** list when appropriate.

### Renewals

**Navigation:** Permits → Renewals (sidebar)

Lists permits expiring within a configurable window (default 60 days). Use this as a work queue to contact drivers and run the **Issue Permit** wizard again for a new permit period.

> Renewals do not auto-extend existing permits; officers issue a **new permit** through the wizard.

---

## 8. Permit certificates and verification

### PDF certificate contents

Template: `resources/views/permits/pdf-certificate.blade.php`

The certificate includes:

- ZAFFICO branding and permit number
- Holder name (prominent)
- Employee ID, NRC, licence number/class, department, phone
- Issue date, expiry date, status
- Issuing officer name and role
- **Official scanned signature** (if uploaded in Settings → Signature Upload)
- **QR code** linking to the public verification page
- Footer with print timestamp and verification notice

No driver photo is shown.

### Official signature

**Navigation:** Settings → Signature Upload (administrators only)

- Upload PNG/JPEG/WebP (max 2 MB). Transparent PNG ~170×40 px works well.
- Signature appears above the signing line on all new PDFs.
- **Remove signature** deletes the file and clears the setting.

Files are stored on the `public` disk under `certificate-signatures/`.

### Public verification

**URL:** `/permits/verify/{permit_number}`

- **No login required** — intended for QR scans at gates or checkpoints.
- Enter or scan a permit number to see status, holder name, dates, and validity.
- Status is refreshed from dates when the page loads.

Ensure `APP_URL` points to a host reachable from devices that scan QR codes (LAN IP or internal DNS).

---

## 9. Administration

### User accounts

**Navigation:** Users → Add User (administrators only)

- Public registration is **disabled**.
- Admins create accounts with name, email, role, and password.
- At least one administrator must always remain.
- Admins cannot demote themselves if they are the last admin.

### Settings

**Navigation:** Settings

| Section | Status |
|---------|--------|
| **Company Information** | Display only (persistence planned) |
| **Signature Upload** | Fully functional |
| **System Preferences** | Display only (persistence planned) |
| **Permit Design** | PDF layout edited in Blade template (developer) |

### Archives

**Navigation:** Archives (administrators only)

Read-only views of archived drivers and historical permit records.

### Audit logs

**Navigation:** Audit Logs (administrators only)

Records create/update/delete events for drivers, permits, and users, including actor, IP, timestamp, and change details. Filter by event type or subject (users, drivers, permits).

---

## 10. Reports and renewals

### Reports

**Navigation:** Reports

| Report type | Description |
|-------------|-------------|
| Active permits | Valid permits ordered by expiry |
| Expired permits | Expired or past-date permits |
| Driver list | Active drivers |
| Issuance log | All permits with issuer |

Available to admin, fleet, and management roles.

### Dashboards

Each role sees a tailored dashboard:

- **Administrator** — counts, charts, recent audit activity, upcoming expiries
- **Fleet Officer** — operational stats, recent issuance, expiring soon
- **Management** — summary stats and recent issuance (read-only)

---

## 11. Technical architecture

### High-level structure

```
Browser
   │
   ▼
Laravel (routes/web.php, controllers, policies)
   │
   ├── Models: User, Driver, Permit, AuditLog, CertificateSetting
   ├── Services: PermitQrService, PermitNumberGenerator
   ├── Observers: audit logging on Driver, Permit, User changes
   └── Views: Blade + Tailwind + Alpine.js
   │
   ▼
Database (SQLite or MySQL) + storage/app/public (QR SVGs, signatures)
```

### Key routes

| Route | Purpose |
|-------|---------|
| `/login` | Staff authentication |
| `/dashboard` | Role-based home |
| `/drivers` | Driver CRUD and archive |
| `/permits/issue/*` | Issuance wizard |
| `/permits/{id}/certificate` | PDF stream |
| `/permits/verify/{code}` | Public verification |
| `/users` | Account administration |
| `/settings` | System settings |
| `/audit-logs` | Audit trail |
| `/reports` | Operational reports |

### Data model (summary)

| Table | Purpose |
|-------|---------|
| `users` | Staff accounts and roles |
| `drivers` | Employee driver register |
| `permits` | Issued permits linked to drivers |
| `audit_logs` | Change history |
| `certificate_settings` | Official PDF signature (singleton row) |
| `sessions`, `cache`, `jobs` | Laravel infrastructure |

### Authorisation

Access is enforced with **Laravel Policies** (`DriverPolicy`, `PermitPolicy`, `UserPolicy`) and **Gates** (`viewReports`, `viewAuditLogs`). Routes under the `admin` middleware require the administrator role.

---

## 12. Configuration reference

### Important `.env` variables

| Variable | Purpose |
|----------|---------|
| `APP_NAME` | Display name (default: DPMS) |
| `APP_URL` | Base URL for links and QR codes — **must match how users reach the server** |
| `APP_ENV` | `local` or `production` |
| `APP_DEBUG` | `false` in production |
| `DB_CONNECTION` | `sqlite` or `mysql` |
| `DB_DATABASE` | Path (SQLite) or database name (MySQL) |
| `SESSION_DRIVER` | `database` (default) |
| `FILESYSTEM_DISK` | `local` default; uploads use `public` disk explicitly |
| `MAIL_*` | Email (currently `log` driver — not used for certificate delivery) |

### Storage paths

| Path | Content |
|------|---------|
| `storage/app/public/permit-qrcodes/` | QR SVG files per permit |
| `storage/app/public/certificate-signatures/` | Official signature image |
| `public/storage` | Symlink to `storage/app/public` (run `storage:link`) |

---

## 13. Maintenance and operations

### Daily permit expiry sync

Command:

```bash
php artisan permits:sync-expiry
```

Marks `valid` permits as `expired` when `expiry_date` is before today. Scheduled daily at **01:00** via Laravel’s scheduler.

On a server, add a cron entry (Linux) or Task Scheduler job (Windows):

```bash
* * * * * cd /path/to/dpms && php artisan schedule:run >> /dev/null 2>&1
```

### Frontend assets

After pulling code changes:

```bash
npm install
npm run build
```

During development:

```bash
npm run dev
```

### Database

```bash
php artisan migrate          # Apply schema changes
php artisan db:seed          # Demo data only — not for production refresh
```

### Tests

```bash
php artisan test
```

### Backups

Back up regularly:

- Database file (`database/database.sqlite`) or MySQL dump
- `storage/app/public/` (signatures and QR assets)

---

## 14. Security notes

- Change default seeded passwords immediately in any shared environment.
- Use HTTPS on production networks when possible; required for some browser features if extended later.
- Keep `APP_DEBUG=false` on the LAN server to avoid exposing stack traces.
- Restrict network access to the DPMS host (VLAN / firewall) — the app is designed for **internal** use.
- Legacy `driver` role accounts are rejected at login with a clear message.
- Audit logs capture who changed driver and permit records.

---

## 15. Known limitations

| Item | Notes |
|------|-------|
| **Email delivery** | Certificates are not emailed automatically; officers distribute PDFs manually |
| **Driver login / portal** | Removed by design |
| **Driver photos on certificates** | Removed by design |
| **Company / preferences settings** | UI placeholders; not persisted to database yet |
| **Permit renewal** | Manual re-issuance via wizard; no one-click extend |
| **`php artisan serve`** | Development/pilot only; use a proper web server for production |
| **SQLite** | Fine for single-user pilot; prefer MySQL for many concurrent officers |

---

## 16. Troubleshooting

### Other PCs cannot open the site

- Confirm `php artisan serve --host=0.0.0.0` (or proper web server binding)
- Check Windows firewall / antivirus allows the port
- Use the server’s IP, not `localhost`, on client machines

### QR verification opens wrong host or fails

- Set `APP_URL` in `.env` to the LAN URL everyone uses
- Run `php artisan config:clear` after changing `.env`

### Signature does not appear on PDF

- Upload signature in Settings → Signature Upload (admin)
- Run `php artisan storage:link`
- Confirm file exists under `storage/app/public/certificate-signatures/`

### “Illegal offset type” or similar on Audit Logs

- Ensure application code is up to date (filter parameters must be plain strings)

### Login fails for fleet@ / admin@ after fresh install

- Run `php artisan migrate --seed` or create users via Accounts UI
- Default development password is `password` until changed

### PDF or QR errors

- Enable PHP `gd` extension
- Check `storage/` and `bootstrap/cache/` are writable by the web server user

---

## Document history

| Version | Date | Notes |
|---------|------|-------|
| 1.0 | May 2026 | Initial internal documentation — no driver portal, no certificate photos, signature upload, LAN deployment guidance |

---

*For quick install commands, see the project [README.md](../README.md). For certificate layout changes, edit `resources/views/permits/pdf-certificate.blade.php`.*
