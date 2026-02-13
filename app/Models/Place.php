<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'world_id',
        'name',
        'region',
        'summary',
    ];

    public function world()
    {
        return $this->belongsTo(World::class);
    }
}

