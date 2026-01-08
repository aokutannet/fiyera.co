@extends('tenant.layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">İşlem Geçmişi</h1>
            <p class="text-slate-500 text-sm mt-1">Sistemde yapılan tüm işlemlerin kaydını buradan inceleyebilirsiniz.</p>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Kullanıcı</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">İşlem Türü</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">İlgili Kayıt</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Açıklama</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Tarih</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Detay/İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 md:px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-xs font-bold ring-2 ring-white">
                                        {{ $log->user ? substr($log->user->name, 0, 2) : 'S' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-950">{{ $log->user ? $log->user->name : 'Sistem' }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium font-mono">{{ $log->ip_address }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                @php
                                    $eventColors = [
                                        'created' => 'text-emerald-600 bg-emerald-50',
                                        'updated' => 'text-amber-600 bg-amber-50',
                                        'deleted' => 'text-rose-600 bg-rose-50',
                                    ];
                                    $eventLabels = [
                                        'created' => 'Yeni Kayıt',
                                        'updated' => 'Güncelleme',
                                        'deleted' => 'Silme',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $eventColors[$log->event] ?? 'text-slate-500 bg-slate-50' }}">
                                    {{ $eventLabels[$log->event] ?? ucfirst($log->event) }}
                                </span>
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                @php
                                    $subjectMap = [
                                        'Product' => 'Ürün',
                                        'Customer' => 'Müşteri',
                                        'Proposal' => 'Teklif',
                                        'User' => 'Kullanıcı',
                                        'Setting' => 'Ayarlar',
                                        'Category' => 'Kategori',
                                        'ProposalActivity' => 'Teklif Hareketi',
                                    ];
                                    $className = class_basename($log->subject_type);
                                @endphp
                                <p class="text-sm font-bold text-slate-700">
                                    {{ $subjectMap[$className] ?? $className }}
                                </p>
                                <p class="text-[10px] text-slate-400 font-medium">ID: #{{ $log->subject_id }}</p>
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                <p class="text-sm text-slate-600 font-medium">{{ $log->description }}</p>
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                <p class="text-sm font-bold text-slate-600">{{ $log->created_at->format('d.m.Y') }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">{{ $log->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-4 md:px-6 py-4 text-right">
                                <a href="{{ route('activity-logs.show', $log->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all" title="İncele">
                                    <i class='bx bx-show text-lg'></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center">
                                        <i class='bx bx-history text-3xl text-slate-300'></i>
                                    </div>
                                    <p class="text-slate-500 text-sm font-medium">Henüz işlem kaydı bulunmuyor.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
