@props(['user', 'sizeClass' => 'w-7 h-7', 'class' => ''])

@php
    $src = ($user && $user->avatar) ? \Illuminate\Support\Facades\Storage::url($user->avatar) : asset('images/default-avatar.jpg');
    $alt = $user->name ?? '?';
@endphp

<img src="{{ $src }}" 
     alt="{{ $alt }}" 
     class="{{ $sizeClass }} {{ $class }} rounded-full flex-shrink-0 object-cover ring-1" 
     style="ring-color:var(--border)"
     onerror="this.src='{{ asset('images/default-avatar.jpg') }}'">
