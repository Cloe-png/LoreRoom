<?php

namespace App\Http\Controllers;

class SuggestionController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $activeWorld = $user ? $user->currentWorld : null;

        return view('manage.suggestions.index', [
            'formspreeEndpoint' => (string) config('services.formspree.endpoint', ''),
            'defaultName' => $user ? $user->name : '',
            'defaultEmail' => $user ? $user->email : '',
            'activeWorld' => $activeWorld,
        ]);
    }
}
