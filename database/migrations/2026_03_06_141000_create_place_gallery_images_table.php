<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('place_gallery_images')) {
            Schema::create('place_gallery_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('place_id')->constrained()->cascadeOnDelete();
                $table->string('image_path');
                $table->string('caption')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('place_gallery_images');
    }
};
