<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeReceiptDataToTextOnExpensesTable extends Migration
{
    public function up(): void
{
    Schema::table('expenses', function (Blueprint $table) {
        // this line is failing because there's no "receipt_blob" column in Postgres:
        $table->renameColumn('receipt_data_old', 'receipt_data');
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
