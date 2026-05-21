<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'route_name',
        'route_path',
        'action_user',
        'action_at',
    ];

    protected $casts = [
        'action_at' => 'datetime',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute(): string
    {
        switch ($this->action_user) {
            case 'connexion':
                return 'Connexion';
            case 'déconnexion':
                return 'Déconnexion';
            case 'page_consulte':
                return 'Consultation de page';
            case 'failed_login':
                return 'Echec de connexion';
            case 'account_locked':
                return 'Compte bloqué';
            case 'trash_restore':
                return 'Restauration corbeille';
            case 'trash_force_delete':
                return 'Suppression définitive';
            case 'trash_emptied':
                return 'Corbeille vidée';
            case 'admin_password_reset':
                return 'Réinitialisation admin du mot de passe';
            case 'password_changed':
                return 'Mot de passe modifié';
            default:
                return ucfirst(str_replace('_', ' ', (string) $this->action_user));
        }
    }

    public static function mostConsultedPages(int $limit = 15): Collection
    {
        return static::query()
            ->select([
                'route_name',
                'route_path',
                DB::raw('COUNT(*) as consultations_count'),
            ])
            ->where('action_user', 'page_consulte')
            ->groupBy('route_name', 'route_path')
            ->orderByDesc('consultations_count')
            ->orderBy('route_path')
            ->limit($limit)
            ->get();
    }

    public static function logAction(
        ?int $userId,
        string $action,
        string $routeName = null,
        string $routePath = null
    ): self {
        return static::create([
            'user_id' => $userId,
            'route_name' => $routeName,
            'route_path' => $routePath,
            'action_user' => $action,
            'action_at' => now(),
        ]);
    }
}
