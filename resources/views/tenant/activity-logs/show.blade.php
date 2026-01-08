@extends('tenant.layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('activity-logs.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class='bx bx-arrow-back text-xl'></i>
                </a>
                <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">İşlem Detayı</h1>
            </div>
            <p class="text-slate-500 text-sm ml-8">
                <span class="font-mono text-xs bg-slate-100 px-2 py-0.5 rounded text-slate-600">#{{ $activityLog->id }}</span>
                numaralı işlem kaydının detaylarını görüntülüyorsunuz.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Meta Information Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 md:p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6">İşlem Bilgileri</h3>
                
                <div class="space-y-6">
                    <!-- User Info -->
                    <div>
                        <span class="text-xs font-medium text-slate-400 block mb-2">Kullanıcı</span>
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                             <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-indigo-600 text-xs font-bold shadow-sm">
                                {{ $activityLog->user ? substr($activityLog->user->name, 0, 2) : 'S' }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-950">{{ $activityLog->user ? $activityLog->user->name : 'Sistem' }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">{{ $activityLog->user ? $activityLog->user->email : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Event Type -->
                    <div>
                        <span class="text-xs font-medium text-slate-400 block mb-2">İşlem Tipi</span>
                        @php
                            $eventColors = [
                                'created' => 'text-emerald-700 bg-emerald-50 border-emerald-100',
                                'updated' => 'text-amber-700 bg-amber-50 border-amber-100',
                                'deleted' => 'text-rose-700 bg-rose-50 border-rose-100',
                            ];
                            $eventLabels = [
                                'created' => 'Yeni Kayıt Oluşturma',
                                'updated' => 'Kayıt Güncelleme',
                                'deleted' => 'Kayıt Silme',
                            ];
                        @endphp
                        <div class="px-4 py-3 rounded-xl border {{ $eventColors[$activityLog->event] ?? 'text-slate-700 bg-slate-50 border-slate-200' }}">
                            <div class="flex items-center gap-2">
                                <i class='bx {{ $activityLog->event == "created" ? "bx-plus-circle" : ($activityLog->event == "updated" ? "bx-edit" : "bx-trash") }} text-lg'></i>
                                <span class="font-bold text-sm">{{ $eventLabels[$activityLog->event] ?? ucfirst($activityLog->event) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Subject Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">İlgili Tablo</span>
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
                                $className = class_basename($activityLog->subject_type);
                            @endphp
                            <span class="text-sm font-bold text-slate-700">{{ $subjectMap[$className] ?? $className }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kayıt ID</span>
                            <span class="text-sm font-mono font-bold text-slate-700">#{{ $activityLog->subject_id }}</span>
                        </div>
                    </div>

                    <!-- Date & IP -->
                    <div class="space-y-3 pt-4 border-t border-slate-50">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-medium text-slate-400">Tarih</span>
                            <span class="text-xs font-bold text-slate-600">{{ $activityLog->created_at->format('d.m.Y H:i:s') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-medium text-slate-400">IP Adresi</span>
                            <span class="text-xs font-mono font-bold text-slate-600">{{ $activityLog->ip_address }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Changes Card -->
        <div class="lg:col-span-2">
             <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-900">Değişiklik Kayıtları</h3>
                    <span class="text-xs font-medium text-slate-400">{{ count($activityLog->properties['attributes'] ?? ($activityLog->properties['new'] ?? [])) }} alan etkilendi</span>
                </div>
                
                @if($activityLog->event === 'updated' && (isset($activityLog->properties['old']) || isset($activityLog->properties['new'])))
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-4 md:px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-1/4">Alan Adı</th>
                                    <th class="px-4 md:px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-1/3">Eski Değer</th>
                                    <th class="px-4 md:px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-1/3">Yeni Değer</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($activityLog->properties['new'] ?? [] as $key => $newValue)
                                    @php
                                        $oldValue = $activityLog->properties['old'][$key] ?? null;
                                    @endphp
                                    @if($oldValue != $newValue)
                                        <tr class="hover:bg-slate-50/30">
                                            <td class="px-4 md:px-6 py-4 text-sm font-bold text-slate-700 font-mono">{{ $key }}</td>
                                            <td class="px-4 md:px-6 py-4">
                                                <div class="text-xs text-rose-700 bg-rose-50 border border-rose-100 rounded-lg p-2 font-mono break-all inline-block min-w-full">
                                                    {{ is_array($oldValue) ? json_encode($oldValue) : ($oldValue ?? 'null') }}
                                                </div>
                                            </td>
                                            <td class="px-4 md:px-6 py-4">
                                                <div class="text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg p-2 font-mono break-all inline-block min-w-full">
                                                    {{ is_array($newValue) ? json_encode($newValue) : ($newValue ?? 'null') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($activityLog->event === 'created' && isset($activityLog->properties['attributes']))
                     <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-4 md:px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-1/3">Alan Adı</th>
                                    <th class="px-4 md:px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-2/3">Değer</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($activityLog->properties['attributes'] as $key => $value)
                                     <tr class="hover:bg-slate-50/30">
                                        <td class="px-4 md:px-6 py-4 text-sm font-bold text-slate-700 font-mono">{{ $key }}</td>
                                        <td class="px-4 md:px-6 py-4">
                                            <div class="text-xs text-slate-600 bg-slate-50 border border-slate-100 rounded-lg p-2 font-mono break-all inline-block min-w-full">
                                                {{ is_array($value) ? json_encode($value) : ($value ?? 'null') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                 @elseif($activityLog->event === 'created' && isset($activityLog->properties['new']))
                     <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-4 md:px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-1/3">Alan Adı</th>
                                    <th class="px-4 md:px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-2/3">Değer</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($activityLog->properties['new'] as $key => $value)
                                     <tr class="hover:bg-slate-50/30">
                                        <td class="px-4 md:px-6 py-4 text-sm font-bold text-slate-700 font-mono">{{ $key }}</td>
                                        <td class="px-4 md:px-6 py-4">
                                            <div class="text-xs text-slate-600 bg-slate-50 border border-slate-100 rounded-lg p-2 font-mono break-all inline-block min-w-full">
                                                {{ is_array($value) ? json_encode($value) : ($value ?? 'null') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($activityLog->event === 'deleted' && (isset($activityLog->properties['old']) || isset($activityLog->properties['attributes'])))
                      <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-4 md:px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-1/3">Alan Adı</th>
                                    <th class="px-4 md:px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-2/3">Silinen Değer</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($activityLog->properties['old'] ?? $activityLog->properties['attributes'] as $key => $value)
                                     <tr class="hover:bg-slate-50/30">
                                        <td class="px-4 md:px-6 py-4 text-sm font-bold text-slate-700 font-mono">{{ $key }}</td>
                                        <td class="px-4 md:px-6 py-4">
                                            <div class="text-xs text-rose-700 bg-rose-50 border border-rose-100 rounded-lg p-2 font-mono break-all inline-block min-w-full">
                                                {{ is_array($value) ? json_encode($value) : ($value ?? 'null') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                 @else
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class='bx bx-info-circle text-3xl text-slate-300'></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1">Detaylı Değişiklik Bilgisi Yok</h3>
                        <p class="text-xs text-slate-500 mb-4">Bu işlem için kaydedilmiş detaylı özellik farkı bulunamamıştır.</p>
                        
                        @if($activityLog->properties)
                        <div class="text-left bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <pre class="text-xs font-mono text-slate-600 overflow-auto max-h-60">{{ json_encode($activityLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                        @endif
                    </div>
                @endif
             </div>
        </div>
    </div>
</div>
@endsection
