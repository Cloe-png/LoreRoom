<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Species extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('selected_world', function ($query) {
            if (app()->runningInConsole() || !app()->bound('request')) {
                return;
            }

            $request = app('request');
            if (!$request->is('manage*')) {
                return;
            }

            $worldId = (int) $request->session()->get('selected_world_id', 0);
            if ($worldId > 0) {
                $query->where($query->qualifyColumn('world_id'), $worldId);
            }
        });
    }

    protected $fillable = [
        'world_id',
        'name',
        'characteristics',
        'abilities',
        'lifespan',
        'origin',
    ];

    public function world()
    {
        return $this->belongsTo(World::class);
    }

    public function characters()
    {
        return $this->belongsToMany(Character::class, 'character_species', 'species_id', 'character_id')
            ->withTimestamps()
            ->orderBy('name');
    }
}
