<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chapter extends Model
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
        'chapter_date',
        'title',
        'summary',
        'description',
        'character_feelings',
    ];

    protected $casts = [
        'chapter_date' => 'date',
        'character_feelings' => 'array',
    ];

    public function world()
    {
        return $this->belongsTo(World::class);
    }

    public function characters()
    {
        return $this->belongsToMany(Character::class, 'chapter_character', 'chapter_id', 'character_id')
            ->select('characters.*')
            ->withTimestamps()
            ->orderBy('name');
    }

    public function places()
    {
        return $this->belongsToMany(Place::class, 'chapter_place', 'chapter_id', 'place_id')
            ->select('places.*')
            ->withTimestamps()
            ->orderBy('name');
    }

    public function chronicles()
    {
        return $this->belongsToMany(Chronicle::class, 'chapter_chronicle', 'chapter_id', 'chronicle_id')
            ->select('chronicles.*')
            ->withTimestamps()
            ->orderByRaw('event_date IS NULL, event_date ASC')
            ->orderBy('title');
    }
}
