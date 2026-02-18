<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('worlds', function (Blueprint $table) {
            $table->string('geography_type', 20)->default('pays')->after('name');
        });
    }

    public function down()
    {
        Schema::table('worlds', function (Blueprint $table) {
            $table->dropColumn('geography_type');
        });
    }
};

