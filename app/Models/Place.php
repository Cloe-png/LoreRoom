<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        static::addGlobalScope('selected_world', function ($query) {
            if (app()->runningInConsole() || !app()->bound('request')) {
                return;
            }

            $request = app('request');
            if (!$request->is('manage*')) {
                return;
            }

            $worldId = (int) $request->session()->get('selected_world_id', 0);
            if ($worldId > 0) {
                $query->where($query->qualifyColumn('world_id'), $worldId);
            }
        });
    }

    protected $fillable = [
        'world_id',
        'name',
        'type',
        'region',
        'summary',
        'image_path',
    ];

    public function world()
    {
        return $this->belongsTo(World::class);
    }

    public function eventChronicles()
    {
        return $this->hasMany(Chronicle::class, 'event_place_id');
    }

    public function birthCharacters()
    {
        return $this->hasMany(Character::class, 'birth_place_id');
    }

    public function residentCharacters()
    {
        return $this->hasMany(Character::class, 'residence_place_id');
    }

    public function galleryImages()
    {
        return $this->hasMany(PlaceGalleryImage::class)->orderBy('sort_order')->orderBy('id');
    }
}

