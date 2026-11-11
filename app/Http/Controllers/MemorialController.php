<?php

namespace App\Http\Controllers;

use App\Models\MemorialOffering;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemorialController extends Controller
{
    private function today(): Carbon { return Carbon::now(config('app.timezone'))->startOfDay(); }

    private function isMemorialDay(): bool
    {
        $today = $this->today();
        return $today->month === (int) config('memorial.month') && $today->day === (int) config('memorial.day');
    }

    public function show(Request $request)
    {
        $user = $request->user();
        if (config('memorial.restrict_page_to_date') && !$this->isMemorialDay() && !$user->isChef()) abort(404);
        $today = $this->today()->toDateString();
        if ($user->memorial_last_visit !== $today) {
            $user->newQuery()->whereKey($user->id)->where(function ($q) use ($today) { $q->whereNull('memorial_last_visit')->orWhere('memorial_last_visit', '<', $today); })
                ->increment('memorial_visits');
            $user->forceFill(['memorial_last_visit' => $today])->save();
            $user->refresh();
        }
        return view('memorial.show', $this->pageData($user));
    }

    private function pageData($user): array
    {
        $today = $this->today()->toDateString();
        return [
            'offeredToday' => MemorialOffering::where('user_id', $user->id)->whereDate('offered_on', $today)->exists(),
            'isChef' => $user->isChef(),
            'visitCount' => (int) $user->memorial_visits,
            'stats' => $user->isChef() ? $this->aggregates() : null,
        ];
    }

    public function offer(Request $request)
    {
        if (config('memorial.restrict_page_to_date') && !$this->isMemorialDay() && !$request->user()->isChef()) abort(404);
        $today = $this->today()->toDateString();
        $inserted = DB::table('memorial_offerings')->insertOrIgnore([
            'user_id' => $request->user()->id, 'offered_on' => $today,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $message = $inserted === 1 ? 'Fleur offerte aujourd’hui.' : 'Fleur déjà offerte aujourd’hui.';
        if ($request->expectsJson()) return response()->json(['message' => $message, 'offered' => true], 200);
        return response()->view('memorial.show', $this->pageData($request->user()) + ['offerMessage' => $message], 200);
    }

    public function stats(Request $request)
    {
        abort_unless($request->user()->isChef(), 404);
        return response()->json($this->aggregates());
    }

    private function aggregates(): array
    {
        $today = $this->today()->toDateString();
        $start = $this->today()->copy()->subDays(6)->toDateString();
        $daily = MemorialOffering::query()->select('offered_on', DB::raw('COUNT(*) as total'))
            ->whereBetween('offered_on', [$start, $today])->groupBy('offered_on')->pluck('total', 'offered_on');
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $this->today()->copy()->subDays($i)->toDateString();
            $days[] = ['date' => $date, 'count' => (int) ($daily[$date] ?? 0)];
        }
        return [
            'people' => (int) MemorialOffering::distinct('user_id')->count('user_id'),
            'total' => (int) MemorialOffering::count(),
            'today' => (int) MemorialOffering::whereDate('offered_on', $today)->count(),
            'last_seven_days' => $days,
        ];
    }
}
