# PropManage SaaS — Replit Environment

## Project Overview

Multi-tenant property management SaaS platform built for Kenya / Africa.

- **Backend**: Laravel 12 + Filament 5 + Spatie Permissions + Laravel Auditing + stancl/tenancy
- **Marketing Site**: Next.js 16 (App Router) + Tailwind CSS 4 — lives in `saas-marketing/`
- **Database**: Replit PostgreSQL (standard, replaces Citus cluster from Docker setup)
- **Cache / Session**: File driver (replaces Redis for dev simplicity)
- **Queue**: Database driver (replaces Redis queue)

## Architecture

```
/                   Laravel 12 backend (PHP 8.2)
├── app/            Controllers, Models, Filament panels, Providers
├── config/         Laravel configuration files
├── database/       Migrations, seeders, factories
├── routes/         web.php, api.php, tenant.php, console.php
├── resources/      Blade views, CSS, JS
├── saas-marketing/ Next.js marketing website
│   ├── app/        Next.js App Router pages
│   └── components/ Shared React components
├── start.sh        Startup script (starts Redis + Laravel)
└── .env            Environment configuration (auto-generated for Replit)
```

## Running Services

| Service | Port | Workflow |
|---------|------|----------|
| Next.js Marketing Site | 5000 | "Start application" (webview) |
| Laravel Backend API | 8000 | "Laravel App" (console) |

## Key Migrations Notes

The original project used **Citus PostgreSQL** (distributed database) with Docker. For Replit:
- `create_distributed_table()` and `create_reference_table()` calls have been removed from migrations
- Composite primary keys that required Citus have been converted to standard single-column PKs
- File `2025_01_01_000010_distribute_tables_via_citus.php` is a no-op

## Environment Variables Needed

From `.env.example`, these optional integrations need secrets:
- `MPESA_CONSUMER_KEY`, `MPESA_CONSUMER_SECRET`, `MPESA_SHORTCODE`, `MPESA_PASSKEY` — M-Pesa payments
- `MAIL_*` — SMTP mail configuration (currently uses log driver)

## Development Commands

```bash
# Run migrations
php artisan migrate

# Clear all caches
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Build Next.js marketing site
cd saas-marketing && npm run build

# Start queue worker manually
php artisan queue:work
```
