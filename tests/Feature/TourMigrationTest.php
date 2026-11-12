<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TourMigrationTest extends TestCase
{
    public function test_existing_accounts_are_marked_complete_and_future_accounts_start_unseen(): void
    {
        $originalConnection = DB::getDefaultConnection();
        Config::set('database.connections.tour_migration_test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => false,
        ]);
        DB::purge('tour_migration_test');
        DB::setDefaultConnection('tour_migration_test');

        try {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
            });
            $existingId = DB::table('users')->insertGetId(['name' => 'Compte existant']);

            require_once database_path('migrations/2026_11_12_090000_add_tour_completed_at_to_users_table.php');
            (new \AddTourCompletedAtToUsersTable())->up();

            $this->assertNotNull(DB::table('users')->where('id', $existingId)->value('tour_completed_at'));
            $newId = DB::table('users')->insertGetId(['name' => 'Nouveau compte']);
            $this->assertNull(DB::table('users')->where('id', $newId)->value('tour_completed_at'));
        } finally {
            Schema::dropIfExists('users');
            DB::setDefaultConnection($originalConnection);
            DB::purge('tour_migration_test');
            Config::set('database.connections.tour_migration_test', null);
        }
    }
}
