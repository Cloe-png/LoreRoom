<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chronicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'world_id',
        'title',
        'event_date',
        'summary',
        'content',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function world()
    {
        return $this->belongsTo(World::class);
    }
}

