<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .btn { display: inline-block; padding: 10px 16px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 6px; }
        .muted { color: #666; font-size: 12px; }
    </style>
    </head>
    <body>
        <div class="container">
            <h2>Verifikasi Email Anda</h2>
            <p>Halo {{ $user->name }},</p>
            <p>Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda dan mengaktifkan akun.</p>
            <p>
                <a class="btn" href="{{ $verificationUrl }}" target="_blank" rel="noopener">Verifikasi Email</a>
            </p>
            <p class="muted">Link verifikasi berlaku selama 60 menit.</p>
            <p>Jika Anda tidak meminta verifikasi ini, abaikan email ini.</p>
        </div>
    </body>
</html>