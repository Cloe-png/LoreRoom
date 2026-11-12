@php
    $birthdayPeople = $birthdays->values();
    $birthdayLead = $birthdayPeople->first();
    $birthdayIsSober = $birthdayPeople->contains(fn ($character) => (bool) $character->birthday_is_dead);
    $birthdayDeceased = $birthdayPeople->filter(fn ($character) => (bool) $character->birthday_is_dead)->values();
    $birthdayParts = $birthdayPeople->map(fn ($character) => $character->display_name . ' (' . $character->birthday_age . ' ' . ($character->birthday_age === 1 ? 'an' : 'ans') . ')')->all();
    if (count($birthdayParts) > 1) {
        $birthdayNames = implode(', ', array_slice($birthdayParts, 0, -1)) . ' et ' . end($birthdayParts);
    } else {
        $birthdayNames = $birthdayParts[0];
    }
    $birthdayHeading = $birthdayPeople->count() === 1 && $birthdayLead->birthday_is_dead
        ? "Aujourd'hui, {$birthdayLead->display_name} aurait eu {$birthdayLead->birthday_age} ans."
        : "C'est la fête : {$birthdayNames}.";
    $birthdayDate = $birthdayLead->birth_date->locale(app()->getLocale())->translatedFormat('j F Y');
    $birthdayWorldName = $birthdayLead->world->name;
    $birthdayWorldPreposition = preg_match('/^[aeiouyàâäéèêëîïôöùûü]/iu', ltrim($birthdayWorldName)) ? 'd’' : 'de ';
    $birthdayTodayKey = 'fete-fermee-' . $birthdayToday->format('Y-m-d');
@endphp

<section class="birthday-banner {{ $birthdayIsSober ? 'is-sober' : '' }}" aria-labelledby="birthday-banner-title" data-birthday-banner data-today-key="{{ $birthdayTodayKey }}">
    @unless($birthdayIsSober)
        <div class="birthday-garland" data-birthday-garland aria-hidden="true"></div>
        <div class="birthday-confetti" data-birthday-confetti aria-hidden="true"></div>
    @endunless
    <div class="birthday-content">
        <div class="birthday-avatars" aria-hidden="true">
            @foreach($birthdayPeople->take(4) as $character)
                <span class="birthday-avatar">
                    @if($character->image_path)
                        <img src="{{ route('media.show', ['path' => $character->image_path]) }}" alt="">
                    @else
                        <span>{{ collect(preg_split('/\s+/', trim($character->display_name)))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('') ?: '?' }}</span>
                    @endif
                    @if($loop->first && !$birthdayIsSober)
                        <svg class="birthday-hat" viewBox="0 0 40 48" focusable="false" aria-hidden="true">
                            <path d="M20 4 5 39h30L20 4Z" fill="var(--ornament)" stroke="var(--heading)" stroke-width="1.5"/>
                            <path d="m13 21 14 4M9 31l21 4" stroke="var(--party)" stroke-width="3"/>
                            <circle cx="20" cy="4" r="4" fill="var(--party)" stroke="var(--heading)" stroke-width="1.2"/>
                        </svg>
                    @endif
                </span>
            @endforeach
            @if($birthdayPeople->count() > 4)
                <span class="birthday-avatar birthday-avatar-more">+{{ $birthdayPeople->count() - 4 }}</span>
            @endif
        </div>
        <div class="birthday-message">
            <div class="birthday-label">{{ $birthdayIsSober ? 'Une pensée' : 'Jour de fête' }}</div>
            <h2 id="birthday-banner-title">{{ $birthdayHeading }}</h2>
            @if($birthdayIsSober)
                <p class="birthday-subtitle">Une bougie pour {{ $birthdayDeceased->map(fn ($character) => $character->first_name ?: $character->display_name)->join(' et ') }}</p>
            @else
                <p class="birthday-subtitle">Né(e) le {{ $birthdayDate }} · monde {{ $birthdayWorldPreposition }}{{ $birthdayWorldName }}</p>
            @endif
            <div class="birthday-actions">
                <a class="birthday-primary" href="{{ route('manage.characters.show', $birthdayLead) }}">Ouvrir la fiche</a>
                @unless($birthdayIsSober)
                    <button class="birthday-link" type="button" data-birthday-replay>Rejouer la fête</button>
                @endunless
                <button class="birthday-link" type="button" data-birthday-close>Fermer pour aujourd'hui</button>
            </div>
        </div>
    </div>
</section>
