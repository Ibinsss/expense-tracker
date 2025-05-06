<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeReceiptDataToTextOnExpensesTable extends Migration
{
    public function up(): void
{
    Schema::table('expenses', function (Blueprint $table) {
        // ONLY change the type of the existing receipt_data column:
        $table->text('receipt_data')
              ->nullable()
              ->change();
    });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('receipt_data')->after('receipt_path'); // or whatever it was before
        });
    }
}
