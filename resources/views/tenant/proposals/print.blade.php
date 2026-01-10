<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $proposal->proposal_number }} - {{ $proposal->title }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Base Reset & Fonts */
        * { box-sizing: border-box; }
        body { 
            margin: 0; 
            padding: 0; 
            font-family: 'Inter', sans-serif; 
            font-size: 11px;
            color: #1f2937; 
            line-height: 1.4;
            background-color: white;
        }

        /* mPDF specific Layout */
        @page {
            margin: 15mm;
        }
        footer {
            position: fixed; 
            bottom: -40px; 
            left: 0px; 
            right: 0px;
            height: 50px; 
            text-align: center;
            color: #9ca3af;
            font-size: 9px;
            line-height: 1.4;
        }

        /* Container - simplified for mPDF */
        .page-container {
            width: 100%;
            padding: 0;
            background: white;
        }

        /* Typography Helper Classes */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-sm { font-size: 10px; }
        .text-xs { font-size: 9px; }
        .text-lg { font-size: 14px; }
        .text-xl { font-size: 18px; font-weight: 800; }
        .uppercase { text-transform: uppercase; }
        .text-gray-500 { color: #6b7280; }
        .text-gray-400 { color: #9ca3af; }
        .text-indigo-600 { color: #4f46e5; }
        .text-red-500 { color: #ef4444; }

        /* Layout Tables */
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        
        td { vertical-align: top; }
        
        .section-mb { margin-bottom: 25px; }
        .divider { border-bottom: 1px solid #e5e7eb; margin: 20px 0; }

        /* Component Styles */
        .logo-img { max-height: 60px; width: auto; }
        
        /* Clearfix */
        .clearfix::after { content: ""; clear: both; display: table; }
        
        .avoid-break { page-break-inside: avoid; }
        
        /* Hide screen-only items */
        .no-print { display: none; }

        .info-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
            margin-bottom: 5px;
            display: block;
        }

        .info-value-lg {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .meta-box {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
        }

        .meta-table td { padding: 0 10px; border-right: 1px solid #e5e7eb; }
        .meta-table td:last-child { border-right: none; }
        .meta-table td:first-child { padding-left: 0; }

        /* Items Table - Compact */
        .items-table { margin-bottom: 30px; }
        .items-table th {
            text-align: left;
            padding: 8px 10px;
            background-color: #f9fafb;
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
        
        .items-table td {
            padding: 10px 10px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }
        .items-table tr:last-child td { border-bottom: none; }

        /* Summary */
        .summary-row td { padding: 5px 0; }
        .summary-label { font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; }
        .summary-value { font-size: 12px; font-weight: 700; color: #111827; text-align: right; }
        
        .total-row td { padding-top: 20px; padding-bottom: 5px; }
        .total-label { color: #4f46e5; font-size: 12px; font-weight: 800; text-transform: uppercase; }
        .total-sub { font-size: 10px; color: #9ca3af; font-weight: 600; text-transform: uppercase; }
        .total-value { font-size: 20px; font-weight: 800; color: #111827; text-align: right; }
    </style>
    
    <!-- Action Bar Utils (Tailwind - Screen Only) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="{{ (isset($isPdf) && $isPdf) ? '' : 'antialiased min-h-screen' }}">
    <!-- DomPDF Footer -->
    @if(isset($isPdf) && $isPdf)
    <footer>
        Bu teklif {{ $proposal->user?->tenant?->name ?? config('app.name') }} tarafından oluşturulmuştur.
    </footer>
    @endif

    
    <!-- Action Bar (Screen Only) -->
    @if(!isset($isPdf) || !$isPdf)
    <div class="max-w-[210mm] mx-auto mt-8 mb-6 flex justify-between items-center px-4 md:px-0 no-print">
        <a href="{{ route('proposals.show', $proposal) }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors font-medium text-sm">
            <span>&larr; Geri Dön</span>
        </a>
        <div class="flex gap-3">
            <button onclick="window.print()" class="h-10 px-4 rounded-lg bg-white border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm">
                Yazdır
            </button>
            <a href="{{ route('proposals.pdf', $proposal) }}" class="h-10 px-4 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition-all flex items-center gap-2 shadow-md">
                PDF İndir
            </a>
        </div>
    </div>
    @endif

    <!-- Document Container -->
    <div class="{{ (isset($isPdf) && $isPdf) ? 'main-container' : 'max-w-[210mm] mx-auto bg-white shadow-lg print-container min-h-[297mm] relative flex flex-col p-12' }}">
        
        <!-- Header -->
        <table class="w-full mb-16">
            <tr>
                <td class="align-top">
                    @php
                        $logoSettings = \App\Models\Setting::whereIn('key', ['proposal_logo', 'company_logo_png', 'company_logo_jpg'])->get()->keyBy('key');
                        $logoPath = $logoSettings['proposal_logo']->value 
                            ?? $logoSettings['company_logo_png']->value 
                            ?? $logoSettings['company_logo_jpg']->value 
                            ?? null;
                    @endphp

                    @if($logoPath)
                        <img src="{{ isset($isPdf) && $isPdf ? public_path('uploads/'.$logoPath) : asset('uploads/'.$logoPath) }}" class="logo-img" alt="Logo">
                    @else
                        <h1 style="font-size: 24px; color: #111827;">{{ config('app.name', 'Fiyera') }}</h1>
                    @endif
                </td>
                <td class="align-top text-right">
                    <div class="text-3xl" style="color: #111827; letter-spacing: -0.5px; opacity: 0.4; font-weight: 700;">TEKLİFTİR</div>
                    <div style="font-weight: 700; font-size: 14px; margin-top: 5px; color: #111827;">#{{ $proposal->proposal_number }}</div>
                    <div style="color: #9ca3af; font-size: 10px; font-weight: 600; text-transform: uppercase;">
                        {{ \Carbon\Carbon::parse($proposal->proposal_date)->format('d.m.Y') }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Recipient Info -->
        <table class="section-mb">
            <tr>
                <td style="width: 50%; padding-right: 20px;">
                    <span class="info-label">DÜZENLEYEN</span>
                    <div class="info-value-lg">{{ $proposal->user?->tenant?->name ?? (auth()->user()?->tenant?->name ?? config('app.name')) }}</div>
                    <div style="color: #6b7280; font-size: 12px; margin-top: 4px;">{{ $proposal->user?->name }}</div>
                    <div style="color: #6b7280; font-size: 12px;">{{ $proposal->user?->email }}</div>
                </td>
                <td style="width: 50%; padding-left: 20px;">
                    <span class="info-label">SAYIN / FİRMA</span>
                    <div class="info-value-lg">{{ $proposal->customer->company_name }}</div>
                    <div style="color: #6b7280; font-size: 12px; margin-top: 4px;">{{ $proposal->customer->contact_person }}</div>
                    <div style="color: #6b7280; font-size: 12px;">{{ $proposal->customer->company_email }}</div>
                    @if($proposal->customer->address)
                        <div style="color: #9ca3af; font-size: 11px; margin-top: 8px;">{{ $proposal->customer->address }}</div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Meta Grid -->
        <div class="meta-box">
            <table class="meta-table">
                <tr>
                    <td style="width: 25%;">
                        <span class="info-label">TEKLİF TARİHİ</span>
                        <div style="font-weight: 700; color: #111827;">{{ \Carbon\Carbon::parse($proposal->proposal_date)->format('d.m.Y') }}</div>
                    </td>
                    <td style="width: 25%;">
                        <span class="info-label">GEÇERLİLİK</span>
                        <div style="font-weight: 700; color: #111827;">{{ $proposal->valid_until ? \Carbon\Carbon::parse($proposal->valid_until)->format('d.m.Y') : '-' }}</div>
                    </td>
                    <td style="width: 25%;">
                        <span class="info-label">TESLİM TARİHİ</span>
                        <div style="font-weight: 700; color: #111827;">{{ $proposal->delivery_date ? \Carbon\Carbon::parse($proposal->delivery_date)->format('d.m.Y') : '-' }}</div>
                    </td>
                    <td style="width: 25%;">
                        <span class="info-label">ÖDEME</span>
                        <div style="font-weight: 700; color: #111827;">{{ $proposal->payment_type ?? '-' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 45%;">HİZMET / ÜRÜN AÇIKLAMASI</th>
                    <th style="width: 10%; text-align: center;">MİKTAR</th>
                    <th style="width: 20%; text-align: right;">BİRİM FİYAT</th>
                    <th style="width: 10%; text-align: center;">VERGİ</th>
                    <th style="width: 15%; text-align: right;">TOPLAM</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proposal->items as $item)
                <tr>
                    <td>
                        <div style="font-weight: 700; font-size: 12px; color: #111827;">{{ $item->product_name ?? 'Hizmet' }}</div>
                        <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">{{ $item->description }}</div>
                    </td>
                    <td style="text-align: center;">
                        <span style="font-weight: 700;">{{ number_format($item->quantity, 0) }}</span>
                        <span style="font-size: 9px; color: #9ca3af; display: block;">{{ $item->unit }}</span>
                    </td>
                    <td style="text-align: right;">
                        <span style="font-weight: 700;">{{ number_format($item->unit_price, 2) }} {{ $proposal->currency }}</span>
                        @if($item->discount_amount > 0)
                            <div style="font-size: 9px; color: #ef4444; margin-top: 2px;">-{{ number_format($item->discount_amount, 2) }} İnd.</div>
                        @endif
                    </td>
                    <td style="text-align: center; color: #9ca3af; font-size: 11px;">
                        %{{ $item->tax_rate }}
                    </td>
                    <td style="text-align: right; font-weight: 700; color: #111827;">
                        {{ number_format($item->total_price, 2) }} {{ $proposal->currency }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary & Notes (Grouped to avoid break) -->
        <div class="avoid-break">
            <table class="section-mb" style="margin-top: 40px;">
                <tr>
                    <!-- Notes (Left) -->
                    <td style="width: 60%; padding-right: 40px; vertical-align: top;">
                        @if($proposal->notes)
                            <div>
                                <span class="info-label">NOTLAR</span>
                                <div style="background-color: #f9fafb; border-radius: 8px; padding: 15px; font-size: 11px; color: #4b5563; line-height: 1.6; margin-top: 5px;">
                                    {!! nl2br(e($proposal->notes)) !!}
                                </div>
                            </div>
                        @endif
                    </td>

                    <!-- Totals (Right) -->
                    <td style="width: 40%; vertical-align: top;">
                        <table style="width: 100%;">
                            <tr class="summary-row">
                                <td class="summary-label">ARA TOPLAM</td>
                                <td class="summary-value">{{ number_format($proposal->subtotal, 2) }} {{ $proposal->currency }}</td>
                            </tr>
                            
                            @if($proposal->discount_amount > 0)
                            <tr class="summary-row">
                                <td class="summary-label" style="color: #ef4444;">İNDİRİM</td>
                                <td class="summary-value" style="color: #ef4444;">-{{ number_format($proposal->discount_amount, 2) }} {{ $proposal->currency }}</td>
                            </tr>
                            @endif

                            <tr class="summary-row">
                                <td class="summary-label">TOPLAM KDV</td>
                                <td class="summary-value">{{ number_format($proposal->tax_amount, 2) }} {{ $proposal->currency }}</td>
                            </tr>

                            <tr class="summary-row">
                                <td colspan="2">
                                    <div style="border-bottom: 2px dashed #e5e7eb; margin: 15px 0;"></div>
                                </td>
                            </tr>

                            <tr class="total-row">
                                <td style="vertical-align: bottom;">
                                    <div class="total-label">GENEL TOPLAM</div>
                                    <div class="total-sub">KDV DAHİL</div>
                                </td>
                                <td style="vertical-align: bottom;">
                                    <div class="total-value">{{ number_format($proposal->total_amount, 2) }} {{ $proposal->currency }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

    </div>

   
    <!-- DomPDF Page Counter Script -->
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Sayfa {PAGE_NUM} / {PAGE_COUNT}";
            $size = 9;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 25;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.61, 0.64, 0.69));
        }
    </script>
</body>
</html>

