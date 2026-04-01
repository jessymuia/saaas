# PropManage SaaS — Windows Installation Guide

## Recommended Method: Docker (Easiest)

Docker runs everything inside Linux containers, so you don't need to install PHP, PostgreSQL, or Redis on Windows manually.

### Prerequisites

1. Install [Docker Desktop for Windows](https://www.docker.com/products/docker-desktop/)
2. Install [Git for Windows](https://git-scm.com/download/win)
3. Make sure Docker Desktop is **running** before you proceed

### Steps

**1. Clone the repository**

```cmd
git clone https://github.com/jessymuia/saaas.git
cd saaas
```

**2. Create your `.env` file**

Copy the example below and save it as `.env` in the project root:

```env
APP_NAME="PropManage SaaS"
APP_ENV=local
APP_DEBUG=true
APP_KEY=
APP_URL=http://localhost:8000
APP_TENANT_MODE=slug

DB_CONNECTION=pgsql
DB_HOST=citus-coordinator
DB_PORT=5432
DB_DATABASE=propman
DB_USERNAME=postgres
DB_PASSWORD=postgres

REDIS_HOST=redis
REDIS_PORT=6379

CACHE_DRIVER=redis
SESSION_DRIVER=file
QUEUE_CONNECTION=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@propmanage.com
MAIL_FROM_NAME="PropManage"
```

> **Note:** Leave `APP_KEY=` blank — it gets generated in step 4.

**3. Build and start all containers**

```cmd
docker compose up --build -d
```

This starts: nginx, PHP, PostgreSQL (Citus), Redis, queue worker, scheduler, and the marketing site.
The first build takes **3–5 minutes** to download images and install dependencies.

**4. Set up the application (run once)**

```cmd
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate --seed
docker compose exec php php artisan storage:link
```

**5. Open the app**

| URL | Panel |
|-----|-------|
| http://localhost:8000/admin | System Admin Panel |
| http://localhost:8000/app/test-client | Tenant Panel (test tenant) |
| http://localhost:5000 | Marketing Site |

**Login credentials:**

| Panel | Email | Password |
|-------|-------|----------|
| Admin | superadmin@gmail.com | password |
| Tenant | lancerbrian001@gmail.com | password |

---

## Daily Commands

```cmd
docker compose up -d          # Start all containers
docker compose down           # Stop all containers
docker compose logs -f php    # View Laravel logs
docker compose logs -f nginx  # View web server logs
```

---

## Alternative Method: XAMPP (Without Docker)

Use this only if you cannot install Docker.

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) with **PHP 8.2+**
- [PostgreSQL 14+](https://www.postgresql.org/download/windows/)
- [Composer](https://getcomposer.org/download/)
- [Node.js 18+](https://nodejs.org/)
- [Redis for Windows](https://github.com/microsoftarchive/redis/releases) (or use [Memurai](https://www.memurai.com/))

### Steps

**1. Clone the repository**

```cmd
git clone https://github.com/jessymuia/saaas.git
cd saaas
```

**2. Install PHP dependencies**

Because some packages require Linux-only PHP extensions (pcntl, posix) that are not available on Windows, use:

```cmd
composer install --ignore-platform-reqs
```

**3. Create your `.env` file**

```env
APP_NAME="PropManage SaaS"
APP_ENV=local
APP_DEBUG=true
APP_KEY=
APP_URL=http://localhost:8000
APP_TENANT_MODE=slug

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=propmanage
DB_USERNAME=postgres
DB_PASSWORD=your_postgres_password

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@propmanage.com
MAIL_FROM_NAME="PropManage"
```

**4. Create the PostgreSQL database**

Open pgAdmin or psql and run:

```sql
CREATE DATABASE propmanage;
```

**5. Generate app key and run migrations**

```cmd
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

**6. Start the application**

Open **two separate CMD windows**:

```cmd
:: Window 1 — Web server
php artisan serve --port=8000

:: Window 2 — Queue worker
php artisan queue:work
```

Open http://localhost:8000/admin in your browser.

---

## Troubleshooting

**Docker containers not starting**
- Make sure Docker Desktop is running (check the system tray)
- Run `docker compose logs` to see error details

**`composer install` fails on Windows**
- Always use `composer install --ignore-platform-reqs` on Windows
- The missing extensions (pcntl, posix) are Linux-only and only needed inside Docker

**Database connection refused**
- Docker: make sure `DB_HOST=citus-coordinator` in your `.env` (not `127.0.0.1`)
- XAMPP: make sure `DB_HOST=127.0.0.1` and PostgreSQL service is running

**Port 8000 already in use**
```cmd
docker compose down
docker compose up -d
```
Or change the port in `docker-compose.yml` from `"8000:80"` to `"8080:80"`.

**Fresh start (wipe all data)**
```cmd
docker compose down -v
docker compose up --build -d
docker compose exec php php artisan migrate --seed
```
