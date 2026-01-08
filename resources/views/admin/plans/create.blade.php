@extends('admin.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.plans.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-slate-900 transition-colors">
            <i class='bx bx-arrow-back text-xl'></i>
        </a>
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Yeni Paket Oluştur</h1>
            <p class="text-slate-500 mt-1 font-medium text-sm">Abonelikler için yeni bir plan tanımlayın.</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.plans.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Basic Info -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
            <h2 class="text-lg font-bold text-slate-800 pb-4 border-b border-slate-50">Temel Bilgiler</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Paket Adı</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="Örn: Başlangıç">
                    @error('name') <span class="text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Popülerlik</label>
                    <div class="flex items-center gap-3 h-[46px]">
                         <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_popular" value="1" class="sr-only peer" {{ old('is_popular') ? 'checked' : '' }}>
                            <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <span class="ms-3 text-sm font-medium text-slate-900">En çok tercih edilen olarak işaretle</span>
                        </label>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Açıklama</label>
                    <input type="text" name="description" value="{{ old('description') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="Paket hakkında kısa açıklama">
                    @error('description') <span class="text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
            <h2 class="text-lg font-bold text-slate-800 pb-4 border-b border-slate-50">Fiyatlandırma</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Aylık Fiyat (TL)</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="price_monthly" value="{{ old('price_monthly') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 pl-10 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="0.00">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₺</span>
                    </div>
                    @error('price_monthly') <span class="text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Yıllık Fiyat (TL)</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="price_yearly" value="{{ old('price_yearly') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 pl-10 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="0.00">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₺</span>
                    </div>
                    @error('price_yearly') <span class="text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Limits -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
            <h2 class="text-lg font-bold text-slate-800 pb-4 border-b border-slate-50">Limitler</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kullanıcı Sayısı</label>
                    <input type="number" name="limits[user_count]" value="{{ old('limits.user_count', 1) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Aylık Teklif</label>
                    <input type="number" name="limits[proposal_monthly]" value="{{ old('limits.proposal_monthly', 10) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Müşteri Limiti</label>
                    <input type="number" name="limits[customer_count]" value="{{ old('limits.customer_count', 50) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ürün Limiti</label>
                    <input type="number" name="limits[product_count]" value="{{ old('limits.product_count', 20) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
            <h2 class="text-lg font-bold text-slate-800 pb-4 border-b border-slate-50">Özellikler</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($features as $key => $label)
                    <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-100 hover:bg-slate-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="features[]" value="{{ $key }}" 
                            class="mt-1 w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2"
                            {{ in_array($key, old('features', [])) ? 'checked' : '' }}
                        >
                        <span class="text-sm font-medium text-slate-700 select-none">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('features') <span class="text-xs font-bold text-red-500 d-block mt-2">{{ $message }}</span> @enderror
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pb-12">
            <a href="{{ route('admin.plans.index') }}" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition-all">
                İptal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 transition-all">
                Paketi Oluştur
            </button>
        </div>
    </form>
</div>
@endsection
