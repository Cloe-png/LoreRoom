<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->string('aliases')->nullable()->after('last_name');
            $table->string('status')->default('vivant')->after('death_date');
            $table->foreignId('birth_place_id')->nullable()->after('mother_id')->constrained('places')->nullOnDelete();
            $table->foreignId('residence_place_id')->nullable()->after('birth_place_id')->constrained('places')->nullOnDelete();
            $table->text('short_term_goal')->nullable()->after('role');
            $table->text('long_term_goal')->nullable()->after('short_term_goal');
            $table->text('secrets')->nullable()->after('long_term_goal');
            $table->boolean('secrets_is_private')->default(true)->after('secrets');
            $table->text('voice_tics')->nullable()->after('psychology_notes');
            $table->unsignedTinyInteger('power_level')->nullable()->after('has_power');
        });

        Schema::create('character_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('name');
            $table->string('rarity', 40)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('character_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->date('event_date')->nullable();
            $table->string('title');
            $table->text('details')->nullable();
            $table->timestamps();
        });

        Schema::create('character_gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('character_gallery_images');
        Schema::dropIfExists('character_events');
        Schema::dropIfExists('character_items');

        Schema::table('characters', function (Blueprint $table) {
            $table->dropForeign(['birth_place_id']);
            $table->dropForeign(['residence_place_id']);
            $table->dropColumn([
                'birth_place_id',
                'residence_place_id',
                'aliases',
                'status',
                'short_term_goal',
                'long_term_goal',
                'secrets',
                'secrets_is_private',
                'voice_tics',
                'power_level',
            ]);
        });
    }
};
