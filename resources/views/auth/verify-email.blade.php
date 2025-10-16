<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; background: #f8fafc; }
        .container { max-width: 480px; margin: 60px auto; background: #fff; padding: 24px; border-radius: 10px; box-shadow: 0 1px 8px rgba(0,0,0,0.06); }
        h1 { font-size: 20px; margin-bottom: 12px; }
        .desc { color: #555; margin-bottom: 18px; }
        .status { margin-bottom: 12px; color: #0f766e; }
        .btn { display: inline-block; padding: 10px 14px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 8px; }
        .btn:disabled { opacity: .6; cursor: not-allowed; }
        form { display: inline; }
        .muted { color: #777; font-size: 12px; margin-top: 8px; }
    </style>
    </head>
    <body>
        <div class="container">
            <h1>Verifikasi Email Diperlukan</h1>
            <p class="desc">Akun Anda belum diverifikasi. Silakan kirim email verifikasi dan klik link di email untuk mengaktifkan akun.</p>
            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="btn" type="submit">Kirim Ulang Email Verifikasi</button>
            </form>
            <p class="muted">Pastikan Anda mengecek folder inbox dan spam.</p>
        </div>
    </body>
</html>