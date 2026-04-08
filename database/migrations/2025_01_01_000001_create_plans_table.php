<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            // addAuditFk: false — plans are managed by SystemAdmins, not tenant users
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: false);

            $table->string('name', 100);
            $table->string('slug', 50)->unique();
            $table->string('description', 500)->nullable();
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->integer('max_properties')->default(-1);
            $table->integer('max_units')->default(-1);
            $table->integer('max_users')->default(-1);
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
