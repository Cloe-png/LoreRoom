<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chronicle extends Model
{
    use HasFactory, SoftDeletes;

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
        'title',
        'event_date',
        'end_date',
        'event_place_id',
        'event_location',
        'summary',
        'content',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'end_date' => 'date',
    ];

    public function world()
    {
        return $this->belongsTo(World::class);
    }

    public function linkedCharacters()
    {
        return $this->belongsToMany(Character::class, 'chronicle_character', 'chronicle_id', 'character_id')
            ->withTimestamps()
            ->orderBy('name');
    }

    public function eventPlace()
    {
        return $this->belongsTo(Place::class, 'event_place_id');
    }
}

