<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('characters', 'traumas')) {
            Schema::table('characters', function (Blueprint $table) {
                $table->json('traumas')->nullable()->after('psychology_notes');
            });
        }

        if (Schema::hasColumn('characters', 'trauma_category')) {
            $characters = DB::table('characters')->select(['id', 'trauma_category'])->get();

            foreach ($characters as $character) {
                $trauma = trim((string) ($character->trauma_category ?? ''));
                DB::table('characters')
                    ->where('id', $character->id)
                    ->update([
                        'traumas' => $trauma !== ''
                            ? json_encode([$trauma], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                            : null,
                    ]);
            }

            Schema::table('characters', function (Blueprint $table) {
                $table->dropColumn('trauma_category');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasColumn('characters', 'trauma_category')) {
            Schema::table('characters', function (Blueprint $table) {
                $table->string('trauma_category', 120)->nullable()->after('psychology_notes');
            });
        }

        if (Schema::hasColumn('characters', 'traumas')) {
            $characters = DB::table('characters')->select(['id', 'traumas'])->get();

            foreach ($characters as $character) {
                $traumas = json_decode((string) ($character->traumas ?? 'null'), true);
                $first = is_array($traumas) ? trim((string) ($traumas[0] ?? '')) : '';

                DB::table('characters')
                    ->where('id', $character->id)
                    ->update([
                        'trauma_category' => $first !== '' ? $first : null,
                    ]);
            }

            Schema::table('characters', function (Blueprint $table) {
                $table->dropColumn('traumas');
            });
        }
    }
};
