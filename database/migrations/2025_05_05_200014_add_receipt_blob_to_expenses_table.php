<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
       
            $table->binary('receipt_data')->nullable();
            $table->string('receipt_mime')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['receipt_data','receipt_mime']);
        });
    }
    
};
