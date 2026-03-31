<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("SET LOCAL citus.multi_shard_modify_mode TO 'sequential'");

        $isDistributed = DB::selectOne("
            SELECT 1 FROM pg_dist_partition
            WHERE logicalrelid = 'saas_clients'::regclass
        ");

        if ($isDistributed) {
            DB::statement("SELECT undistribute_table('saas_clients')");
        }

        DB::statement("SELECT create_reference_table('saas_clients')");

        if (Schema::hasTable('properties')) {
            Schema::table('properties', function (Blueprint $table) {
                if (!Schema::hasColumn('properties', 'saas_client_id')) {
                    $table->uuid('saas_client_id')->nullable()->after('id');
                    $table->foreign('saas_client_id')
                        ->references('id')
                        ->on('saas_clients')
                        ->cascadeOnDelete();
                }
            });
        }

        if (Schema::hasTable('units')) {
            Schema::table('units', function (Blueprint $table) {
                if (!Schema::hasColumn('units', 'saas_client_id')) {
                    $table->uuid('saas_client_id')->nullable()->after('id');
                    $table->foreign('saas_client_id')
                        ->references('id')
                        ->on('saas_clients')
                        ->cascadeOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        // Cannot rollback Citus distributed table changes
        // The partition column cannot be dropped from distributed tables
    }
};