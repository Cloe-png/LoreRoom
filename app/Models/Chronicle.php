<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chronicle extends Model
{
    use HasFactory;

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

