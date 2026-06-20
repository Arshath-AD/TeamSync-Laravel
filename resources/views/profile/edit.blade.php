<x-app-layout>
    <x-slot name="header">Profile</x-slot>

    <div class="fade-in max-w-4xl mx-auto space-y-5">

        {{-- Profile Overview Card --}}
        <div class="ts-card p-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">

                {{-- Avatar --}}
                <div class="flex-shrink-0 flex flex-col items-center gap-3">
                    <div id="avatar-preview-wrapper" class="relative group">
                        <img id="avatar-preview-img"
                             src="{{ Auth::user()->avatarUrl() }}"
                             alt="{{ Auth::user()->name }}"
                             class="w-20 h-20 rounded-full object-cover ring-2 ring-[var(--border)]"
                             onerror="this.src='{{ asset('images/default-avatar.jpg') }}'">
                        <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer"
                             onclick="document.getElementById('avatar-file-input').click()">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="flex-1 text-center sm:text-left">
                    <h2 class="text-lg font-semibold" style="color:var(--text)">{{ Auth::user()->name }}</h2>
                    <p class="text-sm mt-0.5" style="color:var(--secondary)">{{ Auth::user()->email }}</p>
                    <div class="flex justify-center sm:justify-start gap-2 mt-2">
                        @if(Auth::user()->role === 'admin')
                            <span class="text-[11px] px-2 py-0.5 rounded font-medium"
                                  style="background:var(--accent-dim);color:var(--accent);border:1px solid rgba(59,130,246,0.3)">Administrator</span>
                        @else
                            <span class="text-[11px] px-2 py-0.5 rounded font-medium"
                                  style="background:var(--elevated);color:var(--secondary);border:1px solid var(--border)">Team Member</span>
                        @endif
                        <span class="text-[11px] px-2 py-0.5 rounded" style="color:var(--secondary);background:var(--elevated);border:1px solid var(--border)">
                            Joined {{ Auth::user()->created_at ? Auth::user()->created_at->format('M Y') : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Avatar Upload Form (hidden file + preview logic) --}}
            <form id="avatar-form" method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="mt-5 pt-5 border-t border-[var(--border)]">
                @csrf
                <input type="file" id="avatar-file-input" name="avatar"
                       accept=".jpg,.jpeg,.png,.webp"
                       class="hidden" onchange="previewAvatar(event)">

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <div class="flex-1">
                        <label class="ts-label mb-0">Profile Picture</label>
                        <p class="text-[11px] mt-0.5" style="color:var(--secondary)">JPG, JPEG, PNG, WebP · max 2 MB</p>
                        @if(session('status') === 'avatar-updated')
                            <p class="text-[11px] mt-1" style="color:var(--success)">✓ Avatar updated successfully.</p>
                        @endif
                        @error('avatar') <p class="text-[11px] mt-1" style="color:var(--danger)">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="document.getElementById('avatar-file-input').click()"
                                class="ts-btn-secondary text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Choose File
                        </button>
                        <button type="submit" id="avatar-save-btn" class="ts-btn-primary text-xs hidden">Save Avatar</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Account Information --}}
        <div class="ts-card p-5 sm:p-6">
            <div class="mb-4 pb-3 border-b border-[var(--border)]">
                <h3 class="text-sm font-semibold" style="color:var(--text)">Account Information</h3>
                <p class="text-xs mt-0.5" style="color:var(--secondary)">Update your name and email address.</p>
            </div>

            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Security / Password --}}
        <div class="ts-card p-5 sm:p-6">
            <div class="mb-4 pb-3 border-b border-[var(--border)]">
                <h3 class="text-sm font-semibold" style="color:var(--text)">Security</h3>
                <p class="text-xs mt-0.5" style="color:var(--secondary)">Keep your account secure with a strong password.</p>
            </div>

            @include('profile.partials.update-password-form')
        </div>

        {{-- Danger Zone --}}
        <div class="ts-card p-5 sm:p-6" style="border-color:rgba(239,68,68,0.25)">
            <div class="mb-4 pb-3 border-b border-[var(--border)]">
                <h3 class="text-sm font-semibold" style="color:var(--danger)">Danger Zone</h3>
                <p class="text-xs mt-0.5" style="color:var(--secondary)">Permanently delete your account and all associated data.</p>
            </div>

            @include('profile.partials.delete-user-form')
        </div>

    </div>

    <script>
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            // Remove initials div if present
            const initials = document.getElementById('avatar-preview-initials');
            if (initials) initials.remove();

            // Update or create img
            let img = document.getElementById('avatar-preview-img');
            if (!img) {
                img = document.createElement('img');
                img.id = 'avatar-preview-img';
                img.className = 'w-20 h-20 rounded-full object-cover ring-2 ring-[var(--border)]';
                img.alt = 'Preview';
                document.getElementById('avatar-preview-wrapper').prepend(img);
            }
            img.src = e.target.result;

            // Show save button
            document.getElementById('avatar-save-btn').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
    </script>
</x-app-layout>
