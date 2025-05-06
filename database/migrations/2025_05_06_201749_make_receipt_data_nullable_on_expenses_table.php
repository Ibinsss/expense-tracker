<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Handle Postgres separately, since it needs an explicit USING cast
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(<<<SQL
ALTER TABLE expenses
  ALTER COLUMN receipt_data TYPE bytea USING receipt_data::bytea,
  ALTER COLUMN receipt_data DROP NOT NULL,
  ALTER COLUMN receipt_data DROP DEFAULT
SQL
            );
        } else {
            // MySQL / SQLite / others can use the Schema change() helper
            Schema::table('expenses', function (Blueprint $table) {
                $table->binary('receipt_data')
                      ->nullable()
                      ->change();
            });
        }
    }

    public function down(): void
    {
        // Revert back to text and NOT NULL for all drivers
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(<<<SQL
ALTER TABLE expenses
  ALTER COLUMN receipt_data TYPE text USING encode(receipt_data, 'escape'),
  ALTER COLUMN receipt_data SET NOT NULL
SQL
            );
        } else {
            Schema::table('expenses', function (Blueprint $table) {
                $table->text('receipt_data')
                      ->nullable(false)
                      ->change();
            });
        }
    }
};
