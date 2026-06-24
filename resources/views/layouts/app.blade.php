<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($pageTitle) ? $pageTitle . ' — ' : '' }}{{ config('app.name', 'TeamSync') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">

    <!-- Theme system: must run synchronously BEFORE any CSS or Vite loads -->
    <script>
        (function () {
            // Apply saved theme immediately (prevents flash)
            var saved = localStorage.getItem('ts-theme') || 'dark';
            document.documentElement.classList.remove('dark', 'light');
            document.documentElement.classList.add(saved);

            // Expose toggle globally — defined here so it works even before app.js loads
            window.toggleTheme = function () {
                var root = document.documentElement;
                var next = root.classList.contains('light') ? 'dark' : 'light';
                root.classList.remove('dark', 'light');
                root.classList.add(next);
                localStorage.setItem('ts-theme', next);

                // Sync sun/moon icons
                document.querySelectorAll('.icon-sun').forEach(function (el) {
                    el.classList.toggle('hidden', next !== 'dark');
                });
                document.querySelectorAll('.icon-moon').forEach(function (el) {
                    el.classList.toggle('hidden', next !== 'light');
                });
            };
            // Apply saved sidebar state
            var sidebarCollapsed = localStorage.getItem('ts-sidebar-collapsed') === 'true';
            if (sidebarCollapsed) {
                document.documentElement.classList.add('sidebar-collapsed');
            }

            window.toggleSidebar = function () {
                var root = document.documentElement;
                var isCollapsed = root.classList.toggle('sidebar-collapsed');
                localStorage.setItem('ts-sidebar-collapsed', isCollapsed);
            };
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-screen flex overflow-hidden" style="background:var(--bg);color:var(--text);">

    {{-- ── Sidebar ──────────────────────────────────────────────── --}}
    @include('layouts.navigation')

    {{-- ── Main column ─────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- ── Topbar ── --}}
        <header class="ts-topbar h-11 flex items-center justify-between px-4 flex-shrink-0 z-10">

            {{-- Left: breadcrumb / page title --}}
            <div class="flex items-center gap-2 min-w-0">
                @if(isset($header))
                    <div class="text-xs font-semibold truncate" style="color:var(--text)">{{ $header }}</div>
                @endif
            </div>

            {{-- Right: search + theme + notifications + profile --}}
            <div class="flex items-center gap-2 flex-shrink-0">

                {{-- Global search --}}
                <div class="ts-search hidden sm:flex">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    <input type="text" placeholder="Search…" aria-label="Global search">
                    <span class="text-[10px] opacity-40 flex-shrink-0">⌘K</span>
                </div>

                {{-- Theme toggle --}}
                <button class="theme-btn" onclick="toggleTheme()" title="Toggle theme" aria-label="Toggle theme">
                    {{-- Sun icon (dark mode → switch to light) --}}
                    <svg class="icon-sun w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m8.66-9H21M3 12H2m15.36-6.36l-.71.71M6.34 17.66l-.71.71M17.66 17.66l-.71-.71M6.34 6.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z"/>
                    </svg>
                    {{-- Moon icon (light mode → switch to dark) --}}
                    <svg class="icon-moon w-3.5 h-3.5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                </button>

                {{-- Notifications (placeholder) --}}
                <div class="notif-btn" title="Notifications" aria-label="Notifications">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a1 1 0 10-2 0v1.083A6 6 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="notif-dot"></span>
                </div>

                {{-- Profile dropdown --}}
                <div class="relative">
                    <button id="profile-trigger"
                            class="flex items-center gap-2 px-2 py-1 rounded-md transition-all"
                            style="border:1px solid var(--border);background:var(--elevated);"
                            aria-label="Profile menu">
                        <x-workspace.avatar :user="Auth::user()" sizeClass="w-6 h-6" />
                        <span class="text-[11px] hidden sm:block font-medium" style="color:var(--text)">{{ Auth::user()->name }}</span>
                        <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="profile-menu"
                         class="ts-dropdown hidden absolute right-0 mt-1.5 fade-in"
                         style="min-width:180px;">
                        <div class="px-3 py-2 border-b" style="border-color:var(--border)">
                            <div class="text-xs font-semibold" style="color:var(--text)">{{ Auth::user()->name }}</div>
                            <div class="text-[10px]" style="color:var(--secondary)">{{ Auth::user()->email }}</div>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="ts-dropdown-item">
                            <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile
                        </a>
                        <button type="button" class="ts-dropdown-item w-full text-left" onclick="window.dispatchEvent(new CustomEvent('open-about'))">
                            <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            About TeamSync
                        </button>
                        <div class="ts-dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="ts-dropdown-item danger w-full text-left">
                                <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        {{-- ── Page content ── --}}
        <main class="flex-1 overflow-y-auto ts-scroll p-5" style="background:var(--bg)">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="ts-alert-success mb-4 fade-in" data-flash>{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="ts-alert-danger mb-4 fade-in" data-flash>{{ session('error') }}</div>
            @endif

            {{ $slot }}
        </main>
    </div>

    {{-- About TeamSync Modal --}}
    <div x-data="{ open: false }"
         @open-about.window="open = true"
         @keydown.escape.window="open = false"
         class="relative z-50"
         style="display: none;"
         x-show="open">
        
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div class="fixed inset-0 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="open" 
                     x-transition.scale.origin.bottom
                     @click.away="open = false"
                     class="ts-panel w-full max-w-md p-8 overflow-hidden relative fade-in text-center"
                     style="background:var(--surface)">
                    
                    <button @click="open = false" class="absolute top-4 right-4 transition-colors" style="color:var(--secondary)" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--secondary)'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <div class="flex flex-col items-center justify-center mb-6 mt-2">
                        <x-logo class="h-10 w-auto mb-3 rounded-lg overflow-hidden shadow-sm" />
                        <h2 class="text-lg font-semibold leading-tight" style="color:var(--text)">TeamSync</h2>
                        <p class="text-xs mt-1" style="color:var(--secondary)">Version 1.0</p>
                    </div>

                    <div class="text-sm space-y-1 mb-8">
                        <p style="color:var(--secondary)">Created and maintained by</p>
                        <p class="font-medium" style="color:var(--text)">Arshath AD</p>
                    </div>

                    <div class="w-full h-px mb-6" style="background:var(--border)"></div>

                    <div class="flex items-center justify-center gap-4 mb-6">
                        <a href="https://linkedin.com/in/arshathad" target="_blank" rel="noopener" class="text-sm font-medium transition-colors hover-lift" style="color:var(--accent)">
                            LinkedIn
                        </a>
                        <span style="color:var(--border)">|</span>
                        <a href="https://github.com/Arshath-AD" target="_blank" rel="noopener" class="text-sm font-medium transition-colors hover-lift" style="color:var(--accent)">
                            GitHub
                        </a>
                        <span style="color:var(--border)">|</span>
                        <a href="https://arshath-ad.github.io" target="_blank" rel="noopener" class="text-sm font-medium transition-colors hover-lift" style="color:var(--accent)">
                            Portfolio
                        </a>
                    </div>

                    <div class="w-full h-px mb-6" style="background:var(--border)"></div>

                    <div class="text-[11px] leading-tight" style="color:var(--secondary); opacity: 0.7;">
                        &copy; 2026 Arshath AD<br>
                        All Rights Reserved
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
