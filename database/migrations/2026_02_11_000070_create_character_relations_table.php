<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('character_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('to_character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('relation_type');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('intensity')->nullable();
            $table->boolean('is_bidirectional')->default(true);
            $table->timestamps();

            $table->index(['from_character_id', 'to_character_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('character_relations');
    }
};

