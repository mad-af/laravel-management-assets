@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<x-card title="Reset Password" class="shadow-xl max-w-md w-full" shadow>
    <div class="p-6">
        <h2 class="text-center text-2xl font-bold mb-6">Reset Password</h2>
        
        <p class="text-sm text-base-content/70 mb-6 text-center">
            Masukkan email dan password baru Anda.
        </p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email -->
            <div class="form-control mb-4">
                <label class="label" for="email">
                    <span class="label-text">Email</span>
                </label>
                <input 
                    type="email" 
                    id="email"
                    name="email" 
                    value="{{ old('email', $email ?? '') }}"
                    class="input input-bordered w-full @error('email') input-error @enderror" 
                    placeholder="Masukkan email Anda"
                    required 
                    autofocus
                >
                @error('email')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-control mb-4">
                <label class="label" for="password">
                    <span class="label-text">Password Baru</span>
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        class="input input-bordered w-full pr-10 @error('password') input-error @enderror" 
                        placeholder="Masukkan password baru"
                        required
                    >
                    <button 
                        type="button" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        onclick="togglePassword('password')"
                    >
                        <i data-lucide="eye" class="w-4 h-4 text-base-content/50" id="password-eye"></i>
                    </button>
                </div>
                @error('password')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-control mb-6">
                <label class="label" for="password_confirmation">
                    <span class="label-text">Konfirmasi Password</span>
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password_confirmation"
                        name="password_confirmation" 
                        class="input input-bordered w-full pr-10" 
                        placeholder="Konfirmasi password baru"
                        required
                    >
                    <button 
                        type="button" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        onclick="togglePassword('password_confirmation')"
                    >
                        <i data-lucide="eye" class="w-4 h-4 text-base-content/50" id="password_confirmation-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-control mt-6">
                <button type="submit" class="btn btn-primary w-full">
                    <i data-lucide="key" class="w-4 h-4 mr-2"></i>
                    Reset Password
                </button>
            </div>
        </form>

        <!-- Back to Login -->
        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="link link-primary text-sm">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                Kembali ke Login
            </a>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        eye.setAttribute('data-lucide', 'eye-off');
    } else {
        field.type = 'password';
        eye.setAttribute('data-lucide', 'eye');
    }
    
    // Re-initialize lucide icons
    lucide.createIcons();
}
</script>
@endsection