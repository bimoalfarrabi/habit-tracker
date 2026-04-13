<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Todo - Ritme</title>
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
                        <td style="background:linear-gradient(135deg,#c96442 0%,#b7522e 100%);padding:26px 28px;">
                            <p style="margin:0;color:#f7efe7;font-size:30px;line-height:1;font-family:'Manrope',Arial,Helvetica,sans-serif;letter-spacing:0.4px;font-weight:800;">
                                Ritme
                            </p>
                            <p style="margin:12px 0 0;color:#fde9df;font-size:14px;line-height:1.7;">
                                Reminder Todo
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px 28px 10px;">
                            <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#4a433a;">
                                Halo {{ $userName }},
                            </p>
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.8;color:#4a433a;">
                                Ada todo yang dijadwalkan untuk kamu:
                            </p>

                            <p style="margin:0 0 10px;padding:12px 14px;border:1px solid #e8ded0;background:#fff6eb;border-radius:10px;font-size:16px;font-weight:700;color:#6d442f;">
                                {{ $todoTitle }}
                            </p>

                            <p style="margin:0 0 6px;font-size:12px;line-height:1.7;color:#7a7062;">
                                Prioritas: {{ ucfirst($todoPriority) }}
                            </p>

                            @if ($todoDueDate)
                                <p style="margin:0 0 6px;font-size:12px;line-height:1.7;color:#7a7062;">
                                    Due date: {{ $todoDueDate }}
                                </p>
                            @endif

                            <p style="margin:0 0 12px;font-size:12px;line-height:1.7;color:#7a7062;">
                                Dijadwalkan pada {{ $scheduledAtLabel }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 28px 24px;">
                            <p style="margin:0;font-size:12px;line-height:1.8;color:#8a7f71;">
                                Selesaikan sedikit demi sedikit, ritme akan terbentuk.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
