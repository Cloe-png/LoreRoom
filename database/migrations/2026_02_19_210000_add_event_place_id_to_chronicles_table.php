<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chronicles', function (Blueprint $table) {
            $table->foreignId('event_place_id')
                ->nullable()
                ->after('end_date')
                ->constrained('places')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('chronicles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_place_id');
        });
    }
};
