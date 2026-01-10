@extends('admin.layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Onboarding Başvuruları</h1>
            <p class="text-slate-500 font-medium mt-1">Sihirbazı tamamlayan firmaların verdiği yanıtlar.</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider">Firma</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider">Sektör</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider">Ekip</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider">Teklif Adedi</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider">Tarih</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($tenants as $tenant)
                    @php
                        // Helper to find value by key in JSON array
                        $getData = function($key) use ($tenant) {
                            foreach($tenant->onboarding_data ?? [] as $item) {
                                if($item['key'] === $key) return $item['value'];
                            }
                            return '-';
                        };
                    @endphp
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                    {{ substr($tenant->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-slate-900">{{ $tenant->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-600">
                                {{ $getData('onboarding_sector') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-slate-600">{{ $getData('onboarding_team_size') }}</span>
                        </td>
                         <td class="px-6 py-4">
                            <span class="text-sm font-medium text-slate-600">{{ $getData('onboarding_monthly_proposals') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-slate-400">{{ $tenant->created_at->format('d.m.Y') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm">
                                <i class='bx bx-show'></i> İncele
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class='bx bx-notepad text-3xl text-slate-300'></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">Henüz veri yok</h3>
                            <p class="text-xs text-slate-400 mt-1">Hiçbir firma onboarding sürecini tamamlamamış.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($tenants->hasPages())
        <div class="p-4 border-t border-slate-50">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
