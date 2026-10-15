<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapter_chronicle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->foreignId('chronicle_id')->constrained('chronicles')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['chapter_id', 'chronicle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_chronicle');
    }
};
