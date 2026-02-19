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
        'summary',
        'content',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
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
}

