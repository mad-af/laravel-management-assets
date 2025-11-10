@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<x-card title="Login" class="w-full max-w-md shadow-xl" shadow>
    <div class="p-6">
        <h2 class="mb-6 text-2xl font-bold text-center">Login</h2>
        
        @if (session('status'))
            <div class="mb-4 alert alert-success">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.authenticate') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4 form-control">
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
            <div class="mb-4 form-control">
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
                        class="flex absolute inset-y-0 right-0 items-center pr-3"
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
            <div class="mb-6 form-control">
                <label class="justify-start cursor-pointer label">
                    <input type="checkbox" name="remember" class="mr-3 checkbox checkbox-primary" {{ old('remember') ? 'checked' : '' }}>
                    <span class="label-text">Ingat saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 form-control">
                <button type="submit" class="w-full btn btn-primary">
                    <i data-lucide="log-in" class="mr-2 w-4 h-4"></i>
                    Login
                </button>
            </div>
        </form>

        <!-- Forgot Password -->
        <div class="mt-6 text-center">
            <a href="{{ route('password.request') }}" class="text-sm link link-primary">
                Lupa password?
            </a>
        </div>

        <!-- Info Akun Contoh -->
        @if (config('app.env') != 'production')
        <div class="mt-6">
            <div class="alert alert-info">
                <div>
                    <div class="font-semibold">Info Akun</div>
                    <div class="mt-2 text-sm">Masuk dengan akun contoh berikut:</div>
                    <ul class="mt-2 text-sm leading-relaxed">
                        <li>
                            <span class="block font-mono">admin@example.com</span>  
                            <span class="block font-mono">password</span>
                        </li>
                    </ul>
                    <div class="mt-2 text-xs text-base-content/70">Anda dapat mengubah atau menonaktifkan akun contoh ini di seeders.</div>
                </div>
            </div>
        </div>
        @endif
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