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
            $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: true, addStatus: false);

            $table->foreignUuid('saas_client_id')->index()->constrained('saas_clients')->cascadeOnDelete();
            $table->string('subject');
            $table->text('message');
            $table->string('status', 20)->default('open')
                ->comment('open | in_progress | resolved | closed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
