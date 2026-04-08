<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->timestamp('grace_ends_at')->nullable()->after('trial_ends_at');
            $table->timestamp('last_reminded_at')->nullable()->after('grace_ends_at');
            $table->integer('reminder_count')->default(0)->after('last_reminded_at');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['grace_ends_at', 'last_reminded_at', 'reminder_count']);
        });
    }
};