<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body style="margin:0; padding:0; font-family: 'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif; background:#f0f2f5;">

    <br>
    <!-- Container utama -->
    <div style="max-width:560px; margin:40px auto; background:#ffffff; border-radius:24px; box-shadow:0 12px 30px rgba(0,0,0,0.08); overflow:hidden;">
        
        <!-- Header dengan aksen gradien tipis -->
        <div style="padding:32px 32px 0 32px; text-align:center;">
            <!-- Ikon sederhana (emoji) sebagai aksen visual -->
            <div style="font-size:48px; margin-bottom:12px;">🔐</div>
            <h1 style="margin:0 0 8px 0; font-size:28px; font-weight:600; color:#1a2c3e; letter-spacing:-0.3px;">Reset Password</h1>
            <div style="width:50px; height:3px; background:linear-gradient(90deg, #0066cc, #004c99); margin:12px auto 20px auto; border-radius:2px;"></div>
        </div>
        
        <!-- Konten utama -->
        <div style="padding:0 32px 32px 32px;">
            <p style="font-size:16px; line-height:1.5; color:#4a5568; margin:0 0 20px 0;">
                Kamu menerima email ini karena ada permintaan untuk mengatur ulang password akunmu.
            </p>
            
            <p style="font-size:16px; line-height:1.5; color:#4a5568; margin:0 0 24px 0;">
                Klik tombol di bawah ini untuk melanjutkan proses reset password:
            </p>
            
            <!-- Tombol utama dengan efek hover (inline style tidak bisa hover, tapi tetap rapi) -->
            <div style="text-align:center; margin:28px 0 24px 0;">
                <a href="{{ $link }}" 
                   style="display:inline-block; padding:14px 32px; font-size:16px; font-weight:600; color:#ffffff; text-decoration:none; border-radius:40px; background: linear-gradient(135deg, #0066cc 0%, #004c99 100%); box-shadow:0 4px 12px rgba(0,102,204,0.25); transition:all 0.2s;">
                   Reset Password
                </a>
            </div>
            
            <!-- Informasi tambahan dengan box ringan -->
            <div style="background:#f8fafc; border-radius:16px; padding:16px; margin:28px 0 20px 0; text-align:center;">
                <p style="font-size:14px; color:#64748b; margin:0 0 6px 0;">
                    ⏱️ Link ini akan kadaluarsa dalam <strong style="color:#1e293b;">60 menit</strong>.
                </p>
                <p style="font-size:14px; color:#64748b; margin:0;">
                    🔒 Jika kamu tidak meminta reset password, abaikan email ini.
                </p>
            </div>
            
        </div>
    </div>
</body>
</html>