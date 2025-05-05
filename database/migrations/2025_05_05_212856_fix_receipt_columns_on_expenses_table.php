<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // If receipt_blob already exists, rename it ⬇
            if (Schema::hasColumn('expenses', 'receipt_blob')) {
                $table->renameColumn('receipt_blob', 'receipt_data');
            }

            // Add the MIME‑type column if it’s missing
            if (! Schema::hasColumn('expenses', 'receipt_mime')) {
                $table->string('receipt_mime', 100)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'receipt_mime')) {
                $table->dropColumn('receipt_mime');
            }
            if (Schema::hasColumn('expenses', 'receipt_data')) {
                $table->renameColumn('receipt_data', 'receipt_blob');
            }
        });
    }
};

