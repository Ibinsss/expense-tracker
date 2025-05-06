<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Change the column to allow NULL
            $table->binary('receipt_data')
                  ->nullable()
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // If you really want to roll back, you could make it NOT NULL again:
            $table->binary('receipt_data')
                  ->nullable(false)
                  ->change();
        });
    }
};
