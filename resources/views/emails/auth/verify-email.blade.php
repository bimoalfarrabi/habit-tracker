<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email Ritme</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap');
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f4efe7;font-family:Arial,Helvetica,sans-serif;color:#2f2b26;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background:#fffaf4;border:1px solid #e6dfd2;border-radius:14px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#c96442 0%,#b7522e 100%);padding:28px 28px 22px;">
                            <p style="margin:0;color:#f7efe7;font-size:30px;line-height:1;font-family:'Manrope',Arial,Helvetica,sans-serif;letter-spacing:0.4px;font-weight:800;">
                                Ritme
                            </p>
                            <h1 style="margin:10px 0 0;color:#fff8f2;font-size:28px;line-height:1.2;font-weight:700;">
                                Verifikasi Email Kamu
                            </h1>
                            <p style="margin:10px 0 0;color:#fde9df;font-size:14px;line-height:1.6;">
                                Satu langkah lagi untuk mengaktifkan semua fitur Habit Tracker kamu.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:26px 28px 6px;">
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.8;color:#4a433a;">
                                Halo {{ $userName }},
                            </p>
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.8;color:#4a433a;">
                                Klik tombol di bawah untuk memverifikasi email akun kamu dan mulai membangun ritme harian dengan fitur Habit, Todo, Focus Session, dan Reminder.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:10px 28px 14px;">
                            <a
                                href="{{ $verificationUrl }}"
                                style="display:inline-block;padding:12px 24px;background:#c96442;color:#fffaf4;text-decoration:none;border-radius:10px;font-size:14px;font-weight:700;"
                            >
                                Verifikasi Email Sekarang
                            </a>
                            <p style="margin:14px 0 0;font-size:12px;color:#7a7062;">
                                Link ini berlaku selama {{ $expireMinutes }} menit, hingga <strong>{{ $expireAtFormatted }}</strong>.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:10px 28px 6px;">
                            <p style="margin:0 0 10px;font-size:13px;line-height:1.7;color:#6f6659;">
                                Jika tombol tidak bisa diklik, salin dan buka link berikut di browser:
                            </p>
                            <p style="margin:0 0 14px;padding:10px 12px;border:1px dashed #decfb8;background:#fff6eb;border-radius:8px;word-break:break-all;font-size:12px;line-height:1.6;color:#6d442f;">
                                {{ $verificationUrl }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:4px 28px 24px;">
                            <p style="margin:0;font-size:12px;line-height:1.8;color:#8a7f71;">
                                Jika kamu tidak merasa membuat akun di Ritme, abaikan email ini.
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="margin:14px 0 0;font-size:11px;color:#9a8f82;">
                    © {{ now()->year }} Ritme. Build your rhythm, one day at a time.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
