<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('faction_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faction_id')->constrained('factions')->cascadeOnDelete();
            $table->foreignId('related_faction_id')->constrained('factions')->cascadeOnDelete();
            $table->string('relation_type');
            $table->boolean('is_bidirectional')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('faction_relations');
    }
};
