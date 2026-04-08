<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds saas_client_id to properties and units tables.
     * Citus-specific distribution calls have been removed for standard PostgreSQL compatibility.
     */
    public function up(): void
    {
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
        if (Schema::hasTable('units') && Schema::hasColumn('units', 'saas_client_id')) {
            Schema::table('units', function (Blueprint $table) {
                $table->dropForeign(['saas_client_id']);
                $table->dropColumn('saas_client_id');
            });
        }

        if (Schema::hasTable('properties') && Schema::hasColumn('properties', 'saas_client_id')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropForeign(['saas_client_id']);
                $table->dropColumn('saas_client_id');
            });
        }
    }
};
