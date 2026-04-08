<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            // addAuditFk: false — domains are managed by SystemAdmins, not tenant users
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: false);

            $table->foreignUuid('saas_client_id')->constrained('saas_clients')->cascadeOnDelete();
            $table->string('domain')->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
