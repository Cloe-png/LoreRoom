<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('character_gallery_images', function (Blueprint $table) {
            $table->unsignedBigInteger('world_id')->nullable()->after('character_id');
            $table->string('title', 60)->nullable()->after('image_path');
            $table->string('display_path')->nullable()->after('title');
            $table->text('description')->nullable()->after('caption');
            $table->unsignedInteger('position')->default(0)->after('sort_order');
            $table->softDeletes();
            $table->index(['world_id', 'character_id', 'deleted_at'], 'char_gallery_world_character_deleted_idx');
        });

        DB::table('character_gallery_images')->orderBy('id')->chunk(200, function ($images) {
            foreach ($images as $image) {
                $worldId = DB::table('characters')->where('id', $image->character_id)->value('world_id');
                DB::table('character_gallery_images')->where('id', $image->id)->update([
                    'world_id' => $worldId,
                    'title' => $image->caption ? mb_substr($image->caption, 0, 60) : 'Photo',
                    'display_path' => $image->image_path,
                    'description' => $image->caption,
                    'position' => $image->sort_order,
                ]);
            }
        });

        Schema::table('character_gallery_images', function (Blueprint $table) {
            $table->foreign('world_id')->references('id')->on('worlds')->cascadeOnDelete();
        });

        Schema::create('gallery_image_mentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_image_id')->constrained('character_gallery_images')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->unique(['gallery_image_id', 'character_id'], 'gallery_image_mentions_unique');
            $table->index('character_id', 'gallery_image_mentions_character_idx');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gallery_image_mentions');
        Schema::table('character_gallery_images', function (Blueprint $table) {
            $table->dropForeign(['world_id']);
            $table->dropIndex('char_gallery_world_character_deleted_idx');
            $table->dropColumn(['world_id', 'title', 'display_path', 'description', 'position', 'deleted_at']);
        });
    }
};
