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
    <body class="font-sans antialiased bg-workspace-background text-workspace-text">
        <div class="min-h-screen flex flex-col sm:justify-center items-center p-4">
            <a href="/" class="flex items-center gap-2 mb-6 group">
                <div class="h-8 w-8 bg-workspace-accent rounded flex items-center justify-center text-white font-bold text-xs group-hover:bg-indigo-400 transition-colors">TS</div>
                <span class="font-bold text-workspace-text tracking-tight">TeamSync</span>
            </a>

            <div class="w-full sm:max-w-md ws-panel p-6">
                {{ $slot }}
            </div>

            <p class="mt-6 text-[10px] text-workspace-secondary/60">TeamSync Workspace</p>
        </div>
    </body>
</html>
