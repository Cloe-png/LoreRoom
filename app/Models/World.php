<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class World extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
