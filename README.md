# Driver Permit Management System (DPMS)

Internal web application for **ZAFFICO PLC** to register drivers, issue driving permits, print PDF certificates, and verify permits via QR code.

Built with **Laravel 12**, **Tailwind CSS**, and **Alpine.js**.

---

## What it does

- **Driver register** - employee records (NRC, licence, department, contact)
- **Permit issuance** - guided wizard for fleet officers
- **PDF certificates** - branded permits with official signature and QR verification
- **Public verification** - `/permits/verify/{permit_number}` (no login)
- **Roles** - Administrator, Fleet Officer, Management
- **Audit trail** - changes to drivers, permits, and users

Drivers do **not** log in. Fleet staff distribute certificates by email or print.

---

## Documentation

| Document | Purpose |
|----------|---------|
| [docs/DOCUMENTATION.md](docs/DOCUMENTATION.md) | Full user and technical guide (roles, workflows, certificates, admin) |
| [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php) | Development login accounts (local only) |

---

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ (build assets once per deploy)
- PHP extensions: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, **`gd`**
- Database: **SQLite** (dev / small pilot) or **MySQL** (recommended on Windows Server)

---

## Local development

```bash
cp .env.example .env          # Windows: copy .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed           # optional: demo users (password: password)
php artisan storage:link
npm install
npm run build
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000) and sign in.

### Development logins (after `db:seed`)

| Email | Role |
|-------|------|
| `admin@zaffico.test` | Administrator |
| `fleet@zaffico.test` | Fleet Officer |
| `management@zaffico.test` | Management |

Change these passwords before any shared or production use.

---

## Deploy on Windows Server (e.g. `10.0.0.0`)

1. Install **PHP 8.2+**, **Composer**, **IIS** + URL Rewrite, and **MySQL** (recommended).
2. Clone or copy the repo to e.g. `C:\inetpub\dpms`.
3. Create `.env` for production:

   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=http://10.0.30.52
   ```

4. Run:

   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --force
   php artisan storage:link
   npm ci && npm run build
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. Point the **IIS site** physical path to the `public` folder (not the project root).
6. Grant **Modify** on `storage` and `bootstrap\cache` to the IIS app pool identity.
7. Allow the HTTP port in **Windows Firewall** for the LAN.
8. Schedule `php artisan schedule:run` (Task Scheduler, every minute) for nightly permit expiry sync.

Do **not** run `db:seed` on production. Create real admin accounts under **Users** in the app.

See [docs/DOCUMENTATION.md](docs/DOCUMENTATION.md) sections 5-6 and 13 for full detail.

---

## Tests

```bash
php artisan test
```

---

## License

Proprietary - ZAFFICO PLC internal use unless stated otherwise.
