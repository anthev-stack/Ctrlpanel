@extends('layouts.app')

@section('content')
    @include('auth.partials.modern-styles')
    @php($website_settings = app(App\Settings\WebsiteSettings::class))

    <body class="auth-modern-body">
        <div class="auth-container">
            <div class="auth-intro">
                <h1>Dial in your next server in minutes.</h1>
                <p>GameControl Command Centre keeps your billing, provisioning, and community tools in one sleek place.</p>
                <ul>
                    <li>Instant provisioning & monthly billing</li>
                    <li>Live resource scaling for RAM and slots</li>
                    <li>Secure by design with MFA and recaptcha support</li>
                </ul>
            </div>
            <div class="auth-card">
                <div class="auth-brand">
                    <strong>{{ config('app.name', 'Gamecontrol') }}</strong>
                    <span>Command Centre</span>
                </div>
                <h2 class="auth-title">{{ __('Welcome back') }}</h2>
                <p class="auth-subtitle">{{ __('Sign in to continue to your dashboard.') }}</p>

                @if (session('message'))
                    <div class="auth-alert">{{ session('message') }}</div>
                @endif

                <form action="{{ route('login') }}" method="post">
                    @csrf
                    @if (Session::has('error'))
                        <p class="auth-error">{{ Session::get('error') }}</p>
                    @endif

                    <div class="auth-field">
                        <label for="login-identity">{{ __('Email or Username') }}</label>
                        <input id="login-identity" type="text" name="email"
                               class="auth-input @error('email') is-invalid @enderror @error('name') is-invalid @enderror"
                               placeholder="{{ __('Enter your credentials') }}" value="{{ old('email') }}">
                        @if ($errors->get('email') || $errors->get('name'))
                            <p class="auth-error">
                                {{ $errors->first('email') ? $errors->first('email') : $errors->first('name') }}
                            </p>
                        @endif
                    </div>

                    <div class="auth-field">
                        <label for="login-password">{{ __('Password') }}</label>
                        <input id="login-password" type="password" name="password"
                               class="auth-input @error('password') is-invalid @enderror"
                               placeholder="{{ __('Your secret phrase') }}">
                        @error('password')
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
                        <label class="auth-checkbox" for="remember">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>{{ __('Remember me') }}</span>
                        </label>
                        <button type="submit" class="auth-btn">{{ __('Sign In') }}</button>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>

                <div class="auth-links">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                    @endif
                    <a href="{{ route('register') }}">{{ __('Need an account? Register') }}</a>
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
