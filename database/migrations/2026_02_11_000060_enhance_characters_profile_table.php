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
            $table->string('first_name')->nullable()->after('world_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('gender')->nullable()->after('last_name');
            $table->date('birth_date')->nullable()->after('gender');
            $table->date('death_date')->nullable()->after('birth_date');
            $table->boolean('has_children')->default(false)->after('death_date');
            $table->unsignedBigInteger('father_id')->nullable()->after('has_children');
            $table->unsignedBigInteger('mother_id')->nullable()->after('father_id');
            $table->boolean('has_power')->default(false)->after('role');
            $table->text('power_description')->nullable()->after('has_power');
            $table->string('image_path')->nullable()->after('power_description');
            $table->string('height')->nullable()->after('image_path');
            $table->string('silhouette')->nullable()->after('height');
            $table->string('hair_eyes')->nullable()->after('silhouette');
            $table->text('posture')->nullable()->after('hair_eyes');
            $table->text('marks')->nullable()->after('posture');
            $table->text('clothing_style')->nullable()->after('marks');
            $table->text('qualities')->nullable()->after('clothing_style');
            $table->text('flaws')->nullable()->after('qualities');
            $table->text('psychology_notes')->nullable()->after('flaws');

            $table->foreign('father_id')->references('id')->on('characters')->nullOnDelete();
            $table->foreign('mother_id')->references('id')->on('characters')->nullOnDelete();
        });

        DB::statement("UPDATE characters SET first_name = name WHERE first_name IS NULL");
    }

    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropForeign(['father_id']);
            $table->dropForeign(['mother_id']);
            $table->dropColumn([
                'first_name',
                'last_name',
                'gender',
                'birth_date',
                'death_date',
                'has_children',
                'father_id',
                'mother_id',
                'has_power',
                'power_description',
                'image_path',
                'height',
                'silhouette',
                'hair_eyes',
                'posture',
                'marks',
                'clothing_style',
                'qualities',
                'flaws',
                'psychology_notes',
            ]);
        });
    }
};

