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
        Schema::create('tenancy_agreement_files', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table);

            $table->unsignedBigInteger('tenancy_agreement_id');
            $table->string('name');
            $table->string('path');
            $table->string('mime_type',20);
            $table->string('extension',10);
            $table->integer('size');
            $table->string('description')->nullable();

            $table->foreign('tenancy_agreement_id')->references('id')->on('tenancy_agreements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenancy_agreement_files');
    }
};
