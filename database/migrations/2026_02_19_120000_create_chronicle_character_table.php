<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chronicle_character', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chronicle_id')->constrained('chronicles')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['chronicle_id', 'character_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chronicle_character');
    }
};
