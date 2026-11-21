<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CharacterGalleryImage extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        static::deleting(function (self $image) {
            $image->mentions()->detach();
        });
    }

    protected $fillable = [
        'character_id',
        'world_id',
        'image_path',
        'display_path',
        'title',
        'caption',
        'description',
        'sort_order',
        'position',
    ];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    public function owner()
    {
        return $this->character();
    }

    public function mentions()
    {
        return $this->belongsToMany(Character::class, 'gallery_image_mentions', 'gallery_image_id', 'character_id')->withTimestamps();
    }
}

