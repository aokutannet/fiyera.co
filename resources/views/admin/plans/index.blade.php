@extends('admin.layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Paket Yönetimi</h1>
            <p class="text-slate-500 mt-2 font-medium">Sistemdeki abonelik paketlerini yönetin.</p>
        </div>
        
        <div class="flex items-center gap-3">
             <a href="{{ route('admin.plans.create') }}" class="flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 hover:shadow-indigo-300 hover:bg-indigo-700 transition-all">
                <i class='bx bx-plus text-xl'></i>
                Yeni Paket Oluştur
            </a>
        </div>
    </div>

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($plans as $plan)
            <div class="bg-white rounded-2xl border {{ $plan->is_popular ? 'border-indigo-200 shadow-xl shadow-indigo-50' : 'border-slate-100 shadow-sm' }} overflow-hidden flex flex-col h-full relative group hover:shadow-md transition-all duration-300">
                @if($plan->is_popular)
                    <div class="absolute top-0 right-0 bg-indigo-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider">
                        Popüler
                    </div>
                @endif

                <div class="p-6 flex-grow">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-slate-800">{{ $plan->name }}</h3>
                        <p class="text-xs text-slate-500 font-medium mt-1 line-clamp-2">{{ $plan->description }}</p>
                    </div>

                    <div class="mb-6 flex items-baseline gap-1">
                        <span class="text-3xl font-extrabold text-slate-900">₺{{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                        <span class="text-xs font-bold text-slate-400">/ay</span>
                    </div>

                    <div class="space-y-3 mb-6">
                         <!-- Features Preview -->
                         @php
                            $limits = $plan->limits ?? [];
                         @endphp
                         <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                            <i class='bx bx-user text-slate-400 text-base'></i>
                            {{ $limits['user_count'] ?? 1 }} Kullanıcı
                         </div>
                         <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                            <i class='bx bx-file text-slate-400 text-base'></i>
                            {{ $limits['proposal_monthly'] ?? 10 }} Aylık Teklif
                         </div>
                    </div>
                </div>

                <div class="p-4 border-t border-slate-50 bg-slate-50/50 flex items-center justify-between gap-3">
                    <a href="{{ route('admin.plans.edit', $plan) }}" class="flex-1 flex items-center justify-center gap-2 py-2 rounded-lg bg-white border border-slate-200 text-slate-600 text-xs font-bold hover:border-indigo-600 hover:text-indigo-600 transition-all">
                        <i class='bx bx-edit'></i>
                        Düzenle
                    </a>
                    <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu paketi silmek istediğinize emin misiniz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-400 hover:border-red-500 hover:text-red-500 hover:bg-red-50 transition-all">
                            <i class='bx bx-trash'></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400">
                <i class='bx bx-package text-4xl mb-3 opacity-30'></i>
                <p class="text-sm font-medium">Henüz hiç paket oluşturulmamış.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
