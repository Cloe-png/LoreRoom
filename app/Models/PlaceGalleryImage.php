<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaceGalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
