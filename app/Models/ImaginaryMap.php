<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImaginaryMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'world_id',
        'title',
        'map_type',
        'image_url',
        'description',
        'status',
    ];

    public function world()
    {
        return $this->belongsTo(World::class);
    }
}

