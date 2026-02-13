<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('characters', 'has_brother_sister')) {
            Schema::table('characters', function (Blueprint $table) {
                $table->boolean('has_brother_sister')->default(false)->after('has_children');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('characters', 'has_brother_sister')) {
            Schema::table('characters', function (Blueprint $table) {
                $table->dropColumn('has_brother_sister');
            });
        }
    }
};
