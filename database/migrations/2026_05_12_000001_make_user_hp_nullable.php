<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user') || !Schema::hasColumn('user', 'hp')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `user` MODIFY `hp` VARCHAR(13) NULL');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('user') || !Schema::hasColumn('user', 'hp')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `user` MODIFY `hp` VARCHAR(13) NOT NULL');
        }
    }
};
