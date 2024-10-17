<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->string('name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('logo')->nullable();
            $table->string('location'); //physical location
            $table->string('address'); //physical address
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank_name');
            $table->string('bank_branch');
            $table->string('branch_swift_code');
            $table->integer('mpesa_paybill_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
