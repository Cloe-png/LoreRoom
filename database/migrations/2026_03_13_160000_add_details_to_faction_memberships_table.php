<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('faction_memberships', function (Blueprint $table) {
            $table->string('grade')->nullable()->after('role');
            $table->date('joined_at')->nullable()->after('grade');
            $table->string('status')->nullable()->after('joined_at');
        });
    }

    public function down()
    {
        Schema::table('faction_memberships', function (Blueprint $table) {
            $table->dropColumn(['grade', 'joined_at', 'status']);
        });
    }
};
