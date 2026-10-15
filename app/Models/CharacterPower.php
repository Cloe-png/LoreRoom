<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterPower extends Model
{
    use HasFactory;

    protected $fillable = [
        'character_id',
        'name',
        'description',
        'sort_order',
    ];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}
