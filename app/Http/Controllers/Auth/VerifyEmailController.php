<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class VerifyEmailController extends Controller
{
    /**
     * Show verification notice page.
     */
    public function notice(Request $request)
    {
        return view('auth.verify-email');
    }

    /**
     * Send verification email with signed link.
     */
    public function send(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->email_verified_at) {
            return redirect()->route('dashboard');
        }

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->email),
            ]
        );

        try {
            $mailer = Mail::to($user->email);
            $mailer->send(new VerifyEmailNotification($user, $verificationUrl));
        } catch (\Exception $e) {
            report($e);

            return back()->with('error', 'Gagal mengirim email verifikasi.');
        }

        return back()->with('success', 'Email verifikasi telah dikirim.');
    }

    /**
     * Verify the signed email link.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals($hash, sha1($user->email))) {
            abort(403, 'Invalid verification link');
        }

        if (! $request->hasValidSignature()) {
            abort(403, 'Link verifikasi tidak valid atau kedaluwarsa');
        }

        if (! $user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }

        // Jika user sedang login, arahkan ke dashboard. Jika tidak, arahkan ke halaman login.
        if ($request->user()) {
            return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi.');
        }

        return redirect()->route('login')->with('success', 'Email berhasil diverifikasi. Silakan login.');
    }
}
