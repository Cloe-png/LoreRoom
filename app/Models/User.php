<?php

namespace App\Models;

use App\Support\EmailPrivacy;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
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
        'email_hash',
        'password',
        'role',
        'current_world_id',
        'login_token_hash',
        'login_token_expires_at',
        'failed_login_attempts',
        'last_failed_login_at',
        'locked_at',
        'password_reset_pending_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_hash',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'current_world_id' => 'integer',
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

    public function setEmailAttribute($value): void
    {
        $normalized = EmailPrivacy::normalize((string) $value);
        $this->attributes['email'] = EmailPrivacy::encrypt($normalized);
        $this->attributes['email_hash'] = EmailPrivacy::hash($normalized);
    }

    public function getEmailAttribute($value): ?string
    {
        return EmailPrivacy::decrypt($value);
    }

    public static function normalizeEmail(?string $email): string
    {
        return EmailPrivacy::normalize($email);
    }

    public static function emailHash(?string $email): string
    {
        return EmailPrivacy::hash($email);
    }

    public static function findByEmail(?string $email): ?self
    {
        if (!static::supportsEmailHash()) {
            return static::query()
                ->where('email', static::normalizeEmail($email))
                ->first();
        }

        return static::query()
            ->where('email_hash', static::emailHash($email))
            ->first();
    }

    public static function supportsEmailHash(): bool
    {
        static $supportsEmailHash = null;

        if ($supportsEmailHash !== null) {
            return $supportsEmailHash;
        }

        try {
            $supportsEmailHash = Schema::hasColumn('users', 'email_hash');
        } catch (\Throwable $e) {
            $supportsEmailHash = false;
        }

        return $supportsEmailHash;
    }
}
