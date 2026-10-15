<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('places', function (Blueprint $table) {
            $table->string('short_description', 280)->nullable();
            $table->text('description')->nullable();
            $table->json('atmosphere')->nullable();
            $table->string('climate')->nullable();
            $table->unsignedBigInteger('population')->nullable();
            $table->json('alt_names')->nullable();
            $table->foreignId('parent_id')->nullable()->after('world_id')->constrained('places')->nullOnDelete();
            $table->string('government')->nullable();
            $table->string('religion')->nullable();
            $table->json('languages')->nullable();
            $table->text('economy')->nullable();
            $table->text('dangers')->nullable();
            $table->string('founded_at')->nullable();
            $table->text('history')->nullable();
            $table->string('status', 24)->default('inconnu');
            $table->text('private_notes')->nullable();
            $table->string('visibility', 24)->default('brouillon');
            $table->index(['world_id', 'name'], 'places_world_name_index');
            $table->index('parent_id');
        });

        Schema::create('place_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('place_place', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
            $table->foreignId('connected_place_id')->constrained('places')->cascadeOnDelete();
            $table->string('travel_note')->nullable();
            $table->timestamps();
            $table->unique(['place_id', 'connected_place_id']);
        });

        Schema::create('place_leaders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->timestamps();
            $table->unique(['place_id', 'character_id']);
        });

        foreach (['place_faction', 'place_species', 'place_character', 'chronicle_place'] as $pivot) {
            Schema::create($pivot, function (Blueprint $table) use ($pivot) {
                $table->id();
                $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
                $related = [
                    'place_faction' => ['faction_id', 'factions'],
                    'place_species' => ['species_id', 'species'],
                    'place_character' => ['character_id', 'characters'],
                    'chronicle_place' => ['chronicle_id', 'chronicles'],
                ][$pivot];
                $table->foreignId($related[0])->constrained($related[1])->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['place_id', $related[0]]);
            });
        }
    }

    public function down()
    {
        foreach (['chronicle_place', 'place_character', 'place_species', 'place_faction', 'place_leaders', 'place_place', 'place_points'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('places', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex('places_world_name_index');
            $table->dropIndex(['parent_id']);
            $table->dropColumn(['short_description', 'description', 'atmosphere', 'climate', 'population', 'alt_names', 'parent_id', 'government', 'religion', 'languages', 'economy', 'dangers', 'founded_at', 'history', 'status', 'private_notes', 'visibility']);
        });
    }
};
