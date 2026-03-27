<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $tableName  = config('audit.drivers.database.table', 'audits');
        $morphPrefix = config('audit.user.morph_prefix', 'user');

        Schema::connection($connection)->create($tableName, function (Blueprint $table) use ($morphPrefix) {
           
            $table->unsignedBigInteger('id')->autoIncrement();

            
            $table->uuid('saas_client_id')->nullable();

            $table->string($morphPrefix . '_type')->nullable();
            $table->unsignedBigInteger($morphPrefix . '_id')->nullable();
            $table->string('event');
            $table->morphs('auditable');
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('url')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1023)->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();

            
            $table->primary(['id', 'saas_client_id']);

            $table->index([$morphPrefix . '_id', $morphPrefix . '_type']);
        });

        
        DB::statement("SELECT create_distributed_table('$tableName', 'saas_client_id')");
    }

    public function down(): void
    {
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $tableName  = config('audit.drivers.database.table', 'audits');

        Schema::connection($connection)->drop($tableName);
    }
};

