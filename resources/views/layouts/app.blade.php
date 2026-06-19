<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'TeamSync') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-workspace-background text-workspace-text h-screen flex overflow-hidden selection:bg-workspace-accent selection:text-white">

        @include('layouts.navigation')

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="h-12 bg-workspace-surface border-b border-workspace-border flex items-center justify-between px-4 flex-shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    @if (isset($header))
                        <h1 class="text-sm font-semibold text-workspace-text truncate">{{ $header }}</h1>
                    @endif
                </div>

                <div class="flex items-center gap-3 flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <div class="h-6 w-6 rounded bg-workspace-elevated border border-workspace-border flex items-center justify-center text-[10px] font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="text-xs text-workspace-secondary font-medium hidden sm:block">{{ Auth::user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-[11px] text-workspace-secondary hover:text-workspace-danger transition-colors">Logout</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-workspace-background p-4 custom-scrollbar">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
