<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('character_jobs', function (Blueprint $table) {
            $table->foreignId('job_id')->nullable()->after('character_id')->constrained('jobs')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('character_jobs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_id');
        });
    }
};
