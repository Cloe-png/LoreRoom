@extends('layouts.auth')

@section('title', __('app.public.register_page_title'))

@section('content')
<x-auth-card :title="__('app.public.register_heading')" :subtitle="__('app.public.register_intro')" :wide="true" :mascot="true">
    @if(session('success'))<div class="auth-message" role="status"><x-auth-error-message :message="session('success')" /></div>@endif
    @if($errors->any())<div class="auth-message error" role="alert"><x-auth-error-message :message="$errors->first()" /></div>@endif
    <form method="POST" action="{{ route('register.store') }}" data-auth-form>
        @csrf
        <div class="field">
            <label for="name">{{ __('app.public.full_name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="username" required @if($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif>
            @error('name')<p class="field-error" id="name-error"><x-auth-error-message :message="$message" /></p>@enderror
        </div>
        <div class="field">
            <label for="email">{{ __('app.public.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
            @error('email')<p class="field-error" id="email-error"><x-auth-error-message :message="$message" /></p>@enderror
        </div>
        <fieldset class="role-section">
            <legend>{{ __('app.public.roles_label') }}</legend>
            <p class="help">{{ __('app.public.roles_help') }}</p>
            <div class="role-grid" @if($errors->has('roles')) aria-describedby="roles-error" @endif>
                @php($selectedRoles = old('roles', []))
                @foreach($roleOptions as $roleKey => $roleInfo)
                    <label class="role-card"><input type="checkbox" name="roles[]" value="{{ $roleKey }}" {{ in_array($roleKey, $selectedRoles, true) ? 'checked' : '' }} @if($loop->first && $errors->has('roles')) aria-invalid="true" aria-describedby="roles-error" @endif><strong>{{ __($roleInfo['label']) }}</strong><span>{{ __($roleInfo['help']) }}</span></label>
                @endforeach
            </div>
            @error('roles')<p class="field-error" id="roles-error"><x-auth-error-message :message="$message" /></p>@enderror
        </fieldset>
        <div class="field">
            <label for="password">{{ __('app.public.password') }}</label>
            <div class="password-wrap">
                <input id="password" type="password" name="password" autocomplete="new-password" minlength="12" required @if($errors->has('password')) aria-invalid="true" aria-describedby="password-help password-error" @endif>
                <button class="toggle-password" type="button" data-password-toggle aria-controls="password" aria-pressed="false">{{ __('app.public.show_password') }}</button>
            </div>
            <p class="help" id="password-help">{{ __('app.public.password_help') }}</p>
            @error('password')<p class="field-error" id="password-error"><x-auth-error-message :message="$message" /></p>@enderror
        </div>
        <div class="field">
            <label for="password_confirmation">{{ __('app.public.confirmation') }}</label>
            <div class="password-wrap">
                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="12" required>
                <button class="toggle-password" type="button" data-password-toggle aria-controls="password_confirmation" aria-pressed="false">{{ __('app.public.show_password') }}</button>
            </div>
        </div>
        <div class="actions">
            <button class="btn" type="submit" data-submit-button data-loading-text="{{ __('app.public.creating_account') }}">{{ __('app.public.create_account_submit') }}</button>
            <div class="or">{{ __('app.public.or') }}</div>
            <a class="btn secondary" href="{{ route('login') }}">{{ __('app.public.already_registered') }}</a>
        </div>
    </form>
</x-auth-card>
@endsection
