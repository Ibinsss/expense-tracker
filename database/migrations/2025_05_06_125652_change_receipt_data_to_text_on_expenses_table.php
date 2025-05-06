<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // change bytea → text, and ensure it's nullable
            $table->text('receipt_data')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // if you ever roll back, revert to bytea
            $table->binary('receipt_data')->nullable()->change();
        });
    }
};
