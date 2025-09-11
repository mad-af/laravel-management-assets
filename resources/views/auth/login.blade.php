@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<x-card title="Login" class="shadow-xl max-w-md w-full" shadow>
    <div class="p-6">
        <h2 class="text-center text-2xl font-bold mb-6">Login</h2>
        
        @if (session('status'))
            <div class="alert alert-success mb-4">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.authenticate') }}">
            @csrf

            <!-- Email -->
            <div class="form-control mb-4">
                <label class="label" for="email">
                    <span class="label-text">Email</span>
                </label>
                <input 
                    type="email" 
                    id="email"
                    name="email" 
                    value="{{ old('email') }}"
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
                    <span class="label-text">Password</span>
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        class="input input-bordered w-full pr-10 @error('password') input-error @enderror" 
                        placeholder="Masukkan password Anda"
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

            <!-- Remember Me -->
            <div class="form-control mb-6">
                <label class="label cursor-pointer justify-start">
                    <input type="checkbox" name="remember" class="checkbox checkbox-primary mr-3" {{ old('remember') ? 'checked' : '' }}>
                    <span class="label-text">Ingat saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="form-control mt-6">
                <button type="submit" class="btn btn-primary w-full">
                    <i data-lucide="log-in" class="w-4 h-4 mr-2"></i>
                    Login
                </button>
            </div>
        </form>

        <!-- Forgot Password -->
        <div class="text-center mt-6">
            <a href="{{ route('password.request') }}" class="link link-primary text-sm">
                Lupa password?
            </a>
        </div>
    </div>
</x-card>

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