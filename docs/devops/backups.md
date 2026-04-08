# On-Demand Database Backups — PropManage SaaS

> Phase 16 — DevOps & Deployment  
> These instructions cover **local / development** backups. Production backups should be automated via your cloud provider (e.g., AWS RDS snapshots, managed Citus Cloud backups).

---

## Prerequisites

- Docker Compose stack is running (`docker compose up -d`)
- `pg_dump` available locally **or** run inside the `citus-coordinator` container

---

## 1. Full Database Dump (all tables)

### Option A — Run from host (requires `pg_dump` installed)

```bash
PGPASSWORD=postgres pg_dump \
  -h localhost \
  -p 5432 \
  -U postgres \
  -d propman \
  --format=custom \
  --file="backups/propman-$(date +%Y%m%d-%H%M%S).dump"
```

### Option B — Run inside the coordinator container

```bash
docker compose exec citus-coordinator \
  pg_dump -U postgres -d propman \
  --format=custom \
  --file=/tmp/propman-backup.dump

# Copy backup out of container
docker compose cp citus-coordinator:/tmp/propman-backup.dump \
  ./backups/propman-$(date +%Y%m%d-%H%M%S).dump
```

---

## 2. Schema-Only Backup (for migration review)

```bash
PGPASSWORD=postgres pg_dump \
  -h localhost -p 5432 \
  -U postgres -d propman \
  --schema-only \
  --file="backups/propman-schema-$(date +%Y%m%d).sql"
```

---

## 3. Per-Tenant Data Export (for tenant offboarding or debugging)

Replace `<TENANT_UUID>` with the target tenant's `saas_client_id`:

```bash
PGPASSWORD=postgres psql -h localhost -p 5432 -U postgres -d propman -c \
  "COPY (SELECT * FROM properties WHERE saas_client_id = '<TENANT_UUID>') TO STDOUT WITH CSV HEADER" \
  > backups/tenant-<TENANT_UUID>-properties-$(date +%Y%m%d).csv
```

Repeat for each table as needed.

---

## 4. Restore from Backup

```bash
PGPASSWORD=postgres pg_restore \
  -h localhost -p 5432 \
  -U postgres -d propman \
  --clean --if-exists \
  backups/propman-YYYYMMDD-HHMMSS.dump
```

> ⚠️ **Warning:** `--clean` drops and recreates objects. Only use on a dev database.

---

## 5. Backup Checklist (dev)

- [ ] Create `backups/` directory (already in `.gitignore`)
- [ ] Run a full dump before any migration
- [ ] Verify dump file size is non-zero
- [ ] Test restore on a separate local database
- [ ] Keep at least 3 rolling backups locally

---

## Notes

- Citus distributes table data across shards on workers. `pg_dump` on the **coordinator** captures the logical view of all data automatically.
- For production, use **Citus Cloud point-in-time recovery** or configure **pgBackRest / Barman** on the coordinator.
- Always include worker nodes in production backup strategy.
