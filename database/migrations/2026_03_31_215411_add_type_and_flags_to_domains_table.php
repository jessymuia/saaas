<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            if (!Schema::hasColumn('domains', 'type')) {
                $table->string('type')->default('subdomain')->after('domain');
            }
            if (!Schema::hasColumn('domains', 'is_primary')) {
                $table->boolean('is_primary')->default(true)->after('type');
            }
            if (!Schema::hasColumn('domains', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('is_primary');
            }
        });
    }

    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn(['type', 'is_primary', 'is_verified']);
        });
    }
};
