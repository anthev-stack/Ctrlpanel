<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
@php($website_settings = app(App\Settings\WebsiteSettings::class))
@php($general_settings = app(App\Settings\GeneralSettings::class))
@php($discord_settings = app(App\Settings\DiscordSettings::class))
@php($creditBalanceFormatted = $creditBalanceFormatted ?? Currency::formatForDisplay(Auth::user()->credits))
    @use('App\Constants\PermissionGroups')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="{{ $website_settings->seo_title }}" property="og:title">
    <meta content="{{ $website_settings->seo_description }}" property="og:description">
    <meta
            content='{{ \Illuminate\Support\Facades\Storage::disk('public')->exists('logo.png') ? asset('storage/logo.png') : asset('images/ctrlpanel_logo.png') }}'
            property="og:image">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon"
          href="{{ \Illuminate\Support\Facades\Storage::disk('public')->exists('favicon.ico') ? asset('storage/favicon.ico') : asset('favicon.ico') }}"
          type="image/x-icon">

    <script src="{{ asset('plugins/alpinejs/3.12.0_cdn.min.js') }}" defer></script>

    {{-- <link rel="stylesheet" href="{{asset('css/adminlte.min.css')}}"> --}}
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">

    {{-- summernote --}}
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">

    {{-- datetimepicker --}}
    <link rel="stylesheet"
          href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">

    {{-- select2 --}}
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">

    <link rel="preload" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}" as="style"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    </noscript>
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- tinymce -->
    <script src="{{ asset('plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <style>
        #userDropdown.dropdown-toggle::after {
            display: none !important;
        }
    </style>
    @vite('themes/default/sass/app.scss')
</head>

@php
    use App\Constants\PermissionGroups;
    $ticket_settings = app(App\Settings\TicketSettings::class);
    $ticket_enabled = $ticket_settings->enabled;
    $show_store = config('app.env') === 'local' || app(App\Settings\GeneralSettings::class)->store_enabled;
    $adminPermissionSets = array_merge(
        PermissionGroups::OVERVIEW_PERMISSIONS,
        PermissionGroups::TICKET_ADMIN_PERMISSIONS,
        PermissionGroups::TICKET_BLACKLIST_PERMISSIONS,
        PermissionGroups::ROLES_PERMISSIONS,
        PermissionGroups::SETTINGS_PERMISSIONS,
        PermissionGroups::API_PERMISSIONS,
        PermissionGroups::USERS_PERMISSIONS,
        PermissionGroups::SERVERS_PERMISSIONS,
        PermissionGroups::PRODUCTS_PERMISSIONS,
        PermissionGroups::STORE_PERMISSIONS,
        PermissionGroups::VOUCHERS_PERMISSIONS,
        PermissionGroups::PARTNERS_PERMISSIONS,
        PermissionGroups::COUPONS_PERMISSIONS,
        PermissionGroups::USEFUL_LINKS_PERMISSIONS,
        PermissionGroups::PAYMENTS_PERMISSIONS,
        PermissionGroups::LOGS_PERMISSIONS
    );
    $hasAdminSidebar = Auth::check() && Auth::user() ? Auth::user()->hasAnyPermission($adminPermissionSets) : false;
