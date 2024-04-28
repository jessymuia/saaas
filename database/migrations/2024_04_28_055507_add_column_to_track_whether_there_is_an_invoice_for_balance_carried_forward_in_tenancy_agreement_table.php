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
        Schema::table('tenancy_agreements', function (Blueprint $table) {
            //
            $table->boolean('has_invoice_for_balance_carried_forward')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenancy_agreements', function (Blueprint $table) {
            //
            $table->dropColumn('has_invoice_for_balance_carried_forward');
        });
    }
};
