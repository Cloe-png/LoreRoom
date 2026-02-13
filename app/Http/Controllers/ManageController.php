<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Chronicle;
use App\Models\ImaginaryMap;
use App\Models\Place;
use App\Models\World;
use Carbon\Carbon;

class ManageController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        return view('manage.index', [
            'worldsCount' => World::count(),
            'charactersCount' => Character::count(),
            'placesCount' => Place::count(),
            'chroniclesCount' => Chronicle::count(),
            'mapsCount' => ImaginaryMap::count(),
            'today' => $today,
            'todayBirthdays' => Character::with('world')
                ->whereMonth('birth_date', $today->month)
                ->whereDay('birth_date', $today->day)
                ->orderBy('name')
                ->get(),
            'todayChronicles' => Chronicle::with('world')
                ->whereDate('event_date', $today)
                ->orderBy('title')
                ->get(),
            'upcomingChronicles' => Chronicle::with('world')
                ->whereDate('event_date', '>', $today)
                ->whereDate('event_date', '<=', $today->copy()->addDays(14))
                ->orderBy('event_date')
                ->orderBy('title')
                ->get(),
            'recentChronicles' => Chronicle::with('world')->latest()->take(5)->get(),
            'recentWorlds' => World::latest()->take(5)->get(),
            'recentMaps' => ImaginaryMap::with('world')->latest()->take(5)->get(),
        ]);
    }
}
