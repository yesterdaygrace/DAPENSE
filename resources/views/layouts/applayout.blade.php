<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>WAS | {{ ucfirst(Auth::user()->usertype === 'rootsuperuser' ? 'Root Superuser' : (Auth::user()->usertype === 'bod' ? 'BOD' : Auth::user()->usertype)) }}</title>
    <meta name="description" content="WAS — Web Accounting System" />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" />

    <script>
        (function() {
            window.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    e.stopImmediatePropagation();
                    window.location.reload();
                }
            }, true);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background antialiased" x-data="{ sidebarOpen: false }">

    <!-- Skip to content -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[9999] focus:px-4 focus:py-2 focus:bg-primary focus:text-white focus:rounded-button">
        Skip to content
    </a>

    <!-- Toast -->
    <x-toast />

    <!-- Loading -->
    <x-loading />

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black/30 backdrop-blur-sm lg:hidden"
         x-transition.opacity.duration.300ms>
    </div>

    <!-- Sidebar (fixed) -->
    @include('components.sidebar')

    <!-- Main content area -->
    <div class="main-content flex flex-col min-h-screen lg:ml-[72px]">
        <!-- Top navbar -->
        @include('components.topbar')

        <!-- Page content -->
        <main id="main-content" class="flex-1 overflow-y-auto">
            <div class="content-container px-6 lg:px-8 py-6 lg:py-8">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-100 bg-white/60 backdrop-blur-sm px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between text-xs text-gray-500 max-w-content mx-auto">
                <span>&copy; {{ date('Y') }} WAS — Web Accounting System</span>
                <span class="flex items-center gap-2">
                    {{ Auth::user()->name }}
                    <span class="badge badge-primary">{{ ucfirst(Auth::user()->usertype === 'rootsuperuser' ? 'Root Superuser' : Auth::user()->usertype) }}</span>
                </span>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
