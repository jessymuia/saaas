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

        
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'saas_client_id')) {
                $table->uuid('saas_client_id')->nullable()->after('id');
                $table->foreign('saas_client_id')->references('id')->on('saas_clients')->cascadeOnDelete();
            }
        });

        Schema::table('units', function (Blueprint $table) {
            if (!Schema::hasColumn('units', 'saas_client_id')) {
                $table->uuid('saas_client_id')->nullable()->after('id');
                $table->foreign('saas_client_id')->references('id')->on('saas_clients')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        DB::statement("SET LOCAL citus.multi_shard_modify_mode TO 'sequential'");

        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['saas_client_id']);
            $table->dropColumn('saas_client_id');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['saas_client_id']);
            $table->dropColumn('saas_client_id');
        });

        $isReference = DB::selectOne("
            SELECT 1 FROM pg_dist_partition
            WHERE logicalrelid = 'saas_clients'::regclass
        ");

        if ($isReference) {
            DB::statement("SELECT undistribute_table('saas_clients')");
        }

        DB::statement("SELECT create_distributed_table('saas_clients', 'id')");
    }
};
