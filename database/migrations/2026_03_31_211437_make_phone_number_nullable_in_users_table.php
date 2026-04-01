<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL — Citus supports SET/DROP NOT NULL but not ALTER COLUMN TYPE
        // which Laravel's ->change() generates internally even for same-type changes.
        DB::statement('ALTER TABLE users ALTER COLUMN phone_number DROP NOT NULL');
        DB::statement('ALTER TABLE users ALTER COLUMN phone_number SET DEFAULT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users ALTER COLUMN phone_number SET NOT NULL');
        DB::statement('ALTER TABLE users ALTER COLUMN phone_number DROP DEFAULT');
    }
};
