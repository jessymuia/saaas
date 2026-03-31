#!/usr/bin/env bash
# =============================================================================
# Citus Bootstrap Script — PropManage SaaS
# =============================================================================
# Purpose : Idempotent setup of Citus extension + worker nodes on coordinator.
#           Safe to re-run at any time.
# Usage   : bash docker/citus-bootstrap.sh
#           Or: docker compose exec citus-coordinator bash /docker-entrypoint-initdb.d/citus-bootstrap.sh
# =============================================================================

set -euo pipefail

COORDINATOR_HOST="${COORDINATOR_HOST:-localhost}"
COORDINATOR_PORT="${COORDINATOR_PORT:-5432}"
DB_NAME="${POSTGRES_DB:-propman}"
DB_USER="${POSTGRES_USER:-postgres}"
DB_PASSWORD="${POSTGRES_PASSWORD:-postgres}"

WORKER1_HOST="${WORKER1_HOST:-citus-worker-1}"
WORKER1_PORT="${WORKER1_PORT:-5432}"
WORKER2_HOST="${WORKER2_HOST:-citus-worker-2}"
WORKER2_PORT="${WORKER2_PORT:-5432}"

export PGPASSWORD="${DB_PASSWORD}"

PSQL="psql -h ${COORDINATOR_HOST} -p ${COORDINATOR_PORT} -U ${DB_USER} -d ${DB_NAME}"

echo "==> [Citus Bootstrap] Starting Citus provisioning on coordinator ${COORDINATOR_HOST}:${COORDINATOR_PORT}"

# -----------------------------------------------------------------------------
# 1. Install Citus extension (idempotent)
# -----------------------------------------------------------------------------
echo "--> Creating Citus extension (idempotent)..."
$PSQL -c "CREATE EXTENSION IF NOT EXISTS citus;"

# -----------------------------------------------------------------------------
# 2. Enable pg_stat_statements for query monitoring (idempotent)
# -----------------------------------------------------------------------------
echo "--> Enabling pg_stat_statements extension (idempotent)..."
$PSQL -c "CREATE EXTENSION IF NOT EXISTS pg_stat_statements;" 2>/dev/null || echo "    (pg_stat_statements not available in this image — skipping)"

# -----------------------------------------------------------------------------
# 3. Register worker nodes (idempotent — skip if already added)
# -----------------------------------------------------------------------------
echo "--> Registering Citus worker nodes..."

for WORKER_HOST in "${WORKER1_HOST}" "${WORKER2_HOST}"; do
    WORKER_PORT=5432
    EXISTING=$($PSQL -tAc "SELECT count(*) FROM pg_dist_node WHERE nodename='${WORKER_HOST}' AND nodeport=${WORKER_PORT};" 2>/dev/null || echo "0")
    if [ "${EXISTING}" -eq "0" ] 2>/dev/null; then
        echo "    Adding worker: ${WORKER_HOST}:${WORKER_PORT}"
        $PSQL -c "SELECT citus_add_node('${WORKER_HOST}', ${WORKER_PORT});" 2>/dev/null \
            || $PSQL -c "SELECT master_add_node('${WORKER_HOST}', ${WORKER_PORT});" 2>/dev/null \
            || echo "    (Worker registration skipped — may need manual setup)"
    else
        echo "    Worker already registered: ${WORKER_HOST}:${WORKER_PORT}"
    fi
done

# -----------------------------------------------------------------------------
# 4. Show cluster node status
# -----------------------------------------------------------------------------
echo "--> Citus node status:"
$PSQL -c "SELECT nodeid, nodename, nodeport, isactive FROM pg_dist_node;" 2>/dev/null || echo "    (pg_dist_node not available yet)"

# -----------------------------------------------------------------------------
# 5. Configure shard count to 10 for local development
#    Note: In production scale to 64-128.
#    This is applied per-table after create_distributed_table().
# -----------------------------------------------------------------------------
echo "--> Setting citus.shard_count = 10 for local development..."
$PSQL -c "ALTER SYSTEM SET citus.shard_count = 10;" 2>/dev/null || true
$PSQL -c "SELECT pg_reload_conf();" 2>/dev/null || true

# -----------------------------------------------------------------------------
# 6. Enable Citus stat statements (if available)
# -----------------------------------------------------------------------------
echo "--> Enabling citus_stat_statements (if available)..."
$PSQL -c "SELECT citus_stat_statements_reset();" 2>/dev/null || echo "    (citus_stat_statements not available — skipping)"

# -----------------------------------------------------------------------------
# 7. Verify Citus version
# -----------------------------------------------------------------------------
echo "--> Citus version:"
$PSQL -c "SELECT citus_version();" 2>/dev/null || echo "    (Could not read Citus version)"

# -----------------------------------------------------------------------------
# 8. Show distributed tables colocation status (if any tables are distributed)
# -----------------------------------------------------------------------------
echo "--> Distributed table colocation status:"
$PSQL -c "SELECT logicalrelid, colocationid, partmethod FROM pg_dist_partition ORDER BY colocationid, logicalrelid;" 2>/dev/null || echo "    (No distributed tables yet)"

echo "==> [Citus Bootstrap] Done. ✓"
