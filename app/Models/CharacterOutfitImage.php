<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterOutfitImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'character_id',
        'mode',
        'image_path',
        'caption',
        'sort_order',
    ];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}
