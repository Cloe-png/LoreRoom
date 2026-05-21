<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('characters', 'is_adopted')) {
            Schema::table('characters', function (Blueprint $table) {
                $table->boolean('is_adopted')->default(false)->after('has_brother_sister');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('characters', 'is_adopted')) {
            Schema::table('characters', function (Blueprint $table) {
                $table->dropColumn('is_adopted');
            });
        }
    }
};
