@php
$u = Auth::user()->usertype;
@endphp

<header class="h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-6 lg:px-8 sticky top-0 z-10">
    <div class="flex items-center gap-4">
        <button type="button" class="lg:hidden -ml-2 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" @click="sidebarOpen = !sidebarOpen">
            <i class="bx bx-menu text-xl" aria-hidden="true"></i>
        </button>
        <nav aria-label="breadcrumb" class="hidden sm:flex items-center gap-1.5 text-sm">
            <span class="sr-only">You are here:</span>
            <a href="{{ route(match($u) {
                'rootsuperuser' => 'rootsuperuser/dashboard',
                'admin' => 'admin/dashboard',
                'operator' => 'operator/dashboard',
                'bod' => 'bod/dashboard',
                default => 'dashboard',
            }) }}" class="text-gray-400 hover:text-gray-600 transition-colors" aria-label="Home">
                <i class="bx bx-home-alt text-sm" aria-hidden="true"></i>
            </a>
            <i class="bx bx-chevron-right text-xs text-gray-300" aria-hidden="true"></i>
            <span class="text-gray-500 font-medium">@yield('title', 'Dasbor')</span>
        </nav>
    </div>

    <div class="flex items-center gap-2">
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" aria-haspopup="true" :aria-expanded="open" class="flex items-center gap-2.5 px-3 py-1.5 hover:bg-gray-50 rounded-button transition-colors">
                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center overflow-hidden ring-2 ring-white">
                    @if(Auth::user()->image)
                    <img src="{{ asset('storage/' . Auth::user()->image) }}" alt="" class="w-full h-full object-cover" />
                    @else
                    <i class="bx bxs-user text-primary text-sm"></i>
                    @endif
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-sm font-semibold text-gray-900 leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-2xs text-gray-400 capitalize leading-tight font-medium">{{ $u === 'rootsuperuser' ? 'Root Superuser' : $u }}</p>
                </div>
                <i class="bx bx-chevron-down text-gray-300 text-sm" aria-hidden="true"></i>
            </button>

            <div x-show="open" @click.outside="open = false" class="dropdown-menu origin-top-right" role="menu" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <a href="{{ route('profile.edit') }}" class="dropdown-item" role="menuitem">
                    <i class="bx bx-user text-base text-gray-400" aria-hidden="true"></i>
                    <span>Profil</span>
                </a>
                <div class="border-t border-gray-50 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item w-full text-left text-red-600" role="menuitem">
                        <i class="bx bx-log-out text-base" aria-hidden="true"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
