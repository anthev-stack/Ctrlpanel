@extends('layouts.app')

@section('content')
    @include('auth.partials.modern-styles')
    @php($website_settings = app(App\Settings\WebsiteSettings::class))
    @php($user_settings = app(App\Settings\UserSettings::class))

    <body class="auth-modern-body">
        <div class="auth-container">
            <div class="auth-intro">
                <h1>Launch your own game servers with confidence.</h1>
                <p>Create a GameControl account to access hourly billing, resource configurators, and automated provisioning.</p>
                <ul>
                    <li>Credits, referrals, and partner tooling baked in</li>
                    <li>Powerful Pterodactyl integration</li>
                    <li>White-glove support for your communities</li>
                </ul>
            </div>
            <div class="auth-card">
                <div class="auth-brand">
                    <strong>{{ config('app.name', 'GameControl') }}</strong>
                    <span>Command Centre</span>
                </div>
                <h2 class="auth-title">{{ __('Create your account') }}</h2>
                <p class="auth-subtitle">{{ __('Join the Command Centre to manage servers, billing, and more.') }}</p>

                @if (!$user_settings->creation_enabled)
                    <div class="auth-alert">
                        <strong>{{ __('Registrations are currently disabled.') }}</strong>
                        <p>{{ __('The system administrator has blocked the creation of new users.') }}</p>
                    </div>
                    <a class="auth-btn" href="{{ route('login') }}">{{ __('Back to login') }}</a>
                @else
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        @error('ip')
                            <p class="auth-error">{{ $message }}</p>
                        @enderror
                        @error('registered')
                            <p class="auth-error">{{ $message }}</p>
                        @enderror
                        @if ($errors->has('ptero_registration_error'))
                            @foreach ($errors->get('ptero_registration_error') as $err)
                                <p class="auth-error">{{ $err }}</p>
                            @endforeach
                        @endif

                        <div class="auth-field">
                            <label for="register-name">{{ __('Username') }}</label>
                            <input id="register-name" type="text"
                                   class="auth-input @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                   placeholder="{{ __('Choose a unique handle') }}">
                            @error('name')
                                <p class="auth-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="auth-field">
                            <label for="register-email">{{ __('Email') }}</label>
                            <input id="register-email" type="email"
                                   class="auth-input @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email"
                                   placeholder="{{ __('Where should we reach you?') }}">
                            @error('email')
                                <p class="auth-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="auth-field">
                            <label for="register-password">{{ __('Password') }}</label>
                            <input id="register-password" type="password"
                                   class="auth-input @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="new-password"
                                   placeholder="{{ __('Create a strong password') }}">
                            @error('password')
                                <p class="auth-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="auth-field">
                            <label for="register-password-confirm">{{ __('Retype password') }}</label>
                            <input id="register-password-confirm" type="password" class="auth-input"
                                   name="password_confirmation" required autocomplete="new-password"
                                   placeholder="{{ __('Repeat your password') }}">
                        </div>

                        @if (app(App\Settings\ReferralSettings::class)->enabled)
                            <div class="auth-field">
                                <label for="register-referral">{{ __('Referral code') }} ({{ __('optional') }})</label>
                                <input id="register-referral" type="text" class="auth-input"
                                       name="referral_code" value="{{ Request::get('ref') }}"
                                       placeholder="{{ __('Enter a partner code') }}">
                            </div>
                        @endif

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

                        @if ($website_settings->show_tos)
                            <div class="auth-field">
                                <label class="auth-checkbox" for="agreeTerms">
                                    <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                                    <span>
                                        {{ __('I agree to the') }}
                                        <a target="_blank" href="{{ route('terms', 'tos') }}">{{ __('Terms of Service') }}</a>
                                    </span>
                                </label>
                                @error('terms')
                                    <p class="auth-error">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="auth-actions">
                            <span></span>
                            <button type="submit" class="auth-btn">{{ __('Register') }}</button>
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>

                    <div class="auth-links">
                        <a href="{{ route('login') }}">{{ __('Already have an account? Sign in') }}</a>
                    </div>
                @endif
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
