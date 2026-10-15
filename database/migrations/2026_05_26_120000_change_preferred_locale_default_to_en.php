<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE users MODIFY preferred_locale VARCHAR(5) NOT NULL DEFAULT 'en'");
        DB::table('users')
            ->whereNull('preferred_locale')
            ->update(['preferred_locale' => 'en']);
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY preferred_locale VARCHAR(5) NOT NULL DEFAULT 'fr'");
    }
};
