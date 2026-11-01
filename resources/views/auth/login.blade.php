@extends('layouts.auth')

@section('title', __('app.public.login_page_title'))

@section('content')
<x-auth-card :title="__('app.public.login_heading')">
    @if(session('success'))<div class="auth-message" role="status"><x-auth-error-message :message="session('success')" /></div>@endif
    @if($errors->any())<div class="auth-message error" role="alert"><x-auth-error-message :message="$errors->first()" /></div>@endif
    <form method="POST" action="{{ route('login.attempt') }}" data-auth-form>
        @csrf
        <div class="field">
            <label for="email">{{ __('app.public.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
            @error('email')<p class="field-error" id="email-error"><x-auth-error-message :message="$message" /></p>@enderror
        </div>
        <div class="field">
            <label for="password">{{ __('app.public.password') }}</label>
            <div class="password-wrap">
                <input id="password" type="password" name="password" required autocomplete="current-password" @if($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif>
                <button class="toggle-password" type="button" data-password-toggle aria-controls="password" aria-pressed="false">{{ __('app.public.show_password') }}</button>
            </div>
            @error('password')<p class="field-error" id="password-error"><x-auth-error-message :message="$message" /></p>@enderror
        </div>
        <label class="remember" for="remember"><input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}> {{ __('app.public.remember_me') }}</label>
        <div class="actions">
            <button class="btn" type="submit" data-submit-button data-loading-text="{{ __('app.public.signing_in') }}">{{ __('app.public.sign_in') }}</button>
            <div class="or">{{ __('app.public.or') }}</div>
            <a class="btn secondary" href="{{ route('register') }}">{{ __('app.public.create_account') }}</a>
        </div>
    </form>
</x-auth-card>
@endsection
