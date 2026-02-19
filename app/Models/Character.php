<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saving(function (self $character) {
            $character->status = $character->resolveAutoStatus();
        });
    }

    protected $fillable = [
        'world_id',
        'name',
        'first_name',
        'last_name',
        'family_name',
        'aliases',
        'gender',
        'birth_date',
        'death_date',
        'status',
        'has_children',
        'has_brother_sister',
        'father_id',
        'mother_id',
        'spouse_id',
        'birth_place_id',
        'residence_place_id',
        'role',
        'short_term_goal',
        'long_term_goal',
        'secrets',
        'secrets_is_private',
        'has_power',
        'power_level',
        'power_description',
        'image_path',
        'preferred_color',
        'height',
        'silhouette',
        'hair_color',
        'eye_color',
        'hair_eyes',
        'posture',
        'marks',
        'clothing_style',
        'qualities',
        'flaws',
        'psychology_notes',
        'voice_tics',
        'voice_audio_path',
        'voice_youtube_url',
        'summary',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
        'has_children' => 'boolean',
        'has_brother_sister' => 'boolean',
        'has_power' => 'boolean',
        'secrets_is_private' => 'boolean',
    ];

    public function world()
    {
        return $this->belongsTo(World::class);
    }

    public function father()
    {
        return $this->belongsTo(self::class, 'father_id');
    }

    public function mother()
    {
        return $this->belongsTo(self::class, 'mother_id');
    }

    public function spouse()
    {
        return $this->belongsTo(self::class, 'spouse_id');
    }

    public function birthPlace()
    {
        return $this->belongsTo(Place::class, 'birth_place_id');
    }

    public function residencePlace()
    {
        return $this->belongsTo(Place::class, 'residence_place_id');
    }

    public function childrenFromFather()
    {
        return $this->hasMany(self::class, 'father_id');
    }

    public function childrenFromMother()
    {
        return $this->hasMany(self::class, 'mother_id');
    }

    public function outgoingRelations()
    {
        return $this->hasMany(CharacterRelation::class, 'from_character_id');
    }

    public function incomingRelations()
    {
        return $this->hasMany(CharacterRelation::class, 'to_character_id');
    }

    public function relatedCharacters()
    {
        return $this->belongsToMany(self::class, 'character_relations', 'from_character_id', 'to_character_id')
            ->withPivot([
                'id',
                'relation_type',
                'relation_category',
                'sibling_kind',
                'description',
                'intensity',
                'is_bidirectional',
            ])
            ->withTimestamps();
    }

    public function relatedToCharacters()
    {
        return $this->belongsToMany(self::class, 'character_relations', 'to_character_id', 'from_character_id')
            ->withPivot([
                'id',
                'relation_type',
                'relation_category',
                'sibling_kind',
                'description',
                'intensity',
                'is_bidirectional',
            ])
            ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(CharacterItem::class)->orderBy('id');
    }

    public function jobs()
    {
        return $this->hasMany(CharacterJob::class)->orderBy('start_year')->orderBy('id');
    }

    public function events()
    {
        return $this->hasMany(CharacterEvent::class)->orderBy('event_date')->orderBy('id');
    }

    public function galleryImages()
    {
        return $this->hasMany(CharacterGalleryImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function primaryGalleryImage()
    {
        return $this->hasOne(CharacterGalleryImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function chronicles()
    {
        return $this->belongsToMany(Chronicle::class, 'chronicle_character', 'character_id', 'chronicle_id')
            ->withTimestamps()
            ->orderBy('event_date')
            ->orderBy('id');
    }

    public function getDisplayNameAttribute()
    {
        $full = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));

        return $full !== '' ? $full : $this->name;
    }

    public function getCalculatedAgeAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }

        $endDate = $this->death_date ?: Carbon::today();

        return $this->birth_date->diffInYears($endDate);
    }

    private function resolveAutoStatus(): string
    {
        if (!$this->death_date) {
            return 'vivant';
        }

        $deathDate = $this->death_date;
        if (!$deathDate instanceof CarbonInterface) {
            try {
                $deathDate = Carbon::parse((string) $deathDate);
            } catch (\Throwable $e) {
                return 'vivant';
            }
        }

        return $deathDate->copy()->startOfDay()->lessThanOrEqualTo(Carbon::today())
            ? 'mort'
            : 'vivant';
    }
}
