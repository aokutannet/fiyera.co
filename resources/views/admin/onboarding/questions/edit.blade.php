@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div>
        <a href="{{ route('admin.onboarding-questions.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-slate-600 mb-4 transition-colors">
            <i class='bx bx-arrow-back'></i> Sorulara Dön
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Soruyu Düzenle</h1>
    </div>

    <form action="{{ route('admin.onboarding-questions.update', $onboardingQuestion) }}" method="POST" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Soru Başlığı (Anahtarı)</label>
                <input type="text" name="step_id" value="{{ $onboardingQuestion->step_id }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500">
                <p class="text-[10px] text-slate-400">Benzersiz olmalı.</p>
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Sıralama</label>
                <input type="number" name="order" value="{{ $onboardingQuestion->order }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-500 uppercase">Soru Metni</label>
            <input type="text" name="question" value="{{ $onboardingQuestion->question }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500">
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-500 uppercase">Alt Açıklama (Opsiyonel)</label>
            <input type="text" name="subtext" value="{{ $onboardingQuestion->subtext }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Soru Tipi</label>
                <select name="type" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500">
                    <option value="radio" {{ $onboardingQuestion->type == 'radio' ? 'selected' : '' }}>Tekli Seçim (Radio)</option>
                    <option value="checkbox" {{ $onboardingQuestion->type == 'checkbox' ? 'selected' : '' }}>Çoklu Seçim (Checkbox)</option>
                    <option value="text" {{ $onboardingQuestion->type == 'text' ? 'selected' : '' }}>Metin Girişi (Text)</option>
                </select>
            </div>
             <div class="flex flex-col gap-4 pt-6">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="has_other" value="1" {{ $onboardingQuestion->has_other ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900">'Diğer' seçeneği olsun mu?</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="is_active" value="1" {{ $onboardingQuestion->is_active ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900">Aktif</span>
                </label>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-500 uppercase">Seçenekler</label>
            <textarea name="options_list" rows="5" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500">{{ is_array($onboardingQuestion->options) ? implode("\n", $onboardingQuestion->options) : '' }}</textarea>
            <p class="text-[10px] text-slate-400">Her satıra bir seçenek gelecek şekilde yazın.</p>
        </div>

        <div class="pt-4 border-t border-slate-50 flex justify-end gap-3">
             <a href="{{ route('admin.onboarding-questions.index') }}" class="px-8 py-3 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition-all">
                İptal
            </a>
            <button type="submit" class="px-8 py-3 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/10">
                Güncelle
            </button>
        </div>
    </form>
</div>
@endsection
