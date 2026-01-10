<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fafafa; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background-color: #ffffff; border-radius: 16px; padding: 40px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border: 1px solid #f1f5f9; text-align: center; }
        .logo { margin-bottom: 24px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
        .logo-icon { width: 40px; height: 40px; background-color: #020617; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; }
        .logo-text { font-size: 24px; font-weight: 800; color: #020617; letter-spacing: -0.025em; margin-left: 10px; }
        .logo-dot { color: #4f46e5; }
        .h1 { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 16px; }
        .p { color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 24px; }
        .code-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
        .code { font-size: 32px; font-weight: 800; letter-spacing: 4px; color: #4f46e5; margin: 0; }
        .footer { margin-top: 32px; color: #94a3b8; font-size: 12px; text-align: center; }
        .btn { background-color: #020617; color: white; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                 <span class="logo-text">fiyera<span class="logo-dot">.co</span></span>
            </div>
            
            <h1 class="h1">Doğrulama Kodunuz</h1>
            
            @if(isset($isMobile) && $isMobile)
                <p class="p"><strong>Mobil uygulamasından</strong> hesabınıza güvenli bir şekilde giriş yapmak için aşağıdaki kodu kullanın. Bu kod 10 dakika süreyle geçerlidir.</p>
            @else
                <p class="p">Hesabınıza güvenli bir şekilde giriş yapmak için aşağıdaki kodu kullanın. Bu kod 10 dakika süreyle geçerlidir.</p>
            @endif
            
            <div class="code-box">
                <div class="code">{{ $code }}</div>
            </div>
            
            <p class="p" style="font-size: 12px; margin-bottom: 0;">Eğer bu işlemi siz yapmadıysanız, lütfen bu e-postayı dikkate almayın.</p>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} Fiyera.co. Tüm hakları saklıdır.
        </div>
    </div>
</body>
</html>
