<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->foreignId('spouse_id')
                ->nullable()
                ->after('mother_id')
                ->constrained('characters')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropConstrainedForeignId('spouse_id');
        });
    }
};

