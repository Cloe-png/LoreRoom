<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('imaginary_maps')) {
            Schema::drop('imaginary_maps');
        }

        if (Schema::hasColumn('worlds', 'map_path')) {
            Schema::table('worlds', function (Blueprint $table) {
                $table->dropColumn('map_path');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasTable('imaginary_maps')) {
            Schema::create('imaginary_maps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('world_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->string('map_type')->nullable();
                $table->string('image_url', 2048)->nullable();
                $table->string('image_path')->nullable();
                $table->text('description')->nullable();
                $table->string('status')->default('draft');
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('worlds', 'map_path')) {
            Schema::table('worlds', function (Blueprint $table) {
                $table->string('map_path')->nullable()->after('slug');
            });
        }
    }
};
