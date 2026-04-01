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
- `APP_TENANT_MODE=slug` (default) or `subdomain` — switches tenant identification mode

## Multi-Tenancy Architecture

**Tenant identification modes** (set in `.env`):
- `APP_TENANT_MODE=slug` — path-based `/app/app/{slug}` (Replit / dev default)
- `APP_TENANT_MODE=subdomain` — subdomain-based `{slug}.yoursaas.com` (production, needs wildcard DNS)

**Middleware stack per-request** (AppPanelProvider):
1. `InitializeTenancyBySlug` or `InitializeTenancyBySubdomain` (selected by `APP_TENANT_MODE`)
2. `CheckTenantSuspended` — 403 if tenant is suspended
3. `SetRlsSessionVariables` — sets PostgreSQL RLS session variables
4. `TenantBrandingMiddleware` — applies per-tenant colour from `data.primary_color`
5. `CheckSubscriptionExpiry` — redirects to billing if subscription expired + grace period over

**Bootstrappers** (`config/tenancy.php`):
- `CacheTenancyBootstrapper` — prefixes cache keys per tenant
- `FilesystemTenancyBootstrapper` — scopes storage paths per tenant
- `QueueTenancyBootstrapper` — carries tenant context into queued jobs

**Per-tenant branding** stored in `saas_clients.data` JSON column:
- `data.primary_color` — hex colour for Filament panel accent
- `data.logo_path` — storage path to tenant logo

## Key Features Implemented

- **Billing page** (`/app/app/{slug}/billing-page`) — shows subscription status, plan limits, usage metrics, and M-Pesa STK-push payment button
- **Welcome email queue** — `SendTenantWelcomeDetails` implements `ShouldQueue` on the `emails` queue with 3 retries
- **`Subscription::getAmount()`** — returns monthly / quarterly / annual price from the plan
- **Branding section** in admin `SaasClientResource` form — colour picker + logo upload (collapsed by default)

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
