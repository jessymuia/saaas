<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Citus table distribution step — skipped on standard PostgreSQL (Replit environment).
     * All tables remain as regular PostgreSQL tables without distributed sharding.
     */
    public function up(): void
    {
        // No-op: Citus extension not available in this environment
    }

    public function down(): void
    {
        // No-op
    }
};
