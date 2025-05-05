<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    Schema::table('expenses', function (Blueprint $table) {
        $table->longBlob('receipt_blob')->nullable();
    });
}
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['receipt_data', 'receipt_mime']);
        });
    }
};
