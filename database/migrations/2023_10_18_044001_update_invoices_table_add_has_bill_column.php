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
        //
        Schema::table('invoices',function (Blueprint $table){
            $table->tinyInteger('has_bill')->default(0)->after('consumption');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('invoices',function (Blueprint $table){
            $table->dropColumn('has_bill');
        });
    }
};
