<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $proposal->proposal_number }} - {{ $proposal->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #111827;
        }
        @media print {
            @page {
                margin: 0;
            }
            body {
                background-color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 40px !important;
                border: none !important;
            }
        }
    </style>
</head>
<body class="antialiased min-h-screen">
    
    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
        }
        .text-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .text-secondary { color: var(--secondary-color) !important; }
        .border-primary { border-color: var(--primary-color) !important; }
    </style>
    
    <!-- Action Bar (Screen Only) -->
    <div class="max-w-[210mm] mx-auto mt-8 mb-6 flex justify-between items-center px-4 md:px-0 no-print">
        <a href="{{ route('proposals.show', $proposal) }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors font-medium text-sm">
            <i class='bx bx-left-arrow-alt text-xl'></i>
            <span>Geri Dön</span>
        </a>
        <div class="flex gap-3">
            <button onclick="window.print()" class="h-10 px-4 rounded-lg bg-white border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm">
                <i class='bx bx-printer'></i> Yazdır
            </button>
            <button onclick="window.print()" class="h-10 px-4 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition-all flex items-center gap-2 shadow-md">
                <i class='bx bxs-file-pdf'></i> PDF İndir
            </button>
        </div>
    </div>

    <!-- Document Container -->
    <div class="max-w-[210mm] mx-auto bg-white shadow-lg print-container min-h-[297mm] relative flex flex-col">
        
        @foreach($layout as $block)
            @continue(isset($block['visible']) && !$block['visible'])

            @switch($block['id'])
                
                {{-- HEADER BLOCK --}}
                @case('header')
                    <div class="p-12 pb-8">
                        <div class="flex justify-between items-start">
                            <div class="w-1/2">
                                @php
                                    $logoSettings = \App\Models\Setting::whereIn('key', ['proposal_logo', 'company_logo_png', 'company_logo_jpg'])->get()->keyBy('key');
                                    $logoPath = $logoSettings['proposal_logo']->value 
                                        ?? $logoSettings['company_logo_png']->value 
                                        ?? $logoSettings['company_logo_jpg']->value 
                                        ?? null;
                                @endphp

                                @if($logoPath)
                                    <img src="{{ asset('storage/'.$logoPath) }}" class="h-16 w-auto object-contain mb-6" alt="Logo">
                                @else
                                    <div class="text-2xl font-bold tracking-tight text-primary mb-6 flex items-center gap-2">
                                         <div class="w-8 h-8 bg-primary text-white rounded flex items-center justify-center">
                                            <span class="font-bold text-lg cursor-default select-none">{{ substr(config('app.name'), 0, 1) }}</span>
                                         </div>
                                         {{ config('app.name', 'fiyera.co') }}
                                    </div>
                                @endif

                                <div class="text-sm text-secondary leading-relaxed">
                                    <p class="font-semibold text-primary">{{ $proposal->user?->tenant?->name ?? (auth()->user()?->tenant?->name ?? config('app.name')) }}</p>
                                    <p>{{ $proposal->user?->email ?? (auth()->user()?->email ?? '-') }}</p>
                                </div>
                            </div>

                            <div class="w-1/2 text-right">
                                <h1 class="text-3xl font-light text-primary tracking-tight mb-2">TEKLİF</h1>
                                <p class="text-secondary font-medium text-sm mb-6">#{{ $proposal->proposal_number }}</p>
                                
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-end gap-8">
                                        <span class="text-secondary w-24">Tarih:</span>
                                        <span class="text-primary font-medium">{{ $proposal->proposal_date->format('d.m.Y') }}</span>
                                    </div>
                                    <div class="flex justify-end gap-8">
                                        <span class="text-secondary w-24">Geçerlilik:</span>
                                        <span class="text-primary font-medium">{{ $proposal->valid_until ? $proposal->valid_until->format('d.m.Y') : 'Süresiz' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break

                {{-- SEPARATOR BLOCK --}}
                @case('separator_1')
                    <div class="px-12">
                        <div class="w-full border-b border-gray-100 mb-8"></div>
                    </div>
                    @break

                {{-- RECIPIENT BLOCK --}}
                @case('recipient')
                    <div class="px-12 mb-12">
                        <h3 class="text-secondary text-xs font-semibold uppercase tracking-wider mb-2">SAYIN</h3>
                        <div class="text-primary">
                            <p class="text-xl font-semibold mb-1">{{ $proposal->customer->company_name }}</p>
                            <div class="text-sm text-secondary space-y-0.5">
                                @if($proposal->customer->contact_person)<p>{{ $proposal->customer->contact_person }}</p>@endif
                                @if($proposal->customer->company_email)<p>{{ $proposal->customer->company_email }}</p>@endif
                                @if($proposal->customer->mobile_phone)<p>{{ $proposal->customer->mobile_phone }}</p>@endif
                                @if($proposal->customer->address)<p class="max-w-xs">{{ $proposal->customer->address }}</p>@endif
                            </div>
                        </div>
                    </div>
                    @break

                {{-- ITEMS TABLE BLOCK --}}
                @case('items')
                    <div class="px-12 flex-grow">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b-2 border-gray-100">
                                    <th class="py-3 text-xs font-semibold text-secondary uppercase tracking-wider w-1/2">Hizmet / Ürün</th>
                                    <th class="py-3 text-xs font-semibold text-secondary uppercase tracking-wider text-center">Miktar</th>
                                    <th class="py-3 text-xs font-semibold text-secondary uppercase tracking-wider text-right">Birim Fiyat</th>
                                    <th class="py-3 text-xs font-semibold text-secondary uppercase tracking-wider text-right">Toplam</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($proposal->items as $item)
                                <tr>
                                    <td class="py-4 pr-4 align-top">
                                        <p class="text-primary font-medium text-sm">{{ $item->description }}</p>
                                        @if($item->discount_amount > 0)
                                            <span class="text-[10px] text-green-600 bg-green-50 px-1.5 py-0.5 rounded mt-1 inline-block">İndirimli</span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-center align-top text-gray-600 text-sm">{{ number_format($item->quantity, 0) }} {{ $item->unit }}</td>
                                    <td class="py-4 text-right align-top text-gray-600 text-sm whitespace-nowrap">{{ number_format($item->unit_price, 2) }} {{ $proposal->currency }}</td>
                                    <td class="py-4 text-right align-top text-primary font-medium text-sm whitespace-nowrap">{{ number_format($item->total_price, 2) }} {{ $proposal->currency }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @break

                {{-- SUMMARY BLOCK --}}
                @case('summary')
                    <div class="px-12 mt-8 mb-12">
                        <div class="flex justify-end">
                            <div class="w-64 space-y-3">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-secondary">Ara Toplam</span>
                                    <span class="text-primary font-medium">{{ number_format($proposal->subtotal, 2) }} {{ $proposal->currency }}</span>
                                </div>
                                
                                @if($proposal->discount_amount > 0)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-green-600">İndirim</span>
                                    <span class="text-green-600 font-medium">-{{ number_format($proposal->discount_amount, 2) }} {{ $proposal->currency }}</span>
                                </div>
                                @endif

                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-secondary">KDV (Toplam)</span>
                                    <span class="text-primary font-medium">{{ number_format($proposal->tax_amount, 2) }} {{ $proposal->currency }}</span>
                                </div>

                                <div class="border-t border-gray-200 pt-3 mt-3">
                                    <div class="flex justify-between items-baseline">
                                        <span class="text-primary font-semibold">Genel Toplam</span>
                                        <span class="text-2xl font-bold text-primary tracking-tight">{{ number_format($proposal->total_amount, 2) }} {{ $proposal->currency }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break

                {{-- NOTES BLOCK --}}
                @case('notes')
                    @if($proposal->notes)
                    <div class="px-12 mb-12">
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-100">
                            <h4 class="text-xs font-semibold text-secondary uppercase tracking-wider mb-2">Notlar</h4>
                            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $proposal->notes }}</p>
                        </div>
                    </div>
                    @endif
                    @break

                {{-- FOOTER BLOCK --}}
                @case('footer')
                    <div class="mt-auto px-12 py-8 bg-gray-50 border-t border-gray-100 text-center print:bg-white print:border-t-0">
                        <div class="flex flex-col gap-1 justify-center items-center">
                            <p class="text-xs text-gray-400 uppercase tracking-widest font-medium">Bu belge dijital olarak oluşturulmuştur</p>
                            <div class="w-8 h-px bg-gray-200 my-2"></div>
                            <p class="text-[10px] text-gray-300 font-semibold tracking-wide">Bu teklif fiyera.co sistemi tarafından hazırlanmıştır</p>
                        </div>
                    </div>
                    @break

            @endswitch
        @endforeach
    </div>
</body>
</html>
