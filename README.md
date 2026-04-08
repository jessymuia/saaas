# PropManage SaaS — Local Installation Guide

> **Stack:** Laravel 12 · PHP 8.3 · PostgreSQL 16 + Citus 12 · Redis · Filament 4 · Next.js (marketing site)

---

## Prerequisites

Before you begin, install the following:

1. **[Docker Desktop for Windows](https://www.docker.com/products/docker-desktop/)** (version 4.x or later)
   - During installation, choose **WSL 2** backend when prompted (recommended)
   - After installing, open Docker Desktop and wait until the status shows **"Engine running"**

2. **[Git for Windows](https://git-scm.com/download/win)**
   - Needed to clone the repository

> Everything else (PHP, Node.js, PostgreSQL, Redis) runs inside Docker — nothing else needs to be installed.

---

## Installation Steps

### Step 1 — Open a terminal

Use **PowerShell**, **Command Prompt**, or **Git Bash**. All commands below work in any of them.

---

### Step 2 — Clone the repository

```bash
git clone https://github.com/jessymuia/saaas.git
cd saaas
```

---

### Step 3 — Set up your environment file

```bash
copy .sweenv.example .env
```

> On Git Bash or PowerShell you can also use `cp .sweenv.example .env`

Open the `.env` file in any text editor (Notepad, VS Code, etc.) and fill in these values:

| Variable | What to enter |
|---|---|
| `APP_KEY` | Leave blank — it will be generated automatically in Step 5 |
| `DB_PASSWORD` | `postgres` (this matches the Docker setup — no need to change) |
| `MAIL_HOST` | Your SMTP server address |
| `MAIL_USERNAME` | Your SMTP email address |
| `MAIL_PASSWORD` | Your SMTP password |
| `MAIL_FROM_ADDRESS` | The email address your app sends from |
| `MPESA_CONSUMER_KEY` | Your Safaricom Daraja consumer key |
| `MPESA_CONSUMER_SECRET` | Your Safaricom Daraja consumer secret |
| `MPESA_SHORTCODE` | Your M-Pesa shortcode |
| `MPESA_PASSKEY` | Your M-Pesa passkey |

> Leave all other values as they are — the database and Redis settings are already configured for Docker.

---

### Step 4 — Build and start all services

```bash
docker compose up -d --build
```

This will download the required Docker images and start all services. **The first run takes 5–15 minutes** depending on your internet speed.

Once done, you should see all services listed as **Started**. You can also check Docker Desktop — all containers should show a green running status.

The following services will be running:

| Service | What it does | Port |
|---|---|---|
| `nginx` | Serves the Laravel web app | 8000 |
| `php` | Laravel application (PHP-FPM) | — |
| `citus-coordinator` | Main PostgreSQL database | 5432 |
| `citus-worker-1` | Database shard worker 1 | — |
| `citus-worker-2` | Database shard worker 2 | — |
| `citus-bootstrap` | One-time database cluster setup | — |
| `redis` | Cache and queue backend | 6379 |
| `horizon` | Background job queue worker | — |
| `scheduler` | Scheduled task runner | — |
| `marketing` | Next.js marketing website | 5000 |

---

### Step 5 — Generate the application key

```bash
docker compose exec php php artisan key:generate
```

---

### Step 6 — Run database migrations

```bash
docker compose exec php php artisan migrate
```

> If this fails with a Citus-related error, the database cluster may still be initialising. Wait 30 seconds and try again. You can also manually re-run the cluster setup with:
> ```bash
> docker compose run --rm citus-bootstrap
> ```

---

### Step 7 — Seed the database

```bash
docker compose exec php php artisan db:seed --class=PermissionSeeder
docker compose exec php php artisan db:seed --class=SystemAdminSeeder
```

---

### Step 8 — Link file storage

```bash
docker compose exec php php artisan storage:link
```

---

### Step 9 — Open the application

Open your browser and go to:

| URL | What you will see |
|---|---|
| `http://localhost:8000/admin` | Super-admin login page |
| `http://localhost:5000` | Marketing website |
| `http://localhost:8000/app/app/{slug}` | Tenant panel (replace `{slug}` with the tenant's slug) |
| `http://localhost:8000/horizon` | Queue dashboard |

**Default super-admin login credentials:**
- Email: `superadmin@gmail.com`
- Password: `password`

---

### Step 10 — Set up tenant subdomains (optional)

Tenants can also be accessed via subdomain (e.g. `mycompany.localhost`). To enable this on Windows, open Notepad **as Administrator** and edit the hosts file at:

```
C:\Windows\System32\drivers\etc\hosts
```

Add a line for each tenant slug you want to test:

```
127.0.0.1  mycompany.localhost
127.0.0.1  demo.localhost
```

After saving, the tenant panel will be accessible at `http://mycompany.localhost:8000/app/app/mycompany`.

---

## Useful Commands

Run these from the project folder in your terminal:

```bash
# Check the status of all running services
docker compose ps

# View live logs for a service (e.g. php, horizon, nginx)
docker compose logs -f php

# Run any Laravel artisan command
docker compose exec php php artisan <command>

# Restart a single service
docker compose restart php

# Stop all services (data is kept)
docker compose down

# Stop all services and delete all data (clean slate)
docker compose down -v

# Start services again after stopping
docker compose up -d
```

---

## Troubleshooting

**Docker Desktop is not running**
Open Docker Desktop and wait for the engine to start before running any `docker compose` commands.

**Port 8000 or 5432 already in use**
Another application on your machine is using that port. Either stop that application or change the port in `docker-compose.yml`.

**Migrations fail on first run**
The Citus database cluster takes a moment to initialise. Wait 30 seconds after `docker compose up` finishes, then run the migration command again.

**Changes to `.env` not taking effect**
Restart the affected service after editing `.env`:
```bash
docker compose restart php
```

**To start completely fresh (delete all data)**
```bash
docker compose down -v
docker compose up -d --build
```
Then repeat Steps 5–8.
