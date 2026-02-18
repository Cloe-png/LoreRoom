<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class World extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'geography_type',
        'slug',
        'map_path',
        'summary',
        'status',
    ];

    public function characters()
    {
        return $this->hasMany(Character::class);
    }

    public function places()
    {
        return $this->hasMany(Place::class);
    }

    public function chronicles()
    {
        return $this->hasMany(Chronicle::class);
    }

    public function maps()
    {
        return $this->hasMany(ImaginaryMap::class);
    }
}
