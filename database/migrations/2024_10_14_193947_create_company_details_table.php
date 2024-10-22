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
        Schema::create('company_details', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->string('name');
            $table->string('email');
            $table->string('phone_number', 20);
            $table->string('logo')->nullable();
            $table->string('location'); //physical location
            $table->string('address'); //physical address
            $table->string('account_name', 100);
            $table->string('account_number', 20);
            $table->string('bank_name', 50);
            $table->string('bank_branch');
            $table->string('branch_swift_code', 20);
            $table->integer('mpesa_paybill_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_details');
    }
};
