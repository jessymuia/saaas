# PropManage SaaS — Multi-Tenant Property Management Platform

> **Stack:** Laravel 12 · PHP 8.3 · PostgreSQL 16 + Citus 12 · Redis · Filament 4 · Next.js 14 (marketing)
> **Architecture:** Single-database multi-tenancy (`stancl/tenancy`) with Citus distributed tables on `saas_client_id`
> **Author:** @jessymuia — tag for review

---

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Quick Start — Local Development](#quick-start--local-development)
3. [Citus Schema Design](#citus-schema-design)
4. [Table Classification](#table-classification)
5. [Tenant Identification & Routing](#tenant-identification--routing)
6. [Running Migrations & Bootstrap](#running-migrations--bootstrap)
7. [Running Tests](#running-tests)
8. [Marketing Site (Next.js)](#marketing-site-nextjs)
9. [Filament Panels](#filament-panels)
10. [Laravel Horizon (Queues)](#laravel-horizon-queues)
11. [M-Pesa Integration](#m-pesa-integration)
12. [DevOps & Operations](#devops--operations)
13. [Security & RLS](#security--rls)
14. [ERD Diagram](#erd-diagram)
15. [Checklist Status](#checklist-status)

---

## Architecture Overview

```
+---------------------------------------------------------+
|                     Public Internet                      |
|  propertysasa.com         *.propertysasa.com             |
|  (marketing / central)    (tenant subdomains)            |
+-------------+-----------------------+-------------------+
              |                       |
              v                       v
       +-------------+        +------------------+
       |  Next.js 14 |        |  Laravel 12 App  |
       |  :3000      |        |  Nginx + PHP-FPM |
       |  (marketing)|        |  :8000           |
       +-------------+        +--------+---------+
                                       |
                     +-----------------+-----------------+
                     |                 |                 |
                     v                 v                 v
             +--------------+  +-----------+  +------------------+
             |    Redis     |  |  Horizon  |  | Citus Coordinator|
             |  Cache/Queue |  |  Workers  |  |  PostgreSQL 16   |
             +--------------+  +-----------+  +--------+---------+
                                                        |
                                         +--------------+-------------+
                                         |                            |
                                  +--------------+         +--------------+
                                  |  Worker 1    |         |  Worker 2    |
                                  |  Shards 0-4  |         |  Shards 5-9  |
                                  +--------------+         +--------------+
```

**Key architectural decisions:**

| Decision | Choice | Reason |
|---|---|---|
| Tenancy mode | Single-database (stancl/tenancy) | Simpler ops; RLS + Citus provide isolation |
| Distribution column | `saas_client_id` (UUID) | Consistent with existing RLS policies and seeders |
| Composite PKs | `(id, saas_client_id)` on all distributed tables | Citus requirement for co-located JOINs |
| Shard count | 10 (local), 64-128 (production) | Configured in `docker/citus-bootstrap.sh` |
| Tenant identification | Subdomain: `{slug}.propertysasa.com` | Custom domains supported via `domains` table |
| Queue driver | Redis + Laravel Horizon | Real-time queue monitoring; tenant context preserved |
| Admin panels | Filament 4 dual-panel (Central + App) | Native multi-tenancy support |

---

## Quick Start — Local Development

### Prerequisites

- Docker Desktop 4.x+
- Node.js 20+ (for marketing site)
- PHP 8.3+ & Composer (for local artisan without Docker)

### 1. Clone & Configure

```bash
git clone https://github.com/jessymuia/saaas.git
cd saaas

# Copy environment file
cp .env.example .env
```

Edit `.env`:
- `APP_KEY` — leave blank; run `php artisan key:generate` after
- All DB credentials are pre-configured for Docker Compose (no changes needed for local dev)

### 2. Start Docker Services

```bash
docker compose up -d
```

This starts:
- `citus-coordinator` — PostgreSQL 16 + Citus 12 (port 5432)
- `citus-worker-1`, `citus-worker-2` — Citus worker nodes
- `citus-bootstrap` — runs once to register workers + configure shards (idempotent)
- `php` — Laravel PHP-FPM
- `nginx` — web server (port 8000)
- `redis` — cache & queue backend (port 6379)
- `horizon` — Laravel Horizon queue worker
- `scheduler` — Laravel task scheduler

### 3. Install PHP Dependencies & Generate Key

```bash
docker compose exec php composer install
docker compose exec php php artisan key:generate
```

### 4. Run Citus Bootstrap (if not auto-run by Docker)

```bash
# From host — requires psql installed
bash docker/citus-bootstrap.sh

# Or via Docker:
docker compose run --rm citus-bootstrap
```

### 5. Run Migrations

```bash
docker compose exec php php artisan migrate
```

> The migration `2025_01_01_000010_distribute_tables_via_citus.php` runs
> `create_distributed_table()` and `create_reference_table()` — this requires
> Citus workers to be registered. Run bootstrap first.

### 6. Seed Initial Data

```bash
docker compose exec php php artisan db:seed
# Or full one-command setup:
docker compose exec php php artisan app:setup-application
```

### 7. Access the Application

| Service | URL |
|---|---|
| Main app | http://localhost:8000 |
| Central admin (Filament) | http://localhost:8000/sysadmin |
| Tenant app | http://{slug}.localhost:8000/app |
| Horizon dashboard | http://localhost:8000/horizon |
| Marketing site | http://localhost:3000 |

### 8. Local Subdomain Setup

Add wildcard entries to `/etc/hosts` for subdomain tenant routing:

```
# /etc/hosts
127.0.0.1 localhost
127.0.0.1 mycompany.localhost
127.0.0.1 demo.localhost
```

Or use dnsmasq (macOS) for automatic wildcard resolution:

```bash
brew install dnsmasq
echo "address=/.localhost/127.0.0.1" >> /usr/local/etc/dnsmasq.conf
sudo brew services start dnsmasq
sudo mkdir -p /etc/resolver
echo "nameserver 127.0.0.1" | sudo tee /etc/resolver/localhost
```

---

## Citus Schema Design

### Distribution Column

- **Column:** `saas_client_id`
- **Type:** `UUID` — consistent with existing RLS policies (`app.saas_client_id` session variable uses UUID cast) and all seeders
- **Decision note:** Keeping UUID (not bigint) to avoid a breaking migration on a populated database. Bigint would marginally improve shard key performance but is not worth the migration cost at this stage.

### Shard Count

| Environment | Shard Count | Configuration |
|---|---|---|
| Local dev | 10 | `docker/citus-bootstrap.sh` |
| Staging | 32 | Bootstrap script (default) |
| Production | 64-128 | Bootstrap script + `ALTER SYSTEM SET citus.shard_count` |

Adjust shard count per table after creation:

```sql
SELECT alter_distributed_table('properties', shard_count := 64);
```

### Colocation Verification

After migrations, verify all distributed tables share the same colocation group:

```sql
SELECT logicalrelid, colocationid, partmethod
FROM pg_dist_partition
ORDER BY colocationid, logicalrelid;
```

All tenant-scoped tables must show the **same `colocationid`**.

Via artisan:

```bash
php artisan citus:validate-colocation
```

---

## Table Classification

### Local / Central Tables (NOT distributed)

| Table | Purpose |
|---|---|
| `saas_clients` | SaaS tenant registry |
| `domains` | Custom domain mappings |
| `saas_client_users` | Platform-level user accounts |
| `plans` | Subscription plan definitions |
| `subscriptions` | Active tenant subscriptions |
| `subscription_payments` | Payment records |
| `system_admins` | Central super-admin accounts |
| `migrations` | Laravel migration tracking |

### Reference Tables (Replicated to all Citus shards)

| Table | Purpose |
|---|---|
| `ref_property_types` | Property type options |
| `ref_unit_types` | Unit type options |
| `ref_payment_types` | Payment method options |
| `ref_billing_types` | Billing type options |
| `ref_tenancy_agreement_types` | Agreement type options |
| `ref_utilities` | Utility type options |
| `services` | Service catalogue |

### Distributed Tables (tenant-scoped, distributed on `saas_client_id`)

All have: composite PK `(id, saas_client_id)`, composite FKs, RLS policies, `BelongsToTenant` trait.

| Table | Description |
|---|---|
| `users` | Tenant users |
| `properties` | Properties managed by tenant |
| `units` | Units within properties |
| `tenants` | Property tenants (renters) |
| `clients` | Client contacts |
| `property_owners` | Property owner records |
| `tenancy_agreements` | Lease agreements |
| `invoices` | Rent & service invoices |
| `invoice_payments` | Invoice payment records |
| `tenancy_bills` | Additional bills on agreements |
| `credit_notes` | Credit notes on invoices |
| `manual_invoices` | Manual invoices |
| `manual_invoice_items` | Line items for manual invoices |
| `meter_readings` | Utility meter readings |
| `property_utilities` | Utilities assigned to properties |
| `property_services` | Services assigned to properties |
| `property_payment_details` | Payment details per property |
| `tenancy_agreement_files` | Documents for agreements |
| `vacation_notices` | Tenant vacation notices |
| `unit_occupation_monthly_records` | Monthly occupancy tracking |
| `sent_emails` | Email log |
| `email_attachments` | Email attachment records |
| `escalation_rates_and_amounts_logs` | Rent escalation history |
| `support_tickets` | Support tickets per tenant |
| `usage_metrics` | Tenant usage data |
| `notifications` | In-app notifications |
| `audits` | Audit trail |
| `jobs` | Queue jobs (with `saas_client_id`) |
| `failed_jobs` | Failed queue jobs |

---

## Tenant Identification & Routing

### Strategy: Subdomain-first

```
{tenant-slug}.propertysasa.com  ->  tenant context
propertysasa.com                ->  central (marketing/admin)
```

### Custom Domain Support

Tenants can register custom domains via the `domains` table:
- `mycompany.propertysasa.com` — resolved via subdomain
- `app.mycompany.com` — resolved via `domains` table lookup

### Middleware Stack

| Middleware | Purpose |
|---|---|
| `InitializeTenancyBySubdomain` | Identifies tenant from subdomain, initializes tenancy |
| `SetRlsSessionVariables` | Sets `app.saas_client_id` and `app.bypass_rls` PostgreSQL session vars |
| `CheckTenantSuspended` | Returns 403 if tenant `is_suspended = true` |

---

## Running Migrations & Bootstrap

```bash
# Fresh setup
docker compose exec php php artisan migrate:fresh --seed

# Run only new migrations
docker compose exec php php artisan migrate

# Re-run Citus bootstrap (idempotent)
bash docker/citus-bootstrap.sh

# Validate Citus co-location
docker compose exec php php artisan citus:validate-colocation
```

---

## Running Tests

```bash
# All tests
docker compose exec php php artisan test

# Specific suites
docker compose exec php php artisan test --testsuite=Unit
docker compose exec php php artisan test --testsuite=Feature

# Specific test files
docker compose exec php php artisan test tests/Feature/TenantIsolationTest.php
docker compose exec php php artisan test tests/Feature/CitusColocationTest.php
docker compose exec php php artisan test tests/Feature/RlsPolicyTest.php
docker compose exec php php artisan test tests/Feature/QueueContextTest.php
docker compose exec php php artisan test tests/Unit/Tenancy/TenantScopeTest.php
docker compose exec php php artisan test tests/Unit/Tenancy/BootstrapperRevertTest.php

# Audit raw queries (heuristic scan)
docker compose exec php php artisan citus:audit-raw-queries
```

### Test Coverage

| Test | Validates |
|---|---|
| `TenantIsolationTest` | Cross-tenant data isolation via RLS |
| `TenantScopeTest` | `BelongsToTenant` on all 24 distributed models |
| `BootstrapperRevertTest` | Cache/Filesystem/Queue bootstrapper revert |
| `CitusColocationTest` | All distributed tables distributed + co-located |
| `RlsPolicyTest` | RLS policies exist on distributed tables |
| `QueueContextTest` | Queue jobs carry correct tenant context |

---

## Marketing Site (Next.js)

Lives in `saas-marketing/` — runs independently on port 3000.

```bash
cd saas-marketing
npm install
npm run dev
# Open http://localhost:3000
```

### Pages

| Page | Route |
|---|---|
| Home (1500+ words) | `/` |
| Features | `/features` |
| Pricing | `/pricing` |
| Use Cases | `/use-cases` |
| About Us (1500+ words) | `/about` |
| Testimonials | `/testimonials` |
| Security & Compliance | `/security` |
| Blog (MDX) | `/blog` |
| Changelog | `/changelog` |
| Contact | `/contact` |
| Terms of Service (Kenya law) | `/terms` |
| Privacy Policy | `/privacy` |
| Cookie Policy | `/cookies` |
| FAQ | `/faq` |
| Request a Demo | `/demo` |

---

## Filament Panels

### Central Admin (`/sysadmin`)

Guard: `system_admin` | RLS bypass: `app.bypass_rls = 'true'`

Features: Manage SaaS clients, view all data, billing, support tickets

### Tenant App Panel (`/app`)

Guard: `web` | Scoped via: `BelongsToTenant` | Middleware: `InitializeTenancyBySubdomain`

---

## Laravel Horizon (Queues)

```bash
# Horizon dashboard
open http://localhost:8000/horizon

# Status
docker compose exec horizon php artisan horizon:status

# Pause/resume
docker compose exec horizon php artisan horizon:pause
docker compose exec horizon php artisan horizon:continue
```

---

## M-Pesa Integration

Set in `.env`:

```
MPESA_ENV=sandbox
MPESA_CONSUMER_KEY=your_key
MPESA_CONSUMER_SECRET=your_secret
MPESA_SHORTCODE=your_shortcode
MPESA_PASSKEY=your_passkey
MPESA_CALLBACK_URL=https://yourapp.com/api/mpesa/callback
```

Usage:

```php
$mpesa = new \App\Services\MpesaService();
$result = $mpesa->stkPush('254712345678', 5000, 'INV-001', 'Rent Payment');
```

Webhook: `POST /api/mpesa/callback` — handled by `MpesaWebhookController`.

---

## DevOps & Operations

| Task | Documentation |
|---|---|
| Database backups | `docs/devops/backups.md` |
| Shard rebalancing | `docs/devops/shard-rebalancing.md` |
| Log enrichment | `docs/devops/log-enrichment.md` |
| ERD diagram | `docs/erd/schema.md` |

---

## Security & RLS

RLS is enabled on all 28+ distributed tables. Two policies per table:

1. **Tenant isolation** — rows visible only when `saas_client_id = current_setting('app.saas_client_id')::uuid`
2. **Super admin bypass** — all rows visible when `current_setting('app.bypass_rls') = 'true'`

Verify:

```bash
php artisan test tests/Feature/RlsPolicyTest.php
```

---

## ERD Diagram

See `docs/erd/schema.md` for the full Mermaid ER diagram with composite key annotations.

---

## Checklist Status

### Phase 1 — Infrastructure & Database
- [x] PostgreSQL 16 + Citus 12 coordinator + 2 workers in Docker Compose
- [x] `CREATE EXTENSION citus` in migration and bootstrap
- [x] Bootstrap shell script: `docker/citus-bootstrap.sh` (idempotent, worker registration, shard count = 10)
- [x] Citus bootstrap wired into docker-compose as a `citus-bootstrap` service (runs once)

### Phase 2 — Citus Schema Design
- [x] Distribution column documented (`saas_client_id` UUID)
- [x] Shard count documented (10 local, 64-128 production)
- [x] Table classification lists (local, reference, distributed) in this README
- [x] Colocation verification: `php artisan citus:validate-colocation`

### Phase 3 — Laravel Base Setup
- [x] Laravel 12
- [x] `stancl/tenancy` single-database mode (`config/tenancy.php`)
- [x] Bootstrappers: Cache, Filesystem, Queue (no DatabaseTenancyBootstrapper)
- [x] `.env.example` published without credentials

### Phase 4 — Tenant (SaasClient) Model & Central Tables
- [x] `SaasClient` model with `getCustomColumns()`
- [x] Plans, Subscriptions, SubscriptionPayments, SaasClientUsers tables
- [x] ERD diagram in Mermaid format: `docs/erd/schema.md`

### Phase 5 — Distributed Table Migrations
- [x] `saas_client_id` column on all tenant-scoped tables
- [x] Composite PKs `(id, saas_client_id)`
- [x] Composite FKs include `saas_client_id`
- [x] Citus distribution migration
- [x] Colocation validation artisan command

### Phase 6 — stancl Bootstrappers
- [x] Cache/Filesystem/Queue bootstrappers configured
- [x] Revert tests: `BootstrapperRevertTest`

### Phase 7 — Global Tenant Scope
- [x] `BelongsToTenant` on all 24 distributed models
- [x] Global scope unit tests
- [x] `withoutTenantScope()` escape hatch
- [x] `saas_client_id` auto-injected on INSERT

### Phase 8 — Domain & Routing + Marketing Site
- [x] Subdomain tenant identification
- [x] Custom domain support
- [x] Next.js marketing site: `saas-marketing/` (15 pages)
- [x] Local dev subdomain setup documented

### Phase 9 — Authentication
- [x] `User` model distributed (tenant-scoped)
- [x] `SystemAdmin` central model for super admins
- [ ] Welcome email templates (pending)

### Phase 10 — Filament
- [x] Central admin panel (SystemAdmin guard, RLS bypass)
- [x] Tenant app panel (BelongsToTenant scoped)

### Phase 11 — Queues & Jobs
- [x] `jobs` and `failed_jobs` have `saas_client_id`
- [x] Horizon configured + Docker service
- [x] Queue context tests: `QueueContextTest`

### Phase 12 — Citus Query Discipline
- [x] Raw query audit: `php artisan citus:audit-raw-queries`
- [ ] Slow query log (reminder: enable on coordinator)
- [ ] `pg_stat_statements` (reminder: enable on coordinator)

### Phase 13 — Security & Isolation
- [x] RLS policies on 28+ distributed tables
- [x] Super admin bypass policy
- [x] `SetRlsSessionVariables` middleware
- [x] `CheckTenantSuspended` middleware
- [ ] HTTPS enforcement (configure in Nginx/load balancer)
- [ ] Rate limiting (configure in RouteServiceProvider)

### Phase 14 — Billing & Plans
- [x] Trial period logic (`Subscription::startTrial()`)
- [x] Grace period logic (`Subscription::moveToGracePeriod()`)
- [x] Tenant suspension/expiry
- [x] M-Pesa: `MpesaService` + `MpesaWebhookController` + webhook route

### Phase 15 — Testing
- [x] `TenantIsolationTest`
- [x] `TenantScopeTest`
- [x] `BootstrapperRevertTest`
- [x] `CitusColocationTest`
- [x] `RlsPolicyTest`
- [x] `QueueContextTest`
- [ ] Load tests (k6/Locust — out of scope)

### Phase 16 — DevOps & Deployment
- [x] Backup docs: `docs/devops/backups.md`
- [x] Shard rebalancing docs: `docs/devops/shard-rebalancing.md`
- [x] Log enrichment docs: `docs/devops/log-enrichment.md`
