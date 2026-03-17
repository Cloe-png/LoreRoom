<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faction extends Model
{
    use HasFactory;

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
        'summary',
        'motto',
        'founded_at',
        'status',
        'leader_id',
        'co_leader_id',
        'founder_id',
        'logo_path',
    ];

    protected $casts = [
        'founded_at' => 'date',
    ];

    public function world()
    {
        return $this->belongsTo(World::class);
    }

    public function leader()
    {
        return $this->belongsTo(Character::class, 'leader_id');
    }

    public function coLeader()
    {
        return $this->belongsTo(Character::class, 'co_leader_id');
    }

    public function founder()
    {
        return $this->belongsTo(Character::class, 'founder_id');
    }

    public function members()
    {
        return $this->belongsToMany(Character::class, 'faction_memberships', 'faction_id', 'character_id')
            ->withPivot(['role', 'grade', 'joined_at', 'status'])
            ->withTimestamps()
            ->orderBy('name');
    }

    public function outgoingRelations()
    {
        return $this->hasMany(FactionRelation::class, 'faction_id');
    }

    public function incomingRelations()
    {
        return $this->hasMany(FactionRelation::class, 'related_faction_id');
    }

    public function diplomas()
    {
        return $this->hasMany(Diploma::class)->orderBy('name');
    }
}
