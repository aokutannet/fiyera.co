@extends('admin.layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Firmalar & Kiracılar</h1>
            <p class="text-slate-500 mt-2 font-medium">Sistemdeki kayıtlı firmaları yönetin.</p>
        </div>
        
        <div class="flex items-center gap-3">
             <div class="flex items-center gap-4 bg-white px-4 py-2 rounded-xl border border-slate-200">
                <i class='bx bx-search text-slate-400 text-xl'></i>
                <input type="text" placeholder="Firma Ara..." class="bg-transparent border-none outline-none text-sm font-medium w-64 placeholder:text-slate-400">
            </div>
        </div>
    </div>

    <!-- Tenant List -->
    <div class="bg-white rounded-md border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/50">
                        <th class="px-8 py-5">Firma Adı</th>
                        <th class="px-4 py-5">Abonelik / Paket</th>
                        <th class="px-4 py-5">Kayıt Tarihi</th>
                        <th class="px-4 py-5">Durum</th>
                        <th class="px-8 py-5 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-lg">
                                    {{ substr($tenant->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $tenant->name }}</p>
                                    <p class="text-[11px] text-slate-400 font-bold">ID: {{ $tenant->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-5">
                            @if($tenant->plan)
                                <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-bold">{{ $tenant->plan->name }}</span>
                            @else
                                <span class="text-slate-400 text-xs font-bold">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-5">
                            <span class="text-sm font-medium text-slate-600">{{ $tenant->created_at->format('d.m.Y') }}</span>
                        </td>
                        <td class="px-4 py-5">
                            @if($tenant->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Pasif
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-slate-50 transition-all">
                                <i class='bx bx-chevron-right text-xl'></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center">
                                <i class='bx bx-building-house text-4xl mb-3 opacity-30'></i>
                                <p class="text-sm font-medium">Henüz hiç firma kaydı bulunmuyor.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-4 border-t border-slate-50">
            {{ $tenants->links() }}
        </div>
    </div>
</div>
@endsection
