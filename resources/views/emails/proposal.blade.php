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
        .logo-text { font-size: 24px; font-weight: 800; color: #020617; letter-spacing: -0.025em; margin-left: 10px; }
        .logo-dot { color: #4f46e5; }
        .h1 { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 16px; }
        .p { color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 24px; }
        .footer { margin-top: 32px; color: #94a3b8; font-size: 12px; text-align: center; }
        .icon-circle { width: 64px; height: 64px; background-color: #f0fdf4; border-radius: 50%; margin: 0 auto 24px; text-align: center; line-height: 64px; display: block; }
        .icon { font-size: 32px; vertical-align: middle; line-height: 32px; }
        .highlight { color: #0f172a; font-weight: 600; }
        .proposal-card { background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 16px; margin: 0 0 24px; }
        .proposal-number { font-family: monospace; font-weight: 700; color: #4f46e5; font-size: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <span class="logo-text">fiyera<span class="logo-dot">.co</span></span>
            </div>
            
            <div class="icon-circle">
                <span class="icon">📄</span>
            </div>
            
            <h1 class="h1">Size Özel Bir Teklif Var!</h1>
            
            <p class="p">
                Sayın <span class="highlight">{{ $proposal->customer->contact_person }}</span>,
            </p>

            <p class="p">
                <span class="highlight">{{ $proposal->user?->tenant?->name ?? config('app.name') }}</span> tarafından sizin için hazırlanan teklif detayları aşağıdadır:
            </p>
            
            <div class="proposal-card">
                <div class="p" style="margin-bottom: 4px; font-size: 12px;">Teklif Numarası</div>
                <div class="proposal-number">{{ $proposal->proposal_number }}</div>
            </div>

            <p class="p">
                Teklifin detaylı PDF dosyası bu e-postanın ekinde yer almaktadır. Dosyayı indirerek tüm detayları inceleyebilirsiniz.
            </p>

            @if($proposal->public_token)
            <div style="margin: 32px 0;">
                <a href="{{ route('proposals.public.show', $proposal->public_token) }}" style="background-color: #4f46e5; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-weight: 600; display: inline-block; font-size: 16px; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);">
                    Teklifi Online Görüntüle ve Onayla
                </a>
                <p class="p" style="font-size: 12px; margin-top: 12px; color: #94a3b8;">
                    veya aşağıdaki bağlantıya tıklayın:<br>
                    <a href="{{ route('proposals.public.show', $proposal->public_token) }}" style="color: #4f46e5; text-decoration: none;">{{ route('proposals.public.show', $proposal->public_token) }}</a>
                </p>
            </div>
            @endif

            <p class="p" style="font-size: 12px; margin-top: 32px; margin-bottom: 0;">
                Herhangi bir sorunuz olursa, bizimle iletişime geçmekten çekinmeyin.
            </p>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} {{ $proposal->user?->tenant?->name ?? config('app.name') }}. Tüm hakları saklıdır.<br>
            <span style="opacity: 0.7; font-size: 11px;">Bu mail ve teklif sistemi Fiyera.co tarafından hazırlanmıştır.</span>
        </div>
    </div>
</body>
</html>
