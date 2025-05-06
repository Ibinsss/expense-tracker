<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeReceiptDataToTextOnExpensesTable extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // remove whatever was there (this WILL delete existing receipt_data!)
            $table->dropColumn('receipt_data');
        });

        Schema::table('expenses', function (Blueprint $table) {
            // recreate as text
            $table->text('receipt_data')->after('receipt_path');
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('receipt_data')->after('receipt_path'); // or whatever it was before
        });
    }
}
