<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // 1) rename the old bytea/blob column
            $table->renameColumn('receipt_blob', 'receipt_data');
            // 2) convert it to TEXT (so it can hold Base64)
            $table->text('receipt_data')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // revert back to binary/blobs if you ever roll back
            $table->binary('receipt_data')->nullable()->change();
            $table->renameColumn('receipt_data', 'receipt_blob');
        });
    }
};
