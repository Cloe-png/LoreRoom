<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('worlds', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
        });

        // Legacy installs: if there is only one user, assign all worlds to this owner.
        $userCount = (int) DB::table('users')->count();
        if ($userCount === 1) {
            $userId = (int) DB::table('users')->value('id');
            DB::table('worlds')->whereNull('user_id')->update(['user_id' => $userId]);
        }
    }

    public function down()
    {
        Schema::table('worlds', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
