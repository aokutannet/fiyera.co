@extends('admin.layouts.app')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Onboarding Soruları</h1>
            <p class="text-slate-500 font-medium mt-1">Kayıt sonrası sihirbazda sorulan soruları yönetin.</p>
        </div>
        <a href="{{ route('admin.onboarding-questions.create') }}" class="px-6 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/10 flex items-center gap-2">
            <i class='bx bx-plus'></i> Yeni Soru Ekle
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider w-16">Sıra</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider">Soru Metni</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider">Tip</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider">Seçenek Sayısı</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($questions as $q)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-500">{{ $q->order }}</td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-slate-900 block">{{ $q->question }}</span>
                            <span class="text-xs text-slate-400 font-mono">{{ $q->step_id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-600">
                                {{ strtoupper($q->type) }}
                            </span>
                        </td>
                         <td class="px-6 py-4">
                            <span class="text-sm font-medium text-slate-600">{{ is_array($q->options) ? count($q->options) : 0 }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($q->is_active)
                                <span class="text-xs font-bold text-emerald-600">Aktif</span>
                            @else
                                <span class="text-xs font-bold text-slate-400">Pasif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.onboarding-questions.edit', $q) }}" class="p-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:text-indigo-600 hover:border-indigo-200 transition-all">
                                    <i class='bx bx-edit'></i>
                                </a>
                                <form action="{{ route('admin.onboarding-questions.destroy', $q) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:text-rose-600 hover:border-rose-200 transition-all">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 text-sm">Henüz soru eklenmemiş.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
