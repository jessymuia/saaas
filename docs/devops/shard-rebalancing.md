# Citus Shard Rebalancing — PropManage SaaS

> Phase 16 — DevOps & Deployment  
> These instructions apply to **local development**. Production rebalancing requires careful scheduling to avoid performance impact.

---

## When to Rebalance

- After adding new Citus worker nodes
- After significant data growth causing uneven shard distribution
- After replacing a worker node

---

## Check Current Shard Distribution

```sql
-- Connect to coordinator
PGPASSWORD=postgres psql -h localhost -p 5432 -U postgres -d propman

-- View shard placement distribution across workers
SELECT
    nodename,
    count(*) AS shard_count
FROM pg_dist_shard_placement
GROUP BY nodename
ORDER BY nodename;
```

Expected output with 2 workers and 10 shards:
```
   nodename        | shard_count
-------------------+-------------
 citus-worker-1    |           5
 citus-worker-2    |           5
```

---

## View Current Shard Sizes

```sql
-- Check size of shards on each worker (run from coordinator)
SELECT
    nodename,
    logicalrelid,
    sum(result::bigint) AS total_bytes
FROM run_command_on_shards(
    'properties',
    'SELECT pg_total_relation_size(''%s'')'
)
GROUP BY nodename, logicalrelid
ORDER BY total_bytes DESC;
```

---

## Rebalance Shards (local dev)

```bash
# Connect to coordinator and run rebalancer
docker compose exec citus-coordinator \
  psql -U postgres -d propman -c \
  "SELECT rebalance_table_shards();"
```

This uses the **default rebalancing strategy** which moves shards to achieve even distribution.

### Rebalance with Drain (graceful — preferred for production)

```sql
-- First, drain a node before removing it
SELECT citus_drain_node('citus-worker-1', 5432);

-- Then rebalance
SELECT rebalance_table_shards();
```

---

## Monitor Rebalancing Progress

```sql
-- View active rebalance jobs
SELECT * FROM citus_rebalance_status();
```

---

## Add a New Worker Node

```bash
# 1. Start new worker in docker-compose
#    Add citus-worker-3 service in docker-compose.yml

# 2. Register the new worker on the coordinator
docker compose exec citus-coordinator \
  psql -U postgres -d propman -c \
  "SELECT citus_add_node('citus-worker-3', 5432);"

# 3. Run rebalancer to move shards to the new node
docker compose exec citus-coordinator \
  psql -U postgres -d propman -c \
  "SELECT rebalance_table_shards();"
```

---

## Verify Colocation After Rebalance

```sql
-- All distributed tables must share the same colocationid
SELECT logicalrelid, colocationid, partmethod
FROM pg_dist_partition
ORDER BY colocationid, logicalrelid;
```

Or via artisan:

```bash
php artisan citus:validate-colocation
```

---

## Notes

- Rebalancing moves shard data between workers — this involves network I/O and can be slow on large datasets.
- In production, schedule rebalancing during off-peak hours.
- `rebalance_table_shards()` is safe to run on a live system but may cause temporary performance degradation.
- Never run shard rebalancing while a migration is in progress.
