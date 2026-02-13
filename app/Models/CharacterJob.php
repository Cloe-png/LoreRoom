<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'character_id',
        'job_name',
        'start_year',
        'end_year',
        'notes',
    ];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}

