<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactionRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'faction_id',
        'related_faction_id',
        'relation_type',
        'description',
        'is_bidirectional',
    ];

    protected $casts = [
        'is_bidirectional' => 'boolean',
    ];

    public function faction()
    {
        return $this->belongsTo(Faction::class, 'faction_id');
    }

    public function relatedFaction()
    {
        return $this->belongsTo(Faction::class, 'related_faction_id');
    }
}
