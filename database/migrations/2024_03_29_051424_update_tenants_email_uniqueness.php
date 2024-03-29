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
        Schema::table('tenants', function (Blueprint $table) {
            // remove the uniqeness of email column
            $table->string('email')
                ->default("hamudrealtorsltd@gmail.com")
                ->change();
            // drop the unique index
            $table->dropUnique('tenants_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tenants', function (Blueprint $table) {
            // add the uniqueness of email column
            $table->string('email')->unique()->change();
        });
    }
};
