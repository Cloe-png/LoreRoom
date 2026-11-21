@php
    $tourUser = auth()->user();
    $tourHasWorlds = $tourUser && $tourUser->worlds()->exists();
    $tourHasActiveWorld = $tourHasWorlds && !empty($activeWorld);
    $tourSteps = [
        ['key' => 'welcome', 'target' => null, 'title' => __('tour.welcome_title'), 'text' => __('tour.welcome')],
        ['key' => 'home', 'target' => 'home', 'title' => __('tour.steps.home.title'), 'text' => __('tour.steps.home.text')],
        ['key' => 'worlds', 'target' => 'worlds', 'title' => __('tour.steps.worlds.title'), 'text' => __('tour.steps.worlds.text')],
        ['key' => 'characters', 'target' => 'characters', 'title' => __('tour.steps.characters.title'), 'text' => __('tour.steps.characters.text')],
        ['key' => 'places', 'target' => 'places', 'title' => __('tour.steps.places.title'), 'text' => __('tour.steps.places.text')],
    ];

    if ($tourUser && $tourUser->canAccessModule('chapters')) {
        $tourSteps[] = ['key' => 'chapters', 'target' => 'chapters', 'title' => __('tour.steps.chapters.title'), 'text' => __('tour.steps.chapters.text')];
    }
    if ($tourUser && $tourUser->canAccessModule('lore')) {
        $tourSteps[] = ['key' => 'lore', 'target' => 'lore', 'title' => __('tour.steps.lore.title'), 'text' => __('tour.steps.lore.text')];
    }
    if ($tourUser && $tourUser->canAccessModule('jobs')) {
        $tourSteps[] = ['key' => 'jobs', 'target' => 'jobs', 'title' => __('tour.steps.jobs.title'), 'text' => __('tour.steps.jobs.text')];
    }
    if ($tourUser && $tourUser->canAccessModule('factions')) {
        $tourSteps[] = ['key' => 'factions', 'target' => 'factions', 'title' => __('tour.steps.factions.title'), 'text' => __('tour.steps.factions.text')];
    }
    if ($tourUser && $tourUser->canAccessModule('lore')) {
        $tourSteps[] = ['key' => 'species', 'target' => 'species', 'title' => __('tour.steps.species.title'), 'text' => __('tour.steps.species.text')];
    }

    $tourSteps[] = ['key' => 'chronology', 'target' => 'chronology', 'title' => __('tour.steps.chronology.title'), 'text' => __('tour.steps.chronology.text')];
    $tourSteps[] = ['key' => 'relations', 'target' => 'relations', 'title' => __('tour.steps.relations.title'), 'text' => __('tour.steps.relations.text')];
    if ($tourUser && $tourUser->canAccessModule('genealogy')) {
        $tourSteps[] = ['key' => 'genealogy', 'target' => 'genealogy', 'title' => __('tour.steps.genealogy.title'), 'text' => __('tour.steps.genealogy.text')];
    }
    if ($tourUser && $tourUser->canAccessModule('gallery')) {
        $tourSteps[] = ['key' => 'gallery', 'target' => 'gallery', 'title' => __('tour.steps.gallery.title'), 'text' => __('tour.steps.gallery.text')];
    }

    $tourSteps[] = ['key' => 'tools', 'target' => 'tools', 'title' => __('tour.steps.tools.title'), 'text' => __('tour.steps.tools.text')];
    if ($tourUser && $tourUser->isAdminOrChef()) {
        $tourSteps[] = ['key' => 'analytics', 'target' => 'analytics', 'title' => __('tour.steps.analytics.title'), 'text' => __('tour.steps.analytics.text')];
        $tourSteps[] = ['key' => 'accounts', 'target' => 'accounts', 'title' => __('tour.steps.accounts.title'), 'text' => __('tour.steps.accounts.text')];
    }
    $tourSteps[] = ['key' => 'review', 'target' => 'review', 'title' => __('tour.steps.review.title'), 'text' => __('tour.steps.review.text')];

    if (!$tourHasWorlds) {
        $tourFinishTitle = __('tour.finish_create_title');
        $tourFinishText = __('tour.finish_create');
        $tourFinishAction = 'create-world';
        $tourFinishLabel = __('tour.actions.create_world');
        $tourFinishSkipLabel = __('tour.actions.later');
    } elseif (!$tourHasActiveWorld) {
        $tourFinishTitle = __('tour.finish_choose_title');
        $tourFinishText = __('tour.finish_choose');
        $tourFinishAction = 'choose-world';
        $tourFinishLabel = __('tour.actions.choose_world');
        $tourFinishSkipLabel = null;
    } else {
        $tourFinishTitle = __('tour.finish_title');
        $tourFinishText = __('tour.finish_active');
        $tourFinishAction = 'complete';
        $tourFinishLabel = __('tour.actions.finish');
        $tourFinishSkipLabel = null;
    }
    $tourSteps[] = [
        'key' => 'finish',
        'target' => null,
        'title' => $tourFinishTitle,
        'text' => $tourFinishText,
        'action' => $tourFinishAction,
        'actionLabel' => $tourFinishLabel,
        'skipLabel' => $tourFinishSkipLabel,
    ];
@endphp

<div id="first-visit-tour"
     data-auto-start="{{ $autoStart ? 'true' : 'false' }}"
     data-completed="{{ $tourUser && $tourUser->tour_completed_at ? 'true' : 'false' }}"
     data-complete-url="{{ route('manage.tour.complete') }}"
     data-restart-url="{{ route('manage.tour.restart') }}"
     data-csrf="{{ csrf_token() }}"
     data-label-counter="{{ __('tour.actions.counter') }}"
     data-label-next="{{ __('tour.actions.next') }}"
     data-label-finish="{{ __('tour.actions.finish') }}"
     data-label-skip="{{ __('tour.actions.skip') }}"
     hidden>
    <script type="application/json" data-tour-steps>@json($tourSteps, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)</script>
    <div class="tour-frame" aria-hidden="true"></div>
    <section class="tour-card" role="dialog" aria-modal="true" aria-labelledby="tour-step-title" aria-describedby="tour-announcer" tabindex="-1">
        <div class="tour-card-inner">
            <p class="tour-step-title" id="tour-step-title"></p>
            <div class="tour-copy">
                <p class="tour-copy-reserve"></p>
                <p class="tour-copy-typed" aria-hidden="true"></p>
            </div>
            <span class="tour-sr-only" id="tour-announcer" aria-live="polite" aria-atomic="true"></span>
            <footer class="tour-footer">
                <div class="tour-footer-meta">
                    <span class="tour-counter" data-tour-counter></span>
                    <button class="tour-text-link" type="button" data-tour-skip>{{ __('tour.actions.skip') }}</button>
                </div>
                <div class="tour-footer-controls">
                    <button class="tour-button tour-button-secondary" type="button" data-tour-previous>{{ __('tour.actions.previous') }}</button>
                    <button class="tour-button" type="button" data-tour-next>{{ __('tour.actions.next') }}</button>
                    <button class="tour-text-link tour-never" type="button" data-tour-never>{{ __('tour.actions.never') }}</button>
                </div>
            </footer>
        </div>
    </section>
    <x-tour-mascot />
</div>
