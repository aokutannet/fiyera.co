@extends('admin.layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.orders.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:border-slate-300 transition-colors">
                <i class='bx bx-arrow-back text-xl'></i>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Sipariş #{{ substr($order->payment_id ?? $order->id, -8) }}</h1>
                <p class="text-slate-500 font-medium mt-1">Sipariş detayları ve fatura yönetimi.</p>
            </div>
        </div>
        <div>
             @if($order->status === 'active')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase bg-emerald-50 text-emerald-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Başarılı
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase bg-slate-100 text-slate-500">
                    {{ $order->status }}
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Order Details -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <i class='bx bx-receipt text-indigo-500'></i> Sipariş Bilgileri
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-xs font-bold text-slate-400 uppercase mb-1">Firma</div>
                        @if($order->tenant)
                            <div class="font-bold text-slate-900 text-lg">{{ $order->tenant->name }}</div>
                            <div class="text-sm text-slate-500">{{ $order->tenant->domain ?? 'Domain Yok' }}</div>
                        @else
                             <div class="font-bold text-slate-400 text-lg italic">Silinmiş Firma</div>
                        @endif
                    </div>
                    
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-xs font-bold text-slate-400 uppercase mb-1">Paket</div>
                        <div class="font-bold text-indigo-600 text-lg">{{ $order->plan->name ?? 'Bilinmiyor' }}</div>
                        <div class="text-sm text-slate-500">{{ $order->billing_period === 'yearly' ? 'Yıllık Ödeme' : 'Aylık Ödeme' }}</div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-xs font-bold text-slate-400 uppercase mb-1">Tutar</div>
                        <div class="font-black text-slate-900 text-2xl">₺{{ number_format($order->price, 2) }}</div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-xs font-bold text-slate-400 uppercase mb-1">Ödeme Yöntemi</div>
                        <div class="flex items-center gap-2 font-medium text-slate-700">
                            @if($order->payment_provider === 'stripe')
                                <i class='bx bxl-stripe text-indigo-600 text-xl'></i> Stripe
                            @elseif($order->payment_provider === 'mock_gateway')
                                <i class='bx bx-credit-card text-emerald-600 text-xl'></i> Kredi Kartı
                            @else
                                <i class='bx bx-wallet text-slate-400 text-xl'></i> {{ $order->payment_provider }}
                            @endif
                        </div>
                        <div class="text-xs text-slate-400 mt-1 font-mono">{{ $order->payment_id }}</div>
                    </div>
                </div>
            </div>

            <!-- Billing Info -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <i class='bx bx-building text-amber-500'></i> Fatura Bilgileri
                </h3>
                
                @if(!empty($billingDetails))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase mb-1">Firma Ünvanı</div>
                            <div class="font-medium text-slate-900">{{ $billingDetails['company_name'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase mb-1">Vergi Dairesi / No</div>
                            <div class="font-medium text-slate-900">
                                {{ $billingDetails['tax_office'] ?? '-' }} / {{ $billingDetails['tax_number'] ?? '-' }}
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="text-xs font-bold text-slate-400 uppercase mb-1">Adres</div>
                            <div class="font-medium text-slate-900">{{ $billingDetails['address'] ?? '-' }}</div>
                            <div class="text-sm text-slate-500 mt-1">
                                {{ $billingDetails['district'] ?? '' }} / {{ $billingDetails['city'] ?? '' }} / {{ $billingDetails['country'] ?? '' }}
                            </div>
                            
                            @if(empty($order->billing_snapshot))
                                <div class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100">
                                    <i class='bx bx-info-circle'></i> Bu bilgiler güncel firma kayıtlarından çekilmiştir (Sipariş anında kayıt yoktu).
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-slate-500 italic flex flex-col items-center justify-center py-8">
                        <i class='bx bx-error-circle text-3xl mb-2 opacity-50'></i>
                        Bu sipariş için fatura bilgisi kaydedilmemiş.
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar: Actions -->
        <div class="space-y-8">
            <!-- Invoice Upload -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <i class='bx bx-file text-blue-500'></i> Fatura
                </h3>

                @if($order->invoice_path)
                    <div class="mb-6 bg-emerald-50 rounded-xl p-4 border border-emerald-100 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-600">
                            <i class='bx bxs-file-pdf text-2xl'></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-bold text-emerald-900">Fatura Yüklendi</div>
                            <div class="text-xs text-emerald-700 mt-0.5 mb-2">Fatura başarıyla sisteme eklendi.</div>
                             <a href="{{ Storage::url($order->invoice_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline">
                                <i class='bx bx-download'></i> İndir / Görüntüle
                            </a>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.orders.upload-invoice', $order->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Fatura Yükle / Güncelle</label>
                            <input type="file" name="invoice" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors">
                            <p class="text-xs text-slate-400 mt-2">PDF, JPG veya PNG. Max 5MB.</p>
                        </div>
                        <button type="submit" class="w-full bg-slate-900 text-white rounded-xl py-3 text-sm font-bold hover:bg-slate-800 transition-colors flex items-center justify-center gap-2">
                            <i class='bx bx-upload'></i> Yükle
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="bg-slate-900 rounded-2xl p-6 text-white shadow-lg shadow-slate-900/10">
                <div class="text-sm font-bold text-slate-400 uppercase mb-4">Özet</div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400">Oluşturuldu</span>
                        <span class="font-medium">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400">Son Güncelleme</span>
                        <span class="font-medium">{{ $order->updated_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
