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
        Schema::table('manual_invoice_items', function (Blueprint $table) {
            //
            $table->enum('category', ['ordinary', 'balance_carried_forward'])
                ->default('ordinary')
                ->after('billing_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manual_invoice_items', function (Blueprint $table) {
            //
            $table->dropColumn('category');
        });
    }
};