@endphp
<body class="sidebar-mini layout-fixed dark-mode {{ $hasAdminSidebar ? '' : 'sidebar-collapse no-admin-sidebar' }}" style="height: auto;">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header sticky-top gc-top-nav navbar navbar-expand">
        <div class="gc-top-left">
            @if($hasAdminSidebar)
                <button class="gc-menu-toggle" data-widget="pushmenu" type="button" aria-label="Toggle admin menu">
                    <i class="fas fa-bars"></i>
                </button>
            @endif
            <div class="gc-brand">
                <span class="gc-brand-title">{{ \Illuminate\Support\Str::upper(config('app.name', 'Gamecontrol')) }}</span>
                <small>{{ __('Billing Panel') }}</small>
            </div>
        </div>

        <div class="gc-top-links">
            <a class="gc-link {{ Request::routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('Dashboard') }}</a>
            <a class="gc-link {{ Request::routeIs('servers.*') ? 'active' : '' }}" href="{{ route('servers.index') }}">{{ __('Servers') }}</a>
            @if ($show_store)
                <a class="gc-link {{ (Request::routeIs('store.*') || Request::routeIs('checkout')) ? 'active' : '' }}" href="{{ route('store.index') }}">{{ __('Store') }}</a>
            @endif
            @if ($ticket_enabled && Auth::user()->canAny(PermissionGroups::TICKET_PERMISSIONS))
                <a class="gc-link {{ Request::routeIs('ticket.*') ? 'active' : '' }}" href="{{ route('ticket.index') }}">{{ __('Support Tickets') }}</a>
            @endif
        </div>

        <div class="gc-top-actions ml-auto navbar-nav">
            <div class="nav-item dropdown gc-credit-dropdown">
                <a class="nav-link" href="#" id="creditDropdown" role="button" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    <span>
                        <small><i class="mr-2 fas fa-coins"></i></small>{{ Currency::formatForDisplay(Auth::user()->credits) }}
                    </span>
                </a>
                <div class="shadow dropdown-menu dropdown-menu-right animated--grow-in"
                     aria-labelledby="creditDropdown">
                    @if ($show_store)
                        <a class="dropdown-item" href="{{ route('store.index') }}">
                            <i class="mr-2 text-gray-400 fas fa-coins fa-sm fa-fw"></i>
                            {{ __('Store') }}
                        </a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" data-toggle="modal" data-target="#redeemVoucherModal"
                       href="javascript:void(0)">
                        <i class="mr-2 text-gray-400 fas fa-money-check-alt fa-sm fa-fw"></i>
                        {{ __('Redeem code') }}
                    </a>
                </div>
            </div>

            <div class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle no-arrow" href="#" id="userDropdown" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-1 text-gray-600 d-lg-inline small">
                            {{ Auth::user()->name }}
                            <img width="28px" height="28px" class="ml-1 rounded-circle position-relative"
                                 src="{{ Auth::user()->getAvatar() }}">
                            @if (Auth::user()->unreadNotifications->count() != 0)
                                <span class="badge badge-warning navbar-badge position-absolute" style="top: 0px;">
                                    {{ Auth::user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </span>
                </a>
                <div class="shadow dropdown-menu dropdown-menu-right animated--grow-in"
                     aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                        <i class="mr-2 text-gray-400 fas fa-user fa-sm fa-fw"></i>
                        {{ __('Profile') }}
                    </a>
                    <a class="dropdown-item position-relative" href="{{ route('notifications.index') }}">
                        <i class="mr-2 text-gray-400 fas fa-bell fa-sm fa-fw"></i>
                        {{ __('Notifications') }}
                        @if (Auth::user()->unreadNotifications->count() != 0)
                            <span class="badge badge-warning navbar-badge position-absolute" style="top: 10px;">
                                    {{ Auth::user()->unreadNotifications->count() }}
                                </span>
                        @endif
                    </a>
                    <a class="dropdown-item" href="{{ route('preferences.index') }}">
                        <i class="mr-2 text-gray-400 fas fa-cog fa-sm fa-fw"></i>
                        {{ __('Preferences') }}
                    </a>
                    @if (session()->get('previousUser'))
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('users.logbackin') }}">
                            <i class="mr-2 text-gray-400 fas fa-sign-in-alt fa-sm fa-fw"></i>
                            {{ __('Log back in') }}
                        </a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item" href="#" data-toggle="modal"
                                data-target="#logoutModal">
                            <i class="mr-2 text-gray-400 fas fa-sign-out-alt fa-sm fa-fw"></i>
                            {{ __('Logout') }}
                        </button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <!-- /.navbar -->
    <!-- Main Sidebar Container -->
    @if($hasAdminSidebar)
    <aside class="main-sidebar sidebar-open sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="brand-link">
            <img width="48" height="48"
                 src="{{ \Illuminate\Support\Facades\Storage::disk('public')->exists('icon.png') ? asset('storage/icon.png') : asset('images/ctrlpanel_logo.png') }}"
                 alt="{{ config('app.name', 'Laravel') }} Logo" class="brand-image img-circle elevation-2"
                 style="opacity: .95">
            <span class="brand-text">
                <span>{{ \Illuminate\Support\Str::upper(config('app.name', 'Gamecontrol')) }}</span>
                <small>{{ __('Billing Panel') }}</small>
            </span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar" style="overflow-y: auto">

            <!-- Sidebar Menu -->
            <nav class="my-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                    @canany(array_merge(
                        PermissionGroups::TICKET_PERMISSIONS,
                        PermissionGroups::OVERVIEW_PERMISSIONS,
                        PermissionGroups::TICKET_ADMIN_PERMISSIONS,
                        PermissionGroups::TICKET_BLACKLIST_PERMISSIONS,
                        PermissionGroups::ROLES_PERMISSIONS,
                        PermissionGroups::SETTINGS_PERMISSIONS,
                        PermissionGroups::API_PERMISSIONS,
                        PermissionGroups::USERS_PERMISSIONS,
                        PermissionGroups::SERVERS_PERMISSIONS,
                        PermissionGroups::PRODUCTS_PERMISSIONS,
                        PermissionGroups::STORE_PERMISSIONS,
                        PermissionGroups::VOUCHERS_PERMISSIONS,
                        PermissionGroups::PARTNERS_PERMISSIONS,
                        PermissionGroups::COUPONS_PERMISSIONS,
                        PermissionGroups::USEFUL_LINKS_PERMISSIONS,
                        PermissionGroups::PAYMENTS_PERMISSIONS,
                        PermissionGroups::LOGS_PERMISSIONS
                    ))
                        <li class="nav-header">{{ __('Administration') }}</li>
                    @endcanany

                    @canany(PermissionGroups::OVERVIEW_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.overview.index') }}"
                               class="nav-link @if (Request::routeIs('admin.overview.*')) active @endif">
                                <i class="nav-icon fa fa-home"></i>
                                <p>{{ __('Overview') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::TICKET_ADMIN_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.ticket.index') }}"
                               class="nav-link @if (Request::routeIs('admin.ticket.index')) active @endif">
                                <i class="nav-icon fas fa-ticket-alt"></i>
                                <p>{{ __('Ticket List') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::TICKET_BLACKLIST_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.ticket.blacklist') }}"
                               class="nav-link @if (Request::routeIs('admin.ticket.blacklist')) active @endif">
                                <i class="nav-icon fas fa-user-times"></i>
                                <p>{{ __('Ticket Blacklist') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::ROLES_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.roles.index') }}"
                               class="nav-link @if (Request::routeIs('admin.roles.*')) active @endif">
                                <i class="nav-icon fa fa-user-check"></i>
                                <p>{{ __('Role Management') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::SETTINGS_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.index') . '#icons' }}"
                               class="nav-link @if (Request::routeIs('admin.settings.*')) active @endif">
                                <i class="nav-icon fas fa-tools"></i>
                                <p>{{ __('Settings') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::API_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.api.index') }}"
                               class="nav-link @if (Request::routeIs('admin.api.*')) active @endif">
                                <i class="nav-icon fa fa-gamepad"></i>
                                <p>{{ __('Application API') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(array_merge(
                        PermissionGroups::USERS_PERMISSIONS,
                        PermissionGroups::SERVERS_PERMISSIONS,
                        PermissionGroups::PRODUCTS_PERMISSIONS
                    ))
                        <li class="nav-header">{{ __('Management') }}</li>
                    @endcanany

                    @canany(PermissionGroups::USERS_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}"
                               class="nav-link @if (Request::routeIs('admin.users.*')) active @endif">
                                <i class="nav-icon fas fa-users"></i>
                                <p>{{ __('Users') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::SERVERS_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.servers.index') }}"
                               class="nav-link @if (Request::routeIs('admin.servers.*')) active @endif">
                                <i class="nav-icon fas fa-server"></i>
                                <p>{{ __('Servers') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::PRODUCTS_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.products.index') }}"
                               class="nav-link @if (Request::routeIs('admin.products.*')) active @endif">
                                <i class="nav-icon fas fa-sliders-h"></i>
                                <p>{{ __('Products') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::STORE_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.store.index') }}"
                               class="nav-link @if (Request::routeIs('admin.store.*')) active @endif">
                                <i class="nav-icon fas fa-shopping-basket"></i>
                                <p>{{ __('Store') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::VOUCHERS_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.vouchers.index') }}"
                               class="nav-link @if (Request::routeIs('admin.vouchers.*')) active @endif">
                                <i class="nav-icon fas fa-money-check-alt"></i>
                                <p>{{ __('Vouchers') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::PARTNERS_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.partners.index') }}"
                               class="nav-link @if (Request::routeIs('admin.partners.*')) active @endif">
                                <i class="nav-icon fas fa-handshake"></i>
                                <p>{{ __('Partners') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::COUPONS_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.coupons.index') }}"
                               class="nav-link @if (Request::routeIs('admin.coupons.*')) active @endif">
                                <i class="nav-icon fas fa-ticket-alt"></i>
                                <p>{{ __('Coupons') }}</p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::USEFUL_LINKS_PERMISSIONS)
                        <li class="nav-header">{{ __('Other') }}</li>
                    @endcanany

                    @canany(PermissionGroups::USEFUL_LINKS_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.usefullinks.index') }}"
                               class="nav-link @if (Request::routeIs('admin.usefullinks.*')) active @endif">
                                <i class="nav-icon fas fa-link"></i>
                                <p>{{ __('Useful Links') }}</p>
                            </a>
                        </li>
                    @endcanany


                    @canany(array_merge(
                        PermissionGroups::PAYMENTS_PERMISSIONS,
                        PermissionGroups::LOGS_PERMISSIONS
                    ))
                        <li class="nav-header">{{ __('Logs') }}</li>
                    @endcanany

                    @canany(PermissionGroups::PAYMENTS_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.payments.index') }}"
                               class="nav-link @if (Request::routeIs('admin.payments.*')) active @endif">
                                <i class="nav-icon fas fa-money-bill-wave"></i>
                                <p>{{ __('Payments') }}
                                    <span class="badge badge-success right">{{ \App\Models\Payment::count() }}</span>
                                </p>
                            </a>
                        </li>
                    @endcanany

                    @canany(PermissionGroups::LOGS_PERMISSIONS)
                        <li class="nav-item">
                            <a href="{{ route('admin.activitylogs.index') }}"
                               class="nav-link @if (Request::routeIs('admin.activitylogs.*')) active @endif">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>{{ __('Activity Logs') }}</p>
                            </a>
                        </li>
                    @endcanany
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
    @endif

    <!-- Content Wrapper. Contains page content -->

    <div class="content-wrapper">

        <!--
            @if (!Auth::user()->hasVerifiedEmail())
            @if (Auth::user()->created_at->diffInHours(now(), false) > 1)
                <div class="p-2 m-2 alert alert-warning">
                    <h5><i class="icon fas fa-exclamation-circle"></i> {{ __('Warning!') }}</h5>
                        {{ __('You have not yet verified your email address') }} <a class="text-primary"
                            href="{{ route('verification.send') }}">{{ __('Click here to resend verification email') }}</a>
                        <br>
                        {{ __('Please contact support If you didnt receive your verification email.') }}
                </div>

            @endif
        @endif
        -->

        @yield('content')

        @include('modals.redeem_voucher_modal')
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2021-{{ date('Y') }} <a
                    href="{{ url('/') }}">{{ config('app.name', "Ctrlpanel.gg") }}</a>.</strong>
        All rights
        reserved. Powered by <a href="https://CtrlPanel.gg">CtrlPanel</a>.
        @if (!str_contains(config('BRANCHNAME'), 'main') && !str_contains(config('BRANCHNAME'), 'unknown'))
            Version <b>{{ config('app')['version'] }} - {{ config('BRANCHNAME') }}</b>
        @endif

        {{-- Show imprint and privacy link --}}
        <div class="float-right d-none d-sm-inline-block">
            @if ($website_settings->show_imprint)
                <a target="_blank" href="{{ route('terms', 'imprint') }}"><strong>{{ __('Imprint') }}</strong></a> |
            @endif
            @if ($website_settings->show_privacy)
                <a target="_blank" href="{{ route('terms', 'privacy') }}"><strong>{{ __('Privacy') }}</strong></a>
            @endif
            @if ($website_settings->show_tos)
                | <a target="_blank"
                     href="{{ route('terms', 'tos') }}"><strong>{{ __('Terms of Service') }}</strong></a>
            @endif
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- Scripts -->
<script src="{{ asset('plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- select2 -->
<script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>

<!-- Moment.js -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>

<!-- Datetimepicker -->
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

<!-- Select2 -->
<script src={{ asset('plugins/select2/js/select2.min.js') }}></script>


<script>
    $(document).ready(function () {
        $('[data-toggle="popover"]').popover();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    $(document).on('alpine:init', () => {
        Alpine.magic('currency', () => {
            return {
                format: (amount) => {
                    return (amount / 1000);
                },
            }
        });
    })
</script>
<script>
    @if (Session::has('error'))
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        html: '{{ Session::get('error') }}',
    })
    @endif
    @if (Session::has('success'))
    Swal.fire({
        icon: 'success',
        title: '{{ Session::get('success') }}',
        position: 'top-end',
        showConfirmButton: false,
        background: '#343a40',
        toast: true,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    @endif
    @if (Session::has('info'))
    Swal.fire({
        icon: 'info',
        title: '{{ Session::get('info') }}',
        position: 'top-end',
        showConfirmButton: false,
        background: '#343a40',
        toast: true,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    @endif
    @if (Session::has('warning'))
    Swal.fire({
        icon: 'warning',
        title: '{{ Session::get('warning') }}',
        position: 'top-end',
        showConfirmButton: false,
        background: '#343a40',
        toast: true,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    @endif
</script>
</body>

</html>
