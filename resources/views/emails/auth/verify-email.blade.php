<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Email</title>
    <style>
      /* Hanya sedikit CSS; mayoritas styling di-inline untuk kompatibilitas klien email */
      @media only screen and (max-width: 620px) {
        .container { width: 100% !important; }
        .inner-padding { padding: 24px !important; }
        .h1 { font-size: 22px !important; }
        .btn a { display: block !important; width: 100% !important; }
      }
    </style>
  </head>
  <body style="margin:0; padding:0; background-color:#f4f5f7; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">
    <!-- Preheader (teks singkat yang muncul di inbox preview) -->
    <span class="preheader" style="display:none !important; visibility:hidden; opacity:0; color:transparent; height:0; width:0; overflow:hidden; mso-hide:all;">Verifikasi email Anda untuk mengaktifkan akun.</span>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f4f5f7;">
      <tr>
        <td align="center" style="padding:24px;">

          <!-- Container fix width 600px -->
          <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" class="container" style="width:600px; max-width:600px;">
            <tr>
              <td style="padding:0;">

                <!-- Card -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#ffffff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; box-shadow:0 1px 2px rgba(0,0,0,0.06), 0 4px 12px rgba(0,0,0,0.08);">
                  <tr>
                    <td class="inner-padding" style="padding:32px;">

                      <!-- Heading -->
                      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                          <td class="h1" style="font-family:Arial,Helvetica,sans-serif; font-size:24px; line-height:32px; font-weight:700; color:#111827; padding-bottom:8px; mso-line-height-rule:exactly;">
                            Verifikasi Email Anda
                          </td>
                        </tr>
                        <tr>
                          <td style="font-family:Arial,Helvetica,sans-serif; font-size:16px; line-height:24px; color:#374151; padding-bottom:16px; mso-line-height-rule:exactly;">
                            Halo {{ $user->name }},
                          </td>
                        </tr>
                        <tr>
                          <td style="font-family:Arial,Helvetica,sans-serif; font-size:16px; line-height:24px; color:#374151; padding-bottom:24px; mso-line-height-rule:exactly;">
                            Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda dan mengaktifkan akun.
                          </td>
                        </tr>

                        <!-- Button -->
                        <tr>
                          <td align="center" style="padding-bottom:24px;">
                            <a href="{{ $verificationUrl }}" target="_blank" rel="noopener" style="background-color:#2563eb; color:#ffffff; display:inline-block; font-family:Arial,Helvetica,sans-serif; font-size:16px; font-weight:700; line-height:44px; text-align:center; text-decoration:none; width:220px; border-radius:6px;">Verifikasi Email</a>
                          </td>
                        </tr>

                        <!-- Info text -->
                        <tr>
                          <td style="font-family:Arial,Helvetica,sans-serif; font-size:13px; line-height:20px; color:#6b7280; padding-bottom:16px; mso-line-height-rule:exactly;">
                            Link verifikasi berlaku selama 60 menit.
                          </td>
                        </tr>
                        <tr>
                          <td style="font-family:Arial,Helvetica,sans-serif; font-size:13px; line-height:20px; color:#6b7280; padding-bottom:0; mso-line-height-rule:exactly;">
                            Jika tombol tidak berfungsi, salin dan tempel URL berikut ke peramban Anda:
                            <br>
                            <a href="{{ $verificationUrl }}" style="color:#2563eb; text-decoration:underline; word-break:break-all;">{{ $verificationUrl }}</a>
                          </td>
                        </tr>
                      </table>

                    </td>
                  </tr>
                </table>
                <!-- /Card -->

                <!-- Footer -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top:12px;">
                  <tr>
                    <td style="font-family:Arial,Helvetica,sans-serif; font-size:12px; line-height:18px; color:#9ca3af; text-align:center; mso-line-height-rule:exactly;">
                      Jika Anda tidak meminta verifikasi ini, Anda dapat mengabaikan email ini.
                    </td>
                  </tr>
                </table>
                <!-- /Footer -->

              </td>
            </tr>
          </table>
          <!-- /Container -->

        </td>
      </tr>
    </table>
  </body>
</html>
