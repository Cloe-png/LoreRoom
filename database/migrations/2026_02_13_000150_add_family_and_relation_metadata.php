<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->string('family_name', 120)->nullable()->after('last_name');
            $table->index('family_name');
        });

        Schema::table('character_relations', function (Blueprint $table) {
            $table->string('relation_category', 40)->nullable()->after('relation_type');
            $table->string('sibling_kind', 40)->nullable()->after('relation_category');
            $table->index('relation_category');
            $table->index('sibling_kind');
        });

        DB::table('characters')
            ->whereNull('family_name')
            ->whereNotNull('last_name')
            ->update(['family_name' => DB::raw('last_name')]);

        $rows = DB::table('character_relations')->select(['id', 'relation_type'])->get();
        foreach ($rows as $row) {
            $type = mb_strtolower(trim((string) $row->relation_type));
            $category = 'custom';
            $siblingKind = null;

            if (in_array($type, ['pere', 'mere', 'fils', 'fille', 'fils/fille', 'parent de', 'enfant de'], true)) {
                $category = 'family_lineage';
            } elseif (in_array($type, ['frere', 'soeur', 'frere/soeur'], true)) {
                $category = 'family_sibling';
                $siblingKind = 'full';
            } elseif (in_array($type, ['demi-frere', 'demi-soeur', 'demi-frere/soeur'], true)) {
                $category = 'family_sibling';
                $siblingKind = 'half';
            } elseif (in_array($type, ['jumeau', 'jumelle', 'jumeaux'], true)) {
                $category = 'family_sibling';
                $siblingKind = 'twin';
            } elseif (in_array($type, ['epoux', 'epouse', 'epoux/epouse', 'amour'], true)) {
                $category = 'family_couple';
            } elseif (in_array($type, ['ami', 'allie', 'ennemi', 'mentor', 'rival'], true)) {
                $category = 'social';
            }

            DB::table('character_relations')
                ->where('id', $row->id)
                ->update([
                    'relation_category' => $category,
                    'sibling_kind' => $siblingKind,
                ]);
        }
    }

    public function down()
    {
        Schema::table('character_relations', function (Blueprint $table) {
            $table->dropIndex(['relation_category']);
            $table->dropIndex(['sibling_kind']);
            $table->dropColumn(['relation_category', 'sibling_kind']);
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->dropIndex(['family_name']);
            $table->dropColumn('family_name');
        });
    }
};
