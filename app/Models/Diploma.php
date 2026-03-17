<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diploma extends Model
{
    use HasFactory;

    protected $fillable = [
        'faction_id',
        'name',
        'level',
        'description',
    ];

    public function faction()
    {
        return $this->belongsTo(Faction::class);
    }
}
