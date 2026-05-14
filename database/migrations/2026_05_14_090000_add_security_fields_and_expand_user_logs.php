<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSecurityFieldsAndExpandUserLogs extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('failed_login_attempts')->default(0)->after('login_token_expires_at');
            $table->timestamp('last_failed_login_at')->nullable()->after('failed_login_attempts');
            $table->timestamp('locked_at')->nullable()->after('last_failed_login_at');
        });

        DB::statement("ALTER TABLE user_logs MODIFY action_user VARCHAR(40) NOT NULL");
    }

    public function down()
    {
        DB::table('user_logs')
            ->whereIn('action_user', [
                'failed_login',
                'account_locked',
                'trash_restore',
                'trash_force_delete',
                'trash_emptied',
            ])
            ->delete();

        DB::statement("ALTER TABLE user_logs MODIFY action_user ENUM('connexion', 'deconnexion', 'page_consulte') NOT NULL");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'failed_login_attempts',
                'last_failed_login_at',
                'locked_at',
            ]);
        });
    }
}
