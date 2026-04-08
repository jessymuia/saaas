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

### UUID Migration (Complete — April 2026)
All 60+ tables have been migrated from `bigInteger` auto-increment IDs to UUIDs:
- `AppUtils::defaultTableColumns()` now sets `uuid('id')->primary()->default(DB::raw('gen_random_uuid()'))`, `timestampsTz()`, decimal version, boolean status/archive, and `uuid` audit FK columns (created_by, updated_by, deleted_by)
- All models extending `DefaultAppModel` automatically have `$incrementing = false` and `$keyType = 'string'`
- Standalone models (Plan, Domain, SaasClient, SupportTicket, SubscriptionPayment, UsageMetric, SaasClientUser, SystemAdmin, User) updated with UUID settings, SoftDeletes, full `$fillable`, casts, and `createdBy()`/`updatedBy()`/`deletedBy()` relations
- `SaasClientUser` changed from extending `Authenticatable` to plain `Model` (it is a pivot/join table, not an auth user)
- `plans` and `domains` migrations updated to use `defaultTableColumns(addAuditFk: false)` — these are central/platform tables managed by SystemAdmins, not tenant users
- `AppUtils::defaultTableColumns()` gained an `addStatus: bool = true` parameter to allow tables with their own business `status` string column (subscriptions, subscription_payments, support_tickets) to skip the boolean status column
- `subscriptions`, `subscription_payments`, `support_tickets` migrations updated to `addStatus: false` to prevent duplicate column conflict
- Composite PKs (`['id', 'saas_client_id']`) replaced by simple UUID PKs; composite FKs replaced with `foreignUuid()->constrained()`
- `saas_client_id` on pre-2025 migrations is a plain indexed UUID (no FK constraint, since those tables are created before `saas_clients`); FK integrity maintained by application logic
- Spatie role/permission IDs kept as `bigIncrements`; morph columns use `uuidMorphs`
- `SaasClient::plan_id` cast changed from `'integer'` to `'string'`
- `Subscription::startTrial()` signature changed from `int $planId` to `string $planId`

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
4. `TenantBrandingMiddleware` — applies per-tenant colour from `$tenant->primary_color`
5. `CheckSubscriptionExpiry` — redirects to billing if subscription expired + grace period over

**Bootstrappers** (`config/tenancy.php`):
- `CacheTenancyBootstrapper` — prefixes cache keys per tenant
- `FilesystemTenancyBootstrapper` — scopes storage paths per tenant
- `QueueTenancyBootstrapper` — carries tenant context into queued jobs

**Per-tenant branding** stored in `saas_clients.data` JSON column via stancl/tenancy VirtualColumn:
- `$tenant->primary_color` — hex colour for Filament panel accent (VirtualColumn attribute)
- `$tenant->logo_path` — storage path to tenant logo (VirtualColumn attribute)
- Access via accessors `getPrimaryColorAttribute()`/`getLogoPathAttribute()` which read `$this->attributes[key]` directly
- VirtualColumn decodes `data` JSON → individual attributes on model retrieval, re-encodes on save
- Filament form uses `ColorPicker::make('primary_color')` / `FileUpload::make('logo_path')` (not `data.primary_color`)

**CRITICAL — FilesystemTenancyBootstrapper note**:
- `asset_helper_tenancy = false` MUST remain in `config/tenancy.php`
- Without this, the `asset()` helper gets `/tenantXXX/` prepended to all URLs when tenant context is active
- Filament JS/CSS served from `/tenantXXX/js/filament/...` paths return 404, breaking Alpine.js entirely
- Symptoms: `$store.sidebar` undefined, `filamentDropdown` not defined, sidebar/dropdowns/modals broken

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
