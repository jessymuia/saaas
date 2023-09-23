<?php

namespace App\Utils;

use Illuminate\Database\Schema\Blueprint;

class AppUtils
{
    public static function defaultTableColumns(Blueprint $table)
    {
        $table->id();
        $table->timestamps();
        $table->softDeletes();
        $table->tinyInteger('status')->default(1);
        $table->tinyInteger('archive')->default(0);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->unsignedBigInteger('deleted_by')->nullable();

        // foreign keys
        $table->foreign('created_by')->references('id')->on('users');
        $table->foreign('updated_by')->references('id')->on('users');
        $table->foreign('deleted_by')->references('id')->on('users');

        return $table;
    }
}
