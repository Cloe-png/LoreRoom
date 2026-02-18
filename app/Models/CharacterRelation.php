<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_character_id',
        'to_character_id',
        'relation_type',
        'relation_category',
        'sibling_kind',
        'description',
        'intensity',
        'is_bidirectional',
    ];

    protected $casts = [
        'is_bidirectional' => 'boolean',
    ];

    public function fromCharacter()
    {
        return $this->belongsTo(Character::class, 'from_character_id');
    }

    public function toCharacter()
    {
        return $this->belongsTo(Character::class, 'to_character_id');
    }
}
