<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('saas_client_id');
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('open');
            $table->timestamps();

            
            $table->primary(['id', 'saas_client_id']);

            $table->foreign('saas_client_id')
                ->references('id')
                ->on('saas_clients')
                ->cascadeOnDelete();

            $table->index('saas_client_id');
        });

        
        DB::statement("SELECT create_distributed_table('support_tickets', 'saas_client_id', colocate_with => 'users')");
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
