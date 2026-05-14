<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faction_memberships', function (Blueprint $table) {
            $table->dropUnique(['faction_id', 'character_id']);
            $table->index(['faction_id', 'character_id'], 'faction_memberships_faction_character_index');
        });
    }

    public function down(): void
    {
        Schema::table('faction_memberships', function (Blueprint $table) {
            $table->dropIndex('faction_memberships_faction_character_index');
            $table->unique(['faction_id', 'character_id']);
        });
    }
};
