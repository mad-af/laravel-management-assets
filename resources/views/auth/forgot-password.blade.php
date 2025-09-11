@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<x-card title="Lupa Password" class="shadow-xl max-w-md w-full" shadow>
    <div class="p-6">
        <h2 class="text-center text-2xl font-bold mb-6">Lupa Password</h2>
        
        <p class="text-sm text-base-content/70 mb-6 text-center">
            Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
        </p>

        @if (session('status'))
            <div class="alert alert-success mb-4">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
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

            <!-- Submit Button -->
            <div class="form-control mt-6">
                <button type="submit" class="btn btn-primary w-full">
                    <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                    Kirim Link Reset Password
                </button>
            </div>
        </form>

        <!-- Back to Login -->
        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="link link-primary text-sm inline-flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                Kembali ke Login
            </a>
        </div>
    </div>
</div>
@endsection