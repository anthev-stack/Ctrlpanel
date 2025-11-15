@extends('layouts.app')

@section('content')
    @include('auth.partials.modern-styles')
    @php($website_settings = app(App\Settings\WebsiteSettings::class))

    <body class="auth-modern-body">
        <div class="auth-container">
            <div class="auth-intro">
                <h1>{{ __('Reset access with confidence.') }}</h1>
                <p>{{ __('We’ll send you a secure link to get back into the Command Centre.') }}</p>
                <ul>
                    <li>{{ __('Fast, secure password recovery flow') }}</li>
                    <li>{{ __('Modern encryption and recaptcha protection') }}</li>
                    <li>{{ __('Monthly billing with hourly-grade controls') }}</li>
                </ul>
            </div>
            <div class="auth-card">
                <div class="auth-brand">
                    <strong>{{ config('app.name', 'Gamecontrol') }}</strong>
                    <span>Command Centre</span>
                </div>
                <h2 class="auth-title">{{ __('Forgot your password?') }}</h2>
                <p class="auth-subtitle">{{ __('Enter your account email and we’ll send a reset link right away.') }}</p>

                @if (session('status'))
                    <div class="auth-alert">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="auth-field">
                        <label for="reset-email">{{ __('Email address') }}</label>
                        <input id="reset-email" type="email"
                               class="auth-input @error('email') is-invalid @enderror"
                               placeholder="{{ __('you@example.com') }}" name="email" value="{{ old('email') }}" required
                               autocomplete="email" autofocus>
                        @error('email')
                            <p class="auth-error">{{ $message }}</p>
                        @enderror
                    </div>

                    @php($recaptchaVersion = app(App\Settings\GeneralSettings::class)->recaptcha_version)
                    @if ($recaptchaVersion)
                        <div class="auth-field">
                            @switch($recaptchaVersion)
                                @case('v2')
                                    {!! htmlFormSnippet() !!}
                                    @break
                                @case('v3')
                                    {!! RecaptchaV3::field('recaptchathree') !!}
                                    @break
                                @case('turnstile')
                                    <x-turnstile-widget
                                        theme="dark"
                                        language="en-us"
                                        size="normal"
                                    />
                                    @error('cf-turnstile-response')
                                        <p class="auth-error">{{ $message }}</p>
                                    @enderror
                                    @break
                            @endswitch

                            @error('g-recaptcha-response')
                                <p class="auth-error">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="auth-actions">
                        <span></span>
                        <button type="submit" class="auth-btn">{{ __('Request new password') }}</button>
                    </div>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>

                <div class="auth-links">
                    <a href="{{ route('login') }}">{{ __('Back to login') }}</a>
                </div>
            </div>
        </div>

        <div class="auth-legal">
            @if ($website_settings->show_imprint)
                <a target="_blank" href="{{ route('terms', 'imprint') }}">{{ __('Imprint') }}</a> •
            @endif
            @if ($website_settings->show_privacy)
                <a target="_blank" href="{{ route('terms', 'privacy') }}">{{ __('Privacy') }}</a>
            @endif
            @if ($website_settings->show_tos)
                • <a target="_blank" href="{{ route('terms', 'tos') }}">{{ __('Terms of Service') }}</a>
            @endif
        </div>
    </body>
@endsection
