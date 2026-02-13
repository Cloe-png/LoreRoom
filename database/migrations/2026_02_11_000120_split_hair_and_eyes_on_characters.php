<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->string('hair_color')->nullable()->after('silhouette');
            $table->string('eye_color')->nullable()->after('hair_color');
        });

        DB::statement("UPDATE characters SET hair_color = hair_eyes WHERE hair_color IS NULL AND hair_eyes IS NOT NULL");
        DB::statement("UPDATE characters SET eye_color = hair_eyes WHERE eye_color IS NULL AND hair_eyes IS NOT NULL");
    }

    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn(['hair_color', 'eye_color']);
        });
    }
};

