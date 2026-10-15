<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'parent_id', 'short_description', 'description', 'atmosphere', 'climate', 'population', 'alt_names',
        'government', 'religion', 'languages', 'economy', 'dangers', 'founded_at', 'history', 'status', 'private_notes', 'visibility',
    ];

    protected $casts = ['atmosphere' => 'array', 'alt_names' => 'array', 'languages' => 'array', 'population' => 'integer'];
    protected $hidden = ['private_notes'];

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

    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function children() { return $this->hasMany(self::class, 'parent_id')->orderBy('name'); }
    public function connectedPlaces(): BelongsToMany { return $this->belongsToMany(self::class, 'place_place', 'place_id', 'connected_place_id')->withPivot('travel_note')->withTimestamps(); }
    public function connectedFromPlaces(): BelongsToMany { return $this->belongsToMany(self::class, 'place_place', 'connected_place_id', 'place_id')->withPivot('travel_note')->withTimestamps(); }
    public function leaders(): BelongsToMany { return $this->belongsToMany(Character::class, 'place_leaders')->withPivot('title')->withTimestamps()->orderBy('name'); }
    public function factions(): BelongsToMany { return $this->belongsToMany(Faction::class, 'place_faction')->withTimestamps()->orderBy('name'); }
    public function species(): BelongsToMany { return $this->belongsToMany(Species::class, 'place_species')->withTimestamps()->orderBy('name'); }
    public function characters(): BelongsToMany { return $this->belongsToMany(Character::class, 'place_character')->withTimestamps()->orderBy('name'); }
    public function chronicles(): BelongsToMany { return $this->belongsToMany(Chronicle::class, 'chronicle_place')->withTimestamps()->orderBy('event_date'); }
    public function chapters(): BelongsToMany { return $this->belongsToMany(Chapter::class, 'chapter_place')->withTimestamps()->orderBy('chapter_date'); }
    public function pointsOfInterest() { return $this->hasMany(PlacePoint::class)->orderBy('position')->orderBy('id'); }
}

