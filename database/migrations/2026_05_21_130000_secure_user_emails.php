<?php

use App\Models\User;
use App\Support\EmailPrivacy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SecureUserEmails extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email_hash', 64)->nullable()->after('email');
        });

        DB::statement('ALTER TABLE users DROP INDEX users_email_unique');
        DB::statement('ALTER TABLE users MODIFY email TEXT NOT NULL');

        User::query()
            ->select(['id', 'email'])
            ->orderBy('id')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    $rawEmail = $user->getRawOriginal('email');
                    $normalized = EmailPrivacy::normalize($rawEmail);

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'email' => EmailPrivacy::encrypt($normalized),
                            'email_hash' => EmailPrivacy::hash($normalized),
                        ]);
                }
            });

        DB::statement('ALTER TABLE users MODIFY email_hash VARCHAR(64) NOT NULL');
        DB::statement('ALTER TABLE users ADD UNIQUE INDEX users_email_hash_unique (email_hash)');
    }

    public function down()
    {
        User::query()
            ->select(['id', 'email'])
            ->orderBy('id')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    $decrypted = EmailPrivacy::decrypt($user->getRawOriginal('email'));

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'email' => EmailPrivacy::normalize($decrypted),
                        ]);
                }
            });

        DB::statement('ALTER TABLE users DROP INDEX users_email_hash_unique');
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE users ADD UNIQUE INDEX users_email_unique (email)');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_hash');
        });
    }
}
