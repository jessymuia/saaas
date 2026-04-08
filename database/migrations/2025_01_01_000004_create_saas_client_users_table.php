<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_client_users', function (Blueprint $table) {
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true);

            $table->foreignUuid('saas_client_id')->constrained('saas_clients')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 30)->nullable();

            $table->index(['saas_client_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_client_users');
    }
};
