<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            
           
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

            $table->uuid('saas_client_id');

            $table->string('name');
            $table->string('email')->default('tenant@example.com');
            $table->string('phone_number', 20);

            
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('deleted_by');

            
            $table->primary(['id', 'saas_client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
