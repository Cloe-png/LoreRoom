<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserAnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $this->requireAdmin();

        $days = max(7, min(90, (int) $request->query('days', 30)));
        $userSearch = trim((string) $request->query('user_search', ''));
        $since = Carbon::today()->subDays($days - 1)->startOfDay();

        $logsQuery = UserLog::query()->where('action_at', '>=', $since);
        $failedLogsQuery = UserLog::query()
            ->where('action_user', 'failed_login')
            ->where('action_at', '>=', $since);

        $totalActions = (clone $logsQuery)->count();
        $failedActions = (clone $failedLogsQuery)->count();

        $overview = [
            'totalUsers' => User::count(),
            'newUsersToday' => User::where('created_at', '>=', Carbon::today())->count(),
            'newUsersWeek' => User::where('created_at', '>=', Carbon::today()->startOfWeek())->count(),
            'activeUsers' => (clone $logsQuery)->distinct('user_id')->count('user_id'),
            'logins' => (clone $logsQuery)->where('action_user', 'connexion')->count(),
            'totalActions' => $totalActions,
            'failedActions' => $failedActions,
            'errorRate' => $totalActions > 0 ? round(($failedActions / $totalActions) * 100, 1) : 0,
            'averageTimeSpent' => $this->estimateAverageTimeSpent($since),
        ];

        $topPages = UserLog::query()
            ->select([
                'route_name',
                DB::raw('COUNT(*) as consultations_count'),
            ])
            ->where('action_user', 'page_consulte')
            ->where('action_at', '>=', $since)
            ->groupBy('route_name')
            ->orderByDesc('consultations_count')
            ->orderBy('route_name')
            ->limit(12)
            ->get();

        $recentActions = UserLog::query()
            ->with('user:id,name,email')
            ->where('action_at', '>=', $since)
            ->when($userSearch !== '', function ($query) use ($userSearch) {
                $query->whereHas('user', function ($userQuery) use ($userSearch) {
                    $userQuery->where('name', 'like', '%' . $userSearch . '%');

                    if (filter_var($userSearch, FILTER_VALIDATE_EMAIL) && User::supportsEmailHash()) {
                        $userQuery->orWhere('email_hash', User::emailHash($userSearch));
                    }
                });
            })
            ->latest('action_at')
            ->limit($userSearch !== '' ? 25 : 10)
            ->get();

        $suspiciousActivities = UserLog::query()
            ->select([
                'user_id',
                DB::raw('COUNT(*) as failed_attempts'),
                DB::raw('MAX(action_at) as last_attempt_at'),
            ])
            ->with('user:id,name,email')
            ->where('action_user', 'failed_login')
            ->where('action_at', '>=', $since)
            ->groupBy('user_id')
            ->having('failed_attempts', '>=', 3)
            ->orderByDesc('failed_attempts')
            ->orderByDesc('last_attempt_at')
            ->limit(10)
            ->get();

        $lockedUsers = User::query()
            ->whereNotNull('locked_at')
            ->orderByDesc('locked_at')
            ->limit(10)
            ->get(['id', 'name', 'email', 'locked_at', 'failed_login_attempts']);

        if ($lockedUsers->isNotEmpty()) {
            $suspiciousActivities = $suspiciousActivities->reject(
                fn ($entry) => $lockedUsers->contains('id', $entry->user_id)
            )->values();
        }

        return view('manage.analytics.index', [
            'days' => $days,
            'userSearch' => $userSearch,
            'since' => $since,
            'overview' => $overview,
            'topPages' => $topPages,
            'recentActions' => $recentActions,
            'suspiciousActivities' => $suspiciousActivities,
            'lockedUsers' => $lockedUsers,
        ]);
    }

    private function estimateAverageTimeSpent(Carbon $since): string
    {
        $logs = UserLog::query()
            ->select(['user_id', 'action_at'])
            ->whereNotNull('user_id')
            ->where('action_at', '>=', $since)
            ->orderBy('user_id')
            ->orderBy('action_at')
            ->get()
            ->groupBy('user_id');

        $durations = $logs
            ->map(fn (Collection $userLogs) => $this->estimateUserTimeSpentInMinutes($userLogs))
            ->filter(fn (?int $minutes) => $minutes !== null);

        if ($durations->isEmpty()) {
            return '0 min';
        }

        $averageMinutes = (int) round($durations->avg());

        if ($averageMinutes >= 60) {
            $hours = intdiv($averageMinutes, 60);
            $minutes = $averageMinutes % 60;

            return $minutes > 0 ? sprintf('%dh %02d', $hours, $minutes) : sprintf('%dh', $hours);
        }

        return sprintf('%d min', $averageMinutes);
    }

    private function estimateUserTimeSpentInMinutes(Collection $userLogs): ?int
    {
        if ($userLogs->count() < 2) {
            return null;
        }

        $sessionStart = null;
        $previousAt = null;
        $minutes = 0;

        foreach ($userLogs as $log) {
            $currentAt = Carbon::parse($log->action_at);

            if (!$sessionStart) {
                $sessionStart = $currentAt;
                $previousAt = $currentAt;
                continue;
            }

            if ($currentAt->diffInMinutes($previousAt) > 30) {
                $minutes += $sessionStart->diffInMinutes($previousAt);
                $sessionStart = $currentAt;
            }

            $previousAt = $currentAt;
        }

        if ($sessionStart && $previousAt) {
            $minutes += $sessionStart->diffInMinutes($previousAt);
        }

        return $minutes;
    }
}
