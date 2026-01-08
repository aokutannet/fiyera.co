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
        .btn { background-color: #020617; color: white; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block; margin-top: 20px; }
        .icon-circle { width: 64px; height: 64px; background-color: #f0fdf4; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
        .icon { font-size: 32px; color: #16a34a; }
        .details-box { background-color: #f8fafc; border-radius: 12px; padding: 20px; text-align: left; margin-bottom: 24px; border: 1px solid #e2e8f0; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; font-weight: 500; font-size: 14px; }
        .detail-value { color: #0f172a; font-weight: 600; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                 <span class="logo-text">fiyera<span class="logo-dot">.co</span></span>
            </div>
            
            <div class="icon-circle">
                <span class="icon">✅</span>
            </div>
            
            <h1 class="h1">Aboneliğiniz Başarıyla Başlatıldı!</h1>
            
            <p class="p">
                Merhaba {{ $user->name }},<br><br>
                Ödemeniz başarıyla alındı ve abonelik paketiniz hesabınıza tanımlandı. Artık Fiyera.co'nun tüm özelliklerini kesintisiz kullanabilirsiniz.
            </p>

            <div class="details-box">
                <div class="detail-row">
                    <span class="detail-label">Paket Adı</span>
                    <span class="detail-value">{{ $subscription->plan->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Periyot</span>
                    <span class="detail-value">{{ $subscription->billing_period == 'yearly' ? 'Yıllık' : 'Aylık' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Başlangıç Tarihi</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($subscription->starts_at)->format('d.m.Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Bitiş Tarihi</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($subscription->ends_at)->format('d.m.Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tutar</span>
                    <span class="detail-value">{{ number_format($subscription->price, 2, ',', '.') }} ₺</span>
                </div>
            </div>
            
            <a href="{{ route('subscription.index') }}" class="btn">Abonelik Detaylarını Görüntüle</a>
            
            <p class="p" style="font-size: 12px; margin-top: 32px; margin-bottom: 0;">Bu işlemle ilgili faturalarınıza panelinizden ulaşabilirsiniz.</p>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} Fiyera.co. Tüm hakları saklıdır.
        </div>
    </div>
</body>
</html>
