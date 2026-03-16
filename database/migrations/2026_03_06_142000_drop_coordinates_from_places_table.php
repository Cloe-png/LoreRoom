<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('places', function (Blueprint $table) {
            $columns = [];
            foreach (['latitude', 'longitude', 'map_x', 'map_y'] as $col) {
                if (Schema::hasColumn('places', $col)) {
                    $columns[] = $col;
                }
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }

    public function down()
    {
        Schema::table('places', function (Blueprint $table) {
            if (!Schema::hasColumn('places', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('region');
            }
            if (!Schema::hasColumn('places', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('places', 'map_x')) {
                $table->decimal('map_x', 8, 2)->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('places', 'map_y')) {
                $table->decimal('map_y', 8, 2)->nullable()->after('map_x');
            }
        });
    }
};
