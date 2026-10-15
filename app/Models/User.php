<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'roles',
        'current_world_id',
        'login_token_hash',
        'login_token_expires_at',
        'failed_login_attempts',
        'last_failed_login_at',
        'locked_at',
        'password_reset_pending_at',
        'preferred_locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'current_world_id' => 'integer',
        'roles' => 'array',
        'login_token_expires_at' => 'datetime',
        'last_failed_login_at' => 'datetime',
        'locked_at' => 'datetime',
        'password_reset_pending_at' => 'datetime',
    ];

    public function worlds(): HasMany
    {
        return $this->hasMany(World::class);
    }

    public function currentWorld(): BelongsTo
    {
        return $this->belongsTo(World::class, 'current_world_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(UserLog::class);
    }

    public function isAdmin(): bool
    {
        return (string) ($this->role ?? '') === 'admin' || in_array('admin', $this->roles ?? [], true);
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles ?? [], true);
    }

    public function hasAnyRole(array $roles): bool
    {
        return count(array_intersect($roles, $this->roles ?? [])) > 0;
    }

    public function canAccessModule(string $module): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (in_array($module, config('module_access.public_modules', []), true)) {
            return true;
        }

        $moduleRoles = (array) config("module_access.modules.$module.roles", []);
        if ($moduleRoles === []) {
            return false;
        }

        return $this->hasAnyRole($moduleRoles);
    }
}
