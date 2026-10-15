<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->unsignedSmallInteger('age')->nullable()->after('birth_date');
            $table->string('sexual_orientation')->nullable()->after('gender');
            $table->string('clothing_style_mode')->nullable()->after('marks');
            $table->text('appearance_description')->nullable()->after('clothing_style_mode');
            $table->text('likes')->nullable()->after('summary');
            $table->text('dislikes')->nullable()->after('likes');
        });
    }

    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn([
                'age',
                'sexual_orientation',
                'clothing_style_mode',
                'appearance_description',
                'likes',
                'dislikes',
            ]);
        });
    }
};
