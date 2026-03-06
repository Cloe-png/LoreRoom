<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_world_id')
                ->nullable()
                ->after('role')
                ->constrained('worlds')
                ->nullOnDelete();
        });

        // Backfill current_world_id from worlds.user_id.
        $users = DB::table('users')->select(['id', 'current_world_id'])->get();
        foreach ($users as $user) {
            if (!empty($user->current_world_id)) {
                continue;
            }

            $firstWorldId = DB::table('worlds')
                ->where('user_id', (int) $user->id)
                ->orderBy('id')
                ->value('id');

            if ($firstWorldId) {
                DB::table('users')
                    ->where('id', (int) $user->id)
                    ->update(['current_world_id' => (int) $firstWorldId]);
            }
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('current_world_id');
        });
    }
};
