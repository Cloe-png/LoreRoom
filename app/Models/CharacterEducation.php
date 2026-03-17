<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterEducation extends Model
{
    use HasFactory;

    protected $fillable = [
        'character_id',
        'faction_id',
        'diploma_id',
        'field',
        'start_year',
        'end_year',
        'notes',
    ];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    public function faction()
    {
        return $this->belongsTo(Faction::class);
    }

    public function diploma()
    {
        return $this->belongsTo(Diploma::class);
    }
}
