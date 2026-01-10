<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $proposal->title }} - {{ $proposal->proposal_number }}</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
     <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50/50 font-sans antialiased text-slate-600">
    <div class="min-h-screen p-4 md:p-8 animate-in fade-in zoom-in-95 duration-500">
        <div class="max-w-7xl mx-auto space-y-8">
            
            <!-- Alert Messages -->
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl font-bold flex items-center gap-3 shadow-emerald-500/5 shadow-xl">
                <i class='bx bxs-check-circle text-2xl'></i>
                {{ session('success') }}
            </div>
            @endif

            @if($proposal->status == 'approved')
            <div class="bg-emerald-600 text-white px-8 py-6 rounded-md font-bold flex flex-col md:flex-row items-center justify-between gap-4 shadow-xl shadow-emerald-600/20">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class='bx bx-check-double text-3xl'></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Bu teklif onaylanmıştır</h3>
                        <p class="text-emerald-100 font-medium">Teşekkür ederiz, en kısa sürede sizinle iletişime geçilecektir.</p>
                    </div>
                </div>
            </div>
            @elseif($proposal->status == 'rejected')
            <div class="bg-rose-500 text-white px-8 py-6 rounded-md font-bold flex flex-col md:flex-row items-center justify-between gap-4 shadow-xl shadow-rose-500/20">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class='bx bx-x text-3xl'></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black">Bu teklif reddedilmiştir</h3>
                        <p class="text-rose-100 font-medium">Geri bildiriminiz için teşekkür ederiz.</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Sol Kolon: Teklif Detayı -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white rounded-md p-10 md:p-16 border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-start mb-12">
                            <div>
                                <h2 class="text-3xl font-black text-slate-950 mb-2">TEKLİF</h2>
                                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs flex items-center gap-1">
                                    {{ $proposal->proposal_number }}
                                    @if($proposal->source === 'mobile')
                                        <i class='bx bx-mobile-alt text-indigo-500 text-sm' title="Mobil Uygulama ile Oluşturuldu"></i>
                                    @else
                                        <i class='bx bx-laptop text-slate-300 text-sm' title="Web Paneli ile Oluşturuldu"></i>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                @php
                                    // Logo logic repeated because $settings passed from controller
                                    $logoPath = $settings['proposal_logo']->value 
                                        ?? $settings['company_logo_png']->value 
                                        ?? $settings['company_logo_jpg']->value 
                                        ?? null;
                                @endphp

                                @if($logoPath)
                                    <img src="{{ asset('uploads/'.$logoPath) }}" class="h-12 w-auto ml-auto object-contain" alt="Logo">
                                @else
                                    <div class="w-16 h-16 bg-slate-950 rounded-2xl flex items-center justify-center ml-auto shadow-xl shadow-slate-200">
                                        <i class='bx bxs-bolt text-white text-3xl'></i>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 mb-12">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Kimden (Hazırlayan)</p>
                                <h3 class="font-extrabold text-slate-900 text-lg mb-1">{{ $proposal->user->tenant->name }}</h3>
                                <p class="text-slate-500 text-sm font-medium">{{ $proposal->user->name }}</p>
                                <p class="text-slate-500 text-sm font-medium">{{ $proposal->user->email }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Kime</p>
                                <h3 class="font-extrabold text-slate-900 text-lg mb-1">{{ $proposal->customer->company_name }}</h3>
                                <p class="text-slate-500 text-sm font-medium">{{ $proposal->customer->contact_person }}</p>
                                <p class="text-slate-500 text-sm font-medium">{{ $proposal->customer->company_email }}</p>
                                <p class="text-slate-500 text-sm font-medium">{{ $proposal->customer->mobile_phone }}</p>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-md border border-slate-50 mb-12">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-slate-50/50 border-b border-slate-100">
                                            <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Açıklama</th>
                                            <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Adet</th>
                                            <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Birim Fiyat</th>
                                            <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">KDV</th>
                                            <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Toplam</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @foreach($proposal->items as $item)
                                        <tr>
                                            <td class="px-4 md:px-6 py-4 font-bold text-slate-800 text-sm">{{ $item->description }}</td>
                                            <td class="px-4 md:px-6 py-4 text-center text-slate-600 text-sm font-bold">{{ number_format($item->quantity, 0) }} {{ $item->unit }}</td>
                                            <td class="px-4 md:px-6 py-4 text-right">
                                                <p class="text-slate-600 text-sm font-bold">{{ number_format($item->unit_price, 2) }} {{ $proposal->currency }}</p>
                                                @if($item->discount_amount > 0)
                                                <p class="text-[10px] text-rose-500 font-bold">-{{ number_format($item->discount_amount, 2) }} İndirim</p>
                                                @endif
                                            </td>
                                            <td class="px-4 md:px-6 py-4 text-right text-slate-400 text-xs font-bold">%{{ number_format($item->tax_rate, 0) }}</td>
                                            <td class="px-4 md:px-6 py-4 text-right text-slate-950 text-sm font-black">{{ number_format($item->total_price, 2) }} {{ $proposal->currency }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Redesigned Payment Summary (Under items) -->
                        @php
                            $lineDiscounts = $proposal->items->sum('discount_amount');
                            $grossTotal = $proposal->subtotal + $lineDiscounts;
                        @endphp
                        <div class="flex justify-end">
                            <div class="w-full md:w-80 space-y-3 pt-6 border-t border-slate-100">
                                <div class="flex justify-between items-center text-slate-500">
                                    <span class="text-xs font-bold uppercase tracking-tight">Brüt Toplam</span>
                                    <span class="text-sm font-black">{{ number_format($grossTotal, 2) }} {{ $proposal->currency }}</span>
                                </div>

                                @if($lineDiscounts > 0)
                                <div class="flex justify-between items-center text-rose-500 bg-rose-50/50 px-3 py-1.5 rounded-xl border border-rose-100">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Satır İndirimleri</span>
                                    <span class="text-sm font-bold">-{{ number_format($lineDiscounts, 2) }} {{ $proposal->currency }}</span>
                                </div>
                                @endif

                                <div class="flex justify-between items-center text-slate-800 pt-2">
                                    <span class="text-xs font-bold uppercase tracking-tight">Ara Toplam (Net)</span>
                                    <span class="text-sm font-black">{{ number_format($proposal->subtotal, 2) }} {{ $proposal->currency }}</span>
                                </div>

                                @if($proposal->discount_amount > 0)
                                <div class="flex justify-between items-center text-rose-500 bg-rose-50/50 px-3 py-1.5 rounded-xl border border-rose-100">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Genel İndirim</span>
                                    <span class="text-sm font-bold">-{{ number_format($proposal->discount_amount, 2) }} {{ $proposal->currency }}</span>
                                </div>
                                @endif

                                <div class="flex justify-between items-center text-slate-500">
                                    <span class="text-xs font-bold uppercase tracking-tight">Toplam KDV</span>
                                    <span class="text-sm font-black">{{ number_format($proposal->tax_amount, 2) }} {{ $proposal->currency }}</span>
                                </div>

                                <div class="pt-4 mt-2 border-t-2 border-slate-900 border-dashed animate-pulse"></div>

                                <div class="flex justify-between items-start pt-2">
                                    <div>
                                        <span class="block text-[10px] font-black text-indigo-500 uppercase tracking-widest leading-none mb-1">Genel Toplam</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">KDV DAHİL FİYAT</span>
                                    </div>
                                    <span class="text-3xl font-black text-slate-950 tracking-tighter">
                                        {{ number_format($proposal->total_amount, 2) }} <span class="text-lg">{{ $proposal->currency }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($proposal->notes)
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Teklif Notları (Müşteriye Gözüken)</p>
                            <div class="p-6 bg-slate-50 rounded-2xl text-slate-600 text-sm leading-relaxed border border-slate-100 whitespace-pre-line font-medium italic">
                                {{ $proposal->notes }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Sağ Kolon: Bilgi ve İşlemler -->
                <div class="space-y-8">
                   
                     <!-- Zaman Damgası -->
                    <div class="bg-white rounded-md p-8 border border-slate-100 shadow-sm space-y-6">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Zaman Damgası</h3>
                        
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-slate-400">Teklif Tarihi</span>
                            <span class="text-xs font-black text-slate-700">{{ $proposal->proposal_date->format('d.m.Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-slate-400">Geçerlilik</span>
                            <span class="text-xs font-black text-slate-700">{{ $proposal->valid_until ? $proposal->valid_until->format('d.m.Y') : 'Belirtilmedi' }}</span>

                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-slate-400">Teslim Tarihi</span>
                            <span class="text-xs font-black text-slate-700">{{ $proposal->delivery_date ? $proposal->delivery_date->format('d.m.Y') : 'Belirtilmedi' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-slate-400">Ödeme Türü</span>
                            <span class="text-xs font-black text-slate-700">{{ $proposal->payment_type }}</span>
                        </div>
                        <div class="pt-4 border-t border-slate-50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Oluşturan Kullanıcı</p>
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($proposal->user->name ?? 'User') }}&background=f1f5f9&color=0f172a" class="w-8 h-8 rounded-lg" alt="">
                                <div>
                                    <p class="text-xs font-black text-slate-900">{{ $proposal->user->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400">{{ $proposal->user->email }}</p>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onay / Red İşlemleri -->
                    @if(in_array($proposal->status, ['draft', 'pending', 'viewed']))
                    <div class="bg-white rounded-md p-8 border border-slate-100 shadow-xl shadow-slate-200/50 sticky top-8">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Teklifi Değerlendir</h3>
                        
                        <!-- Validation Errors -->
                        @if($errors->any())
                        <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 text-sm font-bold">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="space-y-4">
                            <!-- Approve Form -->
                            <form action="{{ route('proposals.public.action', $proposal->public_token) }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="w-full h-14 rounded-2xl bg-emerald-600 text-white font-black uppercase tracking-widest hover:bg-emerald-700 hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-emerald-200">
                                    Teklifi Onayla
                                </button>
                            </form>
                            
                            <!-- Reject Trigger -->
                            <button type="button" onclick="document.getElementById('reject-modal').classList.remove('hidden')" class="w-full h-14 rounded-2xl bg-white border-2 border-slate-100 text-slate-600 font-black uppercase tracking-widest hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100 active:scale-95 transition-all">
                                Reddet
                            </button>
                        </div>
                    </div>
                    @endif

                     <!-- İşlemler (Yazdır / PDF) -->
                    <div class=" flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="{{ route('proposals.public.print', $proposal->public_token) }}" target="_blank" class="w-full flex-1 h-12 rounded-xl bg-white border-2 border-slate-100 text-slate-600 font-bold uppercase tracking-tight hover:bg-slate-50 hover:border-slate-200 hover:text-slate-900 transition-all flex items-center justify-center gap-2 group">
                            <i class='bx bx-printer text-xl text-slate-400 group-hover:text-slate-900 transition-colors'></i>
                            <span>Yazdır</span>
                        </a>
                        <a href="{{ route('proposals.public.pdf', $proposal->public_token) }}" class="w-full flex-1 h-12 rounded-xl bg-slate-900 text-white font-bold uppercase tracking-tight hover:bg-indigo-600 transition-all flex items-center justify-center gap-2 shadow-lg shadow-slate-200 hover:shadow-indigo-200">
                            <i class='bx bxs-file-pdf text-xl'></i>
                            <span>PDF İndir</span>
                        </a>
                    </div>

                </div>
            </div>
            
             <div class="pb-8 text-[12px] font-medium text-slate-400 text-center pt-10">
        powered by <a href="https://fiyera.co" target="_blank" class="text-indigo-600 font-bold">fiyera.co</a>
        </div>

    </div>
    </div>
    
    <!-- Reject Modal (Moved to Body Level) -->
    <div id="reject-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm hidden" role="dialog" aria-modal="true">
        <div class="bg-white rounded-[32px] w-full max-w-md p-8 shadow-2xl animate-in zoom-in-95" onclick="event.stopPropagation()">
            <h3 class="text-lg font-black text-slate-900 mb-2">Reddetme Nedeni</h3>
            <p class="text-sm text-slate-500 mb-6 font-medium">Lütfen teklifi reddetme nedeninizi kısaca belirtiniz.</p>
            
            <form action="{{ route('proposals.public.action', $proposal->public_token) }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="reject">
                <textarea name="note" rows="3" class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 mb-6 focus:outline-none focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all font-medium text-slate-600 placeholder:text-slate-400" placeholder="Bir not bırakın..."></textarea>
                
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="flex-1 h-12 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition-colors">
                        Vazgeç
                    </button>
                    <button type="submit" class="flex-1 h-12 rounded-xl bg-rose-600 text-white font-bold hover:bg-rose-700 shadow-lg shadow-rose-200 transition-all">
                        Reddet
                    </button>
                </div>
            </form>
        </div>
        <!-- Backdrop Click Handler -->
        <div class="absolute inset-0 -z-10" onclick="document.getElementById('reject-modal').classList.add('hidden')"></div>
    </div>
</body>
</html>
