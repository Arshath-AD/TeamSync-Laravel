<div>
    <p class="text-xs" style="color:var(--secondary)">
        Once your account is deleted, all data including projects, tasks, and team memberships will be permanently removed. This action cannot be undone.
    </p>

    <div class="mt-4" x-data="{ open: false }">
        <button type="button" @click="open = true" class="ts-btn-danger">Delete My Account</button>

        {{-- Confirmation Modal --}}
        <div x-show="open" x-transition
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="background:rgba(0,0,0,0.6)" @click.self="open = false">
            <div class="ts-panel w-full max-w-[320px] p-5 overflow-hidden relative fade-in" style="background:var(--surface)">
                <h3 class="text-sm font-semibold mb-1" style="color:var(--text)">Delete your account?</h3>
                <p class="text-xs mb-5" style="color:var(--secondary)">Enter your password to permanently delete your account.</p>

                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="mb-4">
                        <label class="ts-label" for="delete-password">Password</label>
                        <div class="relative" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'"
                                   id="delete-password" name="password"
                                   class="ts-input pr-9" placeholder="••••••••" required>
                            <button type="button" @click="show = !show"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 focus:outline-none"
                                    style="color:var(--secondary)">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password', 'userDeletion')
                            <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="open = false" class="ts-btn-secondary">Cancel</button>
                        <button type="submit" class="ts-btn-danger">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
