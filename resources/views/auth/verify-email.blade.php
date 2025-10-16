@extends('layouts.auth')

@section('title', 'Verifikasi Email')

@section('content')
    <div class="shadow-xl card bg-base-100">
        <div class="card-body">
            <h2 class="card-title">Verifikasi Email Diperlukan</h2>
            <p class="text-base-content/70">Akun Anda belum diverifikasi. Silakan kirim email verifikasi dan klik link di email untuk mengaktifkan akun.</p>
            
            <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
                @csrf
                <x-button type="submit" icon="o-envelope" class="w-full btn btn-primary btn-sm">Kirim Ulang Email Verifikasi</x-button>
            </form>

            <p class="mt-4 text-sm text-base-content/70">Pastikan Anda mengecek folder inbox dan spam.</p>
            
            <form method="POST" action="{{ route('logout') }}" class="flex justify-end mt-2">
                @csrf
                <x-button type="submit" icon="o-power" class="btn btn-sm btn-ghost text-error">Keluar</x-button>
            </form>
        </div>
    </div>
@endsection