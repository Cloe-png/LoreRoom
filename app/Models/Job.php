<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'world_id',
        'name',
        'description',
        'is_default',
    ];

    public function world()
    {
        return $this->belongsTo(World::class);
    }

    public function characterJobs()
    {
        return $this->hasMany(CharacterJob::class);
    }

    public function characters()
    {
        return $this->belongsToMany(Character::class, 'character_jobs', 'job_id', 'character_id')
            ->withTimestamps();
    }
}
