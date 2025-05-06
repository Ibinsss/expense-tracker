<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses','receipt_data')) {
                $table->binary('receipt_data')->nullable();
            }
            if (! Schema::hasColumn('expenses','receipt_mime')) {
                $table->string('receipt_mime')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['receipt_data', 'receipt_mime']);
        });
    }
};

