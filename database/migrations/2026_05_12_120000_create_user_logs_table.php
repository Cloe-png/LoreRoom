<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLogsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('user_logs')) {
            return;
        }

        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('route_name', 100)->nullable();
            $table->string('route_path', 255)->nullable();
            $table->enum('action_user', ['connexion', 'deconnexion', 'page_consulte']);
            $table->dateTime('action_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_logs');
    }
}
