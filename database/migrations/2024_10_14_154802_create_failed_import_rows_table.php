<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('failed_import_rows', function (Blueprint $table) {
        
        $table->unsignedBigInteger('id'); 
        
        
        $table->uuid('saas_client_id');

        $table->json('data');
        $table->text('validation_error')->nullable();
        
        
        $table->unsignedBigInteger('import_id');
        $table->foreign(['import_id', 'saas_client_id'])
              ->references(['id', 'saas_client_id'])
              ->on('imports')
              ->cascadeOnDelete();

       
        $table->primary(['id', 'saas_client_id']);

        
        $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_import_rows');
    }
};

