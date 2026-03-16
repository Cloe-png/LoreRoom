<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('places', 'type')) {
            Schema::table('places', function (Blueprint $table) {
                $table->string('type', 40)->nullable()->after('name');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('places', 'type')) {
            Schema::table('places', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
