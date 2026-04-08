<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: false);

            $table->uuid('saas_client_id')->nullable()->index();
            $table->string('name');
            $table->string('email')->default('tenant@example.com');
            $table->string('phone_number', 20);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
