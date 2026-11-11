<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','chanceux','utilisateur','chef') NOT NULL DEFAULT 'utilisateur'");
        }
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->unsignedInteger('memorial_visits')->default(0);
            $table->date('memorial_last_visit')->nullable();
            $table->unsignedBigInteger('changelog_seen_id')->nullable();
        });
        DB::table('users')->whereNull('onboarding_completed_at')->update(['onboarding_completed_at' => DB::raw('COALESCE(created_at, CURRENT_TIMESTAMP)')]);

        Schema::create('writing_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('text', 200);
            $table->string('target_type', 20)->default('any');
            $table->string('section_hint')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('writing_prompt_rotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('world_id')->constrained()->cascadeOnDelete();
            $table->date('requested_on');
            $table->unsignedTinyInteger('alternative_count')->default(0);
            $table->foreignId('prompt_id')->nullable()->constrained('writing_prompts')->nullOnDelete();
            $table->string('target_type', 20)->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_name')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'world_id', 'requested_on']);
        });
        Schema::create('changelog_entries', function (Blueprint $table) {
            $table->id();
            $table->timestamp('published_at')->nullable();
            $table->string('title', 80);
            $table->text('body');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
        Schema::create('memorial_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('offered_on');
            $table->timestamps();
            $table->unique(['user_id', 'offered_on']);
            $table->index('offered_on');
        });
    }

    public function down()
    {
        if (DB::table('users')->where('role', 'chef')->exists()) {
            throw new RuntimeException('Impossible de retirer le rôle chef tant qu’un compte chef existe.');
        }
        Schema::dropIfExists('memorial_offerings');
        Schema::dropIfExists('changelog_entries');
        Schema::dropIfExists('writing_prompt_rotations');
        Schema::dropIfExists('writing_prompts');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['onboarding_completed_at', 'memorial_visits', 'memorial_last_visit', 'changelog_seen_id']);
        });
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','chanceux','utilisateur') NOT NULL DEFAULT 'utilisateur'");
        }
    }
};
