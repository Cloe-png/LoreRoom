<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactionMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'faction_id',
        'character_id',
        'role',
        'grade',
        'joined_at',
        'status',
    ];

    protected $casts = [
        'joined_at' => 'date',
    ];

    public function faction()
    {
        return $this->belongsTo(Faction::class);
    }

    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}
