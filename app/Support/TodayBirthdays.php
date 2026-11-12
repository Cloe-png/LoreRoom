<?php

namespace App\Support;

use App\Models\Character;
use App\Models\User;
use App\Models\World;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TodayBirthdays
{
    public function forWorld(?World $world, ?User $user, Carbon $today): Collection
    {
        if (!$world || !$user || (int) $world->user_id !== (int) $user->id) {
            return collect();
        }

        return Character::with('world')
            ->where('world_id', $world->id)
            ->whereHas('world', fn ($query) => $query->where('user_id', $user->id))
            ->whereMonth('birth_date', $today->month)
            ->whereNotNull('birth_date')
            ->orderBy('name')
            ->get()
            ->filter(function (Character $character) use ($today) {
                $birth = $character->birth_date;
                if (!$birth) {
                    return false;
                }

                return $birth->day === $today->day
                    || ($today->month === 2 && $today->day === 28 && !$today->isLeapYear() && $birth->month === 2 && $birth->day === 29);
            })
            ->map(function (Character $character) use ($today) {
                $character->setAttribute('birthday_age', $today->year - $character->birth_date->year);
                $character->setAttribute('birthday_is_dead', $character->status === 'mort'
                    || ($character->death_date && $character->death_date->lessThanOrEqualTo($today->copy()->endOfDay())));

                return $character;
            })
            ->values();
    }
}
