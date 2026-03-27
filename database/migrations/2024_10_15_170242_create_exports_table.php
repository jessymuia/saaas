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
    Schema::create('exports', function (Blueprint $table) {
        
        $table->unsignedBigInteger('id'); 

       
        $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: false, addAuditFk: false);

        $table->string('file_disk');
        $table->string('file_name')->nullable();
        $table->string('exporter');
        $table->unsignedInteger('processed_rows')->default(0);
        $table->unsignedInteger('total_rows');
        $table->unsignedInteger('successful_rows')->default(0);

        $table->unsignedBigInteger('user_id');
        $table->uuid('saas_client_id'); 

        
        $table->foreign(['user_id', 'saas_client_id'])
                ->references(['id', 'saas_client_id'])
                ->on('users')
                ->onDelete('cascade');

        
        $table->primary(['id', 'saas_client_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exports');
    }
};

