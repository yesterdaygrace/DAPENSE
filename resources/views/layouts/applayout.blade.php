<!DOCTYPE html>

<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Dapense | {{ ucfirst(Auth::user()->usertype === 'rootsuperuser' ? 'Root Superuser' : (Auth::user()->usertype === 'bod' ? 'BOD' : Auth::user()->usertype)) }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <!-- Page CSS -->
    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <style>
        /* ========================================
               DAPENSE — Design Overrides
               Applied on top of the Bootstrap admin theme
               ======================================== */

        html { scroll-behavior: smooth; }

        /* --- Typography --- */
        body {
            font-family: 'Outfit', sans-serif;
        }

        h1, h2, h3, h4, h5, h6,
        .app-brand-text, .menu-text, .card-header h5 {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.02em;
        }

        h1 { font-weight: 700; }
        h2 { font-weight: 600; }
        h3 { font-weight: 600; }
        .card-header h5 {
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Sentence case headers */
        h1, h2, h3, h4, h5, h6 {
            text-transform: none;
        }

        /* --- Color: Banking/Finance palette --- */
        :root {
            --bs-primary: #1E3A8A;
            --bs-primary-rgb: 30, 58, 138;
            --bs-primary-hover: #15255A;
            --bs-primary-light: rgba(30, 58, 138, 0.08);
            --bs-accent-gold: #A16207;
            --bs-border: #E2E8F0;
        }

        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }
        .btn-primary:hover {
            background-color: #15255A;
            border-color: #15255A;
        }
        .btn-primary:active {
            transform: scale(0.98);
        }
        a { color: var(--bs-primary); }

        .bg-primary { background-color: var(--bs-primary) !important; }
        .text-primary { color: var(--bs-primary) !important; }

        .table-primary {
            --bs-table-bg: var(--bs-primary-light);
        }

        /* --- Card refinements (Soft UI Evolution) --- */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04), 0 2px 4px rgba(0,0,0,0.04);
            transition: box-shadow 250ms cubic-bezier(0.23, 1, 0.32, 1), transform 250ms cubic-bezier(0.23, 1, 0.32, 1);
        }
        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.06);
        }

        /* Dashboard menu cards */
        .card.text-center.shadow-lg {
            border: 1px solid rgba(0,0,0,0.04);
            border-radius: 12px;
            transition: transform 250ms cubic-bezier(0.23, 1, 0.32, 1), box-shadow 250ms cubic-bezier(0.23, 1, 0.32, 1);
        }
        .card.text-center.shadow-lg:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.08), 0 16px 40px rgba(0,0,0,0.06);
        }
        .card.text-center.shadow-lg i {
            color: var(--bs-primary);
            transition: transform 250ms cubic-bezier(0.23, 1, 0.32, 1);
        }
        .card.text-center.shadow-lg:hover i {
            transform: scale(1.1);
        }

        /* --- Table refinements --- */
        .table {
            font-size: 0.9rem;
        }
        .table thead th {
            font-weight: 600;
            text-transform: none;
            letter-spacing: 0.01em;
            border-bottom-width: 1px;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(30, 58, 138, 0.04);
        }

        /* --- Sidebar refinements (Soft UI Evolution) --- */
        .menu-item.active > .menu-link {
            background: var(--bs-primary-light);
            border-right: 3px solid var(--bs-primary);
        }
        .menu-link {
            transition: background 200ms ease, padding 200ms ease;
            cursor: pointer;
        }
        .menu-link:hover {
            background: rgba(0,0,0,0.04);
        }
        .menu-toggle::after {
            transition: transform 250ms cubic-bezier(0.23, 1, 0.32, 1);
        }
        .menu-item.open > .menu-link .menu-toggle::after {
            transform: rotate(90deg);
        }
        .menu-sub .menu-link {
            padding-left: 2rem;
        }

        /* --- Button refinements (Soft UI Evolution) --- */
        .btn {
            font-weight: 500;
            letter-spacing: 0.01em;
            border-radius: 8px;
            transition: transform 200ms cubic-bezier(0.23, 1, 0.32, 1), background-color 200ms ease, box-shadow 200ms ease;
            cursor: pointer;
        }
        .btn:active {
            transform: scale(0.97);
        }
        .btn-success {
            background-color: #1a7d5a;
            border-color: #1a7d5a;
        }
        .btn-success:hover {
            background-color: #146649;
            border-color: #146649;
        }
        .btn-warning {
            color: #fff;
            background-color: #b8872a;
            border-color: #b8872a;
        }
        .btn-warning:hover {
            color: #fff;
            background-color: #9a7022;
            border-color: #9a7022;
        }
        .btn-danger {
            background-color: #b33a3a;
            border-color: #b33a3a;
        }
        .btn-danger:hover {
            background-color: #962e2e;
            border-color: #962e2e;
        }

        /* --- Form controls (Soft UI Evolution) --- */
        .form-control {
            border-radius: 8px;
            transition: border-color 200ms ease, box-shadow 200ms ease;
        }
        .form-control:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15);
            outline: none;
        }
        .form-control:hover {
            border-color: rgba(30, 58, 138, 0.3);
        }

        /* Focus visible for keyboard nav (WCAG) */
        :focus-visible {
            outline: 2px solid var(--bs-primary);
            outline-offset: 2px;
        }
        .btn:focus-visible,
        .form-control:focus-visible {
            outline: 2px solid var(--bs-primary);
            outline-offset: 2px;
        }

        /* cursor-pointer on all clickable elements */
        label[for], [onclick], [role="button"], .dropdown-item, .menu-link {
            cursor: pointer;
        }

        /* --- Alert refinements --- */
        .alert {
            border: none;
            border-radius: 8px;
        }
        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
        }
        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
        }
        .alert-warning {
            background-color: #fffbeb;
            color: #92400e;
        }

        /* --- Spacing refinements --- */
        .container-p-y {
            padding-top: 1.5rem;
            padding-bottom: 2rem;
        }

        /* --- Interactive card hover for dashboard --- */
        a.text-decoration-none .card {
            cursor: pointer;
        }

        /* --- Toast refinements --- */
        .toast {
            border: none;
        }
        .toast-header {
            font-weight: 600;
        }

        /* --- Skeleton loading animation --- */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s ease-in-out infinite;
            border-radius: 6px;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* --- Toast enter/exit animation --- */
        .toast.showing {
            animation: toastIn 300ms cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }
        .toast.hiding {
            animation: toastOut 200ms ease-out forwards;
        }
        @keyframes toastIn {
            from { opacity: 0; transform: translateY(-12px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes toastOut {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-8px) scale(0.96); }
        }

        /* --- Reduced motion --- */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            .skeleton {
                animation: none;
                background: #f0f0f0;
            }
            html { scroll-behavior: auto; }
        }

        /* --- Grain overlay for depth --- */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9999;
            opacity: 0.015;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            background-repeat: repeat;
            background-size: 256px 256px;
        }
    </style>
</head>

<body>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('storage/' . Auth::user()->image) }}" alt="{{ Auth::user()->name }}" class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="{{ asset('storage/' . Auth::user()->image) }}" alt="{{ Auth::user()->name }}" class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                                                    <small class="text-muted">{{ Auth::user()->usertype }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Profile</span>
                                        </a>
                                    </li>
                                    @if (in_array(Auth::user()->usertype, ['admin', 'rootsuperuser']))
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route(Auth::user()->usertype . '/otorisator/home') }}">
                                            <i class="bx bx-cog me-2"></i>
                                            <span class="align-middle">Pengaturan Otorisator</span>
                                        </a>
                                    </li>
                                    @endif
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->
                @yield('content')

                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->
    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <!-- Page JS -->
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
