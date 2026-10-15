<?php
namespace Database\Seeders;

use App\Models\Place;
use App\Models\World;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    public function run()
    {
        $world = World::query()->where('name','Aria')->first();
        if (!$world) return;
        $parent = null;
        foreach ([
            ['Aria','continent'], ['Royaume d’Aube','royaume'], ['Valdor','ville'], ['Vieille-Ville','region'], ['La Plume Rouge','taverne'],
        ] as [$name,$type]) {
            $place = Place::withTrashed()->firstOrNew(['world_id'=>$world->id,'name'=>$name]);
            $place->type = $type; $place->short_description = $place->short_description ?: 'Un lieu à découvrir dans le monde d’Aria.';
            $place->status = $place->status ?: 'inconnu'; $place->visibility = $place->visibility ?: 'brouillon'; $place->parent_id = $parent ? $parent->id : null;
            $place->save(); $parent = $place;
        }
    }
}
