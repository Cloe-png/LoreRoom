<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'character_id',
        'event_date',
        'title',
        'details',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}

