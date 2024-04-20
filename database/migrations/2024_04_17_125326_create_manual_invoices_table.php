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
        Schema::create('manual_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('archive')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            // foreign keys
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');

            $table->unsignedBigInteger('property_owner_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('comments',1000)->nullable();
            $table->string('invoice_status',20)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('invoice_for_month');
            $table->date('invoice_due_date')->nullable();
            $table->tinyInteger('is_confirmed')->default(0);
            $table->tinyInteger('is_generated')->default(0);
            $table->string('document_url')->nullable();

            // foreign keys
            $table->foreign('property_owner_id')->references('id')->on('property_owners');
            $table->foreign('client_id')->references('id')->on('clients');
        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE manual_invoices ALTER COLUMN id SET DEFAULT nextval('invoices_id_seq');");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_invoices');
    }
};
