<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('faction_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faction_id')->constrained('factions')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['faction_id', 'character_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('faction_memberships');
    }
};
