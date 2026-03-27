<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    
    Schema::create('ref_unit_types', function (Blueprint $table) {
        $table = \App\Utils\AppUtils::defaultTableColumns($table, addId: true, addAuditFk: false);

        $table->string('name'); 
        $table->string('code')->nullable(); 

        $table->unique('name');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('ref_utilities');
    }
};