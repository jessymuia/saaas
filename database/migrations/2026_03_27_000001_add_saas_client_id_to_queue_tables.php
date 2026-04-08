<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        // ── jobs ─────────────────────────────────────────────────────
        Schema::table('jobs', function (Blueprint $table) {
            $table->uuid('saas_client_id')->nullable()->after('id')->index();
        });

        // ── failed_jobs ───────────────────────────────────────────────
        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->uuid('saas_client_id')->nullable()->after('id')->index();
        });

        
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('saas_client_id');
        });

        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->dropColumn('saas_client_id');
        });
    }
};
