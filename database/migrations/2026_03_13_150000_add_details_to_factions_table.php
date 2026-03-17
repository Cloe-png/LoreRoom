<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('factions', function (Blueprint $table) {
            $table->string('motto')->nullable()->after('summary');
            $table->date('founded_at')->nullable()->after('motto');
            $table->string('status')->nullable()->after('founded_at');
            $table->foreignId('leader_id')->nullable()->after('status')->constrained('characters')->nullOnDelete();
            $table->foreignId('co_leader_id')->nullable()->after('leader_id')->constrained('characters')->nullOnDelete();
            $table->foreignId('founder_id')->nullable()->after('co_leader_id')->constrained('characters')->nullOnDelete();
            $table->string('logo_path')->nullable()->after('founder_id');
        });
    }

    public function down()
    {
        Schema::table('factions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('leader_id');
            $table->dropConstrainedForeignId('co_leader_id');
            $table->dropConstrainedForeignId('founder_id');
            $table->dropColumn(['motto', 'founded_at', 'status', 'logo_path']);
        });
    }
};
