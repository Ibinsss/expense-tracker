<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // bytea in Postgres ‑— use binary() in Laravel
            $table->binary('receipt_data')->nullable();
            $table->string('receipt_mime', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['receipt_data', 'receipt_mime']);
        });
    }
};

