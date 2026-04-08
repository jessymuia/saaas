<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_clients', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->unique();

            $table->foreignUuid('plan_id')->nullable()->constrained('plans')->nullOnDelete();

            $table->string('status')->default('trial');
            $table->json('data')->nullable();

            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_clients');
    }
};
