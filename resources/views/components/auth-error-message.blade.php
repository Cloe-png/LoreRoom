@props(['message'])
@php
    $translations = [
        __('app.auth.invalid_credentials', [], 'en') => __('app.auth.invalid_credentials', [], 'fr'),
        __('app.auth.account_locked', [], 'en') => __('app.auth.account_locked', [], 'fr'),
        __('app.auth.logged_out', [], 'en') => __('app.auth.logged_out', [], 'fr'),
        __('app.auth.password_min', [], 'en') => __('app.auth.password_min', [], 'fr'),
        __('app.auth.password_regex', [], 'en') => __('app.auth.password_regex', [], 'fr'),
        __('app.auth.password_confirmed', [], 'en') => __('app.auth.password_confirmed', [], 'fr'),
    ];
    $localizedMessage = $translations[$message] ?? $message;
    $fields = ['name', 'email', 'locale', 'roles', 'password', 'password_confirmation'];

    if ($localizedMessage === $message) {
        foreach ($fields as $field) {
            $attribute = __('validation.attributes.' . $field, [], 'fr');
            if ($message === "The {$field} field is required.") {
                $localizedMessage = __('validation.required', ['attribute' => $attribute], 'fr');
                break;
            }
            if ($message === "The {$field} field must be a valid email address.") {
                $localizedMessage = __('validation.email', ['attribute' => $attribute], 'fr');
                break;
            }
            if ($message === "The {$field} has already been taken.") {
                $localizedMessage = __('validation.unique', ['attribute' => $attribute], 'fr');
                break;
            }
        }

        if ($localizedMessage === $message && $message === 'The password confirmation does not match.') {
            $localizedMessage = __('app.auth.password_confirmed', [], 'fr');
        }
    }
@endphp
{{ $localizedMessage }}
