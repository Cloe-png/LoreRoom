<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('character_exes')) {
            Schema::create('character_exes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
                $table->foreignId('ex_character_id')->constrained('characters')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['character_id', 'ex_character_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('character_exes');
    }
};
