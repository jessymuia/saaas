<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection  = config('audit.drivers.database.connection', config('database.default'));
        $tableName   = config('audit.drivers.database.table', 'audits');
        $morphPrefix = config('audit.user.morph_prefix', 'user');

        Schema::connection($connection)->create($tableName, function (Blueprint $table) use ($morphPrefix) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('saas_client_id')->nullable();

            $table->string($morphPrefix . '_type')->nullable();
            $table->uuid($morphPrefix . '_id')->nullable();
            $table->string('event');
            $table->uuidMorphs('auditable');
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('url')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1023)->nullable();
            $table->string('tags')->nullable();
            $table->timestampsTz();

            $table->index([$morphPrefix . '_id', $morphPrefix . '_type']);
            $table->index('saas_client_id');
        });
    }

    public function down(): void
    {
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $tableName  = config('audit.drivers.database.table', 'audits');

        Schema::connection($connection)->drop($tableName);
    }
};
