@extends('tenant.layouts.app')

@section('content')
@php
    $statusColors = [
        'draft' => 'text-slate-500 bg-slate-50',
        'pending' => 'text-amber-600 bg-amber-50',
        'approved' => 'text-emerald-600 bg-emerald-50',
        'rejected' => 'text-rose-600 bg-rose-50',
    ];
    $statusLabels = [
        'draft' => 'Taslak',
        'pending' => 'Onay Bekliyor',
        'approved' => 'Onaylandı',
        'rejected' => 'Reddedildi',
    ];
@endphp
<div class="space-y-8" x-data="{ 
    deleteProposal: null,
    isLimitModalOpen: false,
    selected: [],
    showStatusMenu: false,
    pendingBulkAction: null,
    pendingBulkStatus: null,
    get allSelected() {
        return this.selected.length === {{ $proposals->count() }} && this.selected.length > 0;
    },
    confirmDelete(proposal) {
        this.deleteProposal = proposal;
        $dispatch('open-modal', 'delete-proposal-confirm');
    },
    toggleAll() {
        if (this.allSelected) {
            this.selected = [];
        } else {
            this.selected = {{ $proposals->pluck('id') }};
        }
    },
    submitBulkAction(action, status = null) {
        this.pendingBulkAction = action;
        this.pendingBulkStatus = status;
        $dispatch('open-modal', 'bulk-action-confirm');
    },
    confirmBulkAction() {
        const form = document.getElementById('bulkActionForm');
        document.getElementById('bulkActionInput').value = this.pendingBulkAction;
        if (this.pendingBulkStatus) {
            document.getElementById('bulkStatusInput').value = this.pendingBulkStatus;
        }
        form.submit();
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">Teklif Yönetimi</h1>
            <p class="text-slate-500 text-sm mt-1">Tekliflerinizi listeleyin, durumlarını takip edin ve yeni teklifler oluşturun.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <!-- Search Area -->
            <form action="{{ route('proposals.index') }}" method="GET" class="relative group" id="searchForm">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <i class='bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-indigo-600 transition-colors'></i>
                <input type="text" name="search" x-data="{ search: '{{ request('search') }}' }" x-model="search"
                    @input.debounce.500ms="if(search.length >= 3 || search.length == 0) $el.closest('form').submit()"
                    x-init="if('{{ request('search') }}'.length > 0) { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                    placeholder="Teklif no, firma veya başlık ara..." 
                    class="h-11 w-full sm:w-72 pl-11 pr-4 rounded-xl bg-white border border-slate-200 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all shadow-sm">
            </form>

            @if(auth()->user()->hasPermission('proposals.create'))
                @if($limitReached)
                <button @click="isLimitModalOpen = true" class="h-11 px-6 flex items-center justify-center gap-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 whitespace-nowrap opacity-70">
                    <i class='bx bx-plus text-xl'></i> Yeni Teklif Oluştur
                </button>
                @else
                <a href="{{ route('proposals.create') }}" class="h-11 px-6 flex items-center justify-center gap-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 whitespace-nowrap">
                    <i class='bx bx-plus text-xl'></i> Yeni Teklif Oluştur
                </a>
                @endif
            @endif
        </div>
    </div>

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 rounded-xl text-sm font-medium">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3">
        <i class='bx bx-check-circle text-lg'></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3">
        <i class='bx bx-error-circle text-lg'></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- Bulk Actions (Floating) -->
    <div x-show="selected.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-full"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-full"
         class="fixed bottom-8 left-1/2 -translate-x-1/2 z-40 w-full max-w-5xl px-4"
         style="display: none;">
        
        <div class="bg-indigo-50/90 backdrop-blur-xl border border-indigo-100 rounded-full p-2 shadow-2xl shadow-indigo-900/10 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 pl-4">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-200/50 text-indigo-700 font-black text-sm">
                    <span x-text="selected.length"></span>
                </div>
                <span class="text-sm font-bold text-slate-700 hidden sm:inline">Seçildi</span>
            </div>
            


             <!-- The Form Structure Re-imagined for Alpine control -->
             <div class="flex items-center gap-2 pr-2">
                <button @click="selected = []" class="h-10 px-4 rounded-full text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-white/50 transition-all flex items-center justify-center leading-none">
                    VAZGEÇ
                </button>

                <!-- Hidden Form for submission -->
                <!-- Hidden Form for submission -->
                <form id="bulkActionForm" action="{{ route('proposals.bulk-actions') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="action" id="bulkActionInput">
                    <input type="hidden" name="status" id="bulkStatusInput">
                    <!-- Send IDs as JSON string to avoid Alpine x-for rendering issues in hidden elements -->
                    <input type="hidden" name="ids" :value="JSON.stringify(selected)">
                </form>

                <!-- Mail -->
                <button @click="submitBulkAction('email')" class="h-10 w-10 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition-all" title="Tekrar Mail Gönder">
                    <i class='bx bx-envelope text-lg'></i>
                </button>

                <!-- SMS -->
                <button @click="submitBulkAction('sms')" class="h-10 w-10 flex items-center justify-center rounded-full bg-emerald-100 text-emerald-600 hover:bg-emerald-200 transition-all" title="Tekrar SMS Gönder">
                    <i class='bx bx-message-square-dots text-lg'></i>
                </button>
 
                <!-- Status Dropdown -->
                <div class="relative" @click.away="showStatusMenu = false">
                    <button @click="showStatusMenu = !showStatusMenu" class="h-10 px-4 rounded-full bg-white border border-indigo-100 text-slate-600 text-xs font-bold hover:bg-slate-50 transition-all flex items-center gap-2 leading-none">
                        <span>DURUM</span>
                        <i class='bx bx-chevron-down text-lg'></i>
                    </button>
                    <!-- Menu -->
                    <div x-show="showStatusMenu" class="absolute bottom-full left-0 mb-2 w-40 bg-white rounded-xl shadow-xl border border-indigo-50 overflow-hidden py-1" style="display: none;">
                        <button @click="submitBulkAction('status', 'draft')" class="w-full text-left px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600">Taslak</button>
                        <button @click="submitBulkAction('status', 'pending')" class="w-full text-left px-4 py-2 text-xs font-bold text-amber-600 hover:bg-amber-50">Onay Bekliyor</button>
                        <button @click="submitBulkAction('status', 'approved')" class="w-full text-left px-4 py-2 text-xs font-bold text-emerald-600 hover:bg-emerald-50">Onaylandı</button>
                        <button @click="submitBulkAction('status', 'rejected')" class="w-full text-left px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50">Reddedildi</button>
                    </div>
                </div>

                <!-- Set to Draft -->
                <button @click="submitBulkAction('status', 'draft')" class="h-10 px-4 rounded-full bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition-all flex items-center gap-2 leading-none">
                    <i class='bx bx-edit'></i> TASLAĞA AT
                </button>

                <!-- Delete -->
                <button @click="submitBulkAction('delete')" class="h-10 px-4 rounded-full bg-rose-600 text-white text-xs font-black hover:bg-rose-700 transition-all shadow-lg shadow-rose-200 hover:shadow-rose-300 active:scale-95 flex items-center justify-center gap-2 leading-none">
                    <i class='bx bx-trash text-base'></i>
                    <span>SİL</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Status Filters -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide">
        <a href="{{ route('proposals.index', array_merge(request()->query(), ['status' => null])) }}" 
           class="h-10 px-5 flex items-center justify-center rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ !request('status') ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100' }}">
            Tümü
        </a>
        <a href="{{ route('proposals.index', array_merge(request()->query(), ['status' => 'draft'])) }}" 
           class="h-10 px-5 flex items-center justify-center rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ request('status') === 'draft' ? 'bg-slate-600 text-white shadow-lg shadow-slate-100' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100' }}">
            Taslaklar
        </a>
        <a href="{{ route('proposals.index', array_merge(request()->query(), ['status' => 'pending'])) }}" 
           class="h-10 px-5 flex items-center justify-center rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ request('status') === 'pending' ? 'bg-amber-500 text-white shadow-lg shadow-amber-100' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100' }}">
            Onay Bekleyenler
        </a>
        <a href="{{ route('proposals.index', array_merge(request()->query(), ['status' => 'approved'])) }}" 
           class="h-10 px-5 flex items-center justify-center rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ request('status') === 'approved' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-100' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100' }}">
            Onaylananlar
        </a>
        <a href="{{ route('proposals.index', array_merge(request()->query(), ['status' => 'rejected'])) }}" 
           class="h-10 px-5 flex items-center justify-center rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ request('status') === 'rejected' ? 'bg-rose-500 text-white shadow-lg shadow-rose-100' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100' }}">
            Reddedilenler
        </a>
    </div>

    <!-- Proposals Mobile List -->
    <div class="md:hidden space-y-4">
        @foreach($proposals as $proposal)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <!-- Header -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                            <i class='bx bx-file text-xl'></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-slate-950 truncate flex items-center gap-1">
                                {{ $proposal->proposal_number }}
                                @if($proposal->source === 'mobile')
                                    <i class='bx bx-mobile-alt text-indigo-500 text-base' title="Mobil Uygulama ile Oluşturuldu"></i>
                                @else
                                    <i class='bx bx-laptop text-slate-400 text-base' title="Web Paneli ile Oluşturuldu"></i>
                                @endif
                            </p>
                            <p class="text-xs text-slate-500 font-bold truncate">{{ $proposal->title }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold shrink-0 {{ $statusColors[$proposal->status] ?? $statusColors['draft'] }}">
                        {{ $statusLabels[$proposal->status] ?? $proposal->status }}
                    </span>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1">MÜŞTERİ</p>
                        <p class="text-xs font-bold text-slate-900 truncate">{{ $proposal->customer->company_name }}</p>
                        <p class="text-[10px] text-slate-500 font-medium truncate">{{ $proposal->customer->contact_person }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                         <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1">TUTAR</p>
                         <p class="text-sm font-black text-slate-900">{{ number_format($proposal->total_amount, 2) }}</p>
                         <p class="text-[10px] text-slate-500 font-bold">{{ $proposal->currency }}</p>
                    </div>
                </div>

                <!-- Footer / Actions -->
                <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                    <div class="flex flex-col">
                        <span class="text-[10px] text-slate-400 font-bold uppercase">TARİH</span>
                        <span class="text-xs font-bold text-slate-600">{{ $proposal->proposal_date->format('d.m.Y') }}</span>
                    </div>
                    
                    <div class="flex items-center gap-1">
                         @if(auth()->user()->hasPermission('proposals.edit'))
                            <a href="{{ route('proposals.edit', $proposal) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50" >
                                <i class='bx bx-edit-alt text-lg'></i>
                            </a>
                        @endif
                        <a href="{{ route('proposals.show', $proposal) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50" >
                            <i class='bx bx-show text-lg'></i>
                        </a>
                        <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50">
                            <i class='bx bx-envelope text-lg'></i>
                        </button>
                         <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50">
                            <i class='bx bx-message-square-dots text-lg'></i>
                        </button>
                        @if(auth()->user()->hasPermission('proposals.delete'))
                            <button @click="confirmDelete({{ json_encode(['id' => $proposal->id, 'number' => $proposal->proposal_number]) }})" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50">
                                <i class='bx bx-trash text-lg'></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
         @if($proposals->isEmpty())
                <div class="flex flex-col items-center gap-3 py-12 text-center">
                    <i class='bx bx-file text-4xl text-slate-200'></i>
                    <p class="text-slate-400 text-sm font-medium">Henüz teklif bulunamadı.</p>
                </div>
        @endif
    </div>

    <!-- Proposals Table (Desktop) -->
    <div class="hidden md:block bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="pl-6 py-4 w-4">
                        <input type="checkbox" @click="toggleAll()" :checked="allSelected" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 focus:ring-offset-0 w-4 h-4 cursor-pointer">
                    </th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Teklif Bilgisi</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Müşteri</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Tarih / Geçerlilik</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Tutar</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($proposals as $proposal)
                <tr class="hover:bg-slate-50/50 transition-colors" :class="{'bg-indigo-50/50 hover:bg-indigo-50/80': selected.includes({{ $proposal->id }})}">
                    <td class="pl-6 py-4">
                        <input type="checkbox" value="{{ $proposal->id }}" x-model="selected" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 focus:ring-offset-0 w-4 h-4 cursor-pointer">
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i class='bx bx-file text-xl'></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-950 flex items-center gap-1">
                                    {{ $proposal->title }}
                                    @if($proposal->source === 'mobile')
                                        <i class='bx bx-mobile-alt text-indigo-500 text-base' title="Mobil Uygulama ile Oluşturuldu"></i>
                                    @else
                                        <i class='bx bx-laptop text-slate-400 text-base' title="Web Paneli ile Oluşturuldu"></i>
                                    @endif
                                </p>
                                <p class="text-xs text-slate-500 font-medium">{{ $proposal->proposal_number }}</p>
                                <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1 font-medium">
                                    <i class='bx bx-pencil text-[11px] text-slate-300'></i>
                                    <span class="opacity-70 text-[9px] uppercase tracking-tighter">HAZIRLAYAN:</span>
                                    <span class="text-slate-600 font-bold uppercase tracking-tight">
                                        {{ $proposal->user->name ?? (optional(auth()->user())->name ?? 'Bilinmiyor') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-slate-700">{{ $proposal->customer->company_name }}</p>
                        <p class="text-[10px] text-slate-400 font-medium">{{ $proposal->customer->contact_person }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-slate-600">{{ $proposal->proposal_date->format('d.m.Y') }}</p>
                        @if($proposal->valid_until)
                        <p class="text-[10px] text-slate-400 font-medium">Son: {{ $proposal->valid_until->format('d.m.Y') }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-black text-slate-950">{{ number_format($proposal->total_amount, 2) }} {{ $proposal->currency }}</p>
                        <p class="text-[10px] text-slate-400 font-medium">{{ $proposal->items_count ?? $proposal->items()->count() }} Kalem</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $statusColors[$proposal->status] ?? $statusColors['draft'] }}">
                            {{ $statusLabels[$proposal->status] ?? $proposal->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if(auth()->user()->hasPermission('proposals.edit'))
                            <a href="{{ route('proposals.edit', $proposal) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all border border-transparent hover:border-amber-100" data-tooltip="Düzenle">
                                <i class='bx bx-edit-alt text-lg'></i>
                            </a>
                            @endif
                             <a href="{{ route('proposals.show', $proposal) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100" data-tooltip="Görüntüle">
                                <i class='bx bx-show text-lg'></i>
                            </a>
                            @if(auth()->user()->hasPermission('proposals.create'))
                            <form action="{{ route('proposals.duplicate', $proposal) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all border border-transparent hover:border-indigo-100" data-tooltip="Kopyasını Oluştur">
                                    <i class='bx bx-copy text-lg'></i>
                                </button>
                            </form>
                            @endif
                            <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all border border-transparent hover:border-indigo-100" data-tooltip="E-Posta Gönder">
                                <i class='bx bx-envelope text-lg'></i>
                            </button>
                            <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-all border border-transparent hover:border-emerald-100" data-tooltip="SMS Gönder">
                                <i class='bx bx-message-square-dots text-lg'></i>
                            </button>
                           
                           
                            @if(auth()->user()->hasPermission('proposals.delete'))
                            <button type="button" @click="confirmDelete({{ json_encode(['id' => $proposal->id, 'number' => $proposal->proposal_number]) }})" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all border border-transparent hover:border-rose-100" data-tooltip="Sil">
                                <i class='bx bx-trash text-lg'></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($proposals->isEmpty())
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <i class='bx bx-file text-4xl text-slate-200'></i>
                            <p class="text-slate-400 text-sm font-medium">Henüz teklif bulunamadı.</p>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $proposals->links() }}
    </div>

    <!-- Limit Reached Modal -->
    <div x-show="isLimitModalOpen" 
         @keydown.escape.window="isLimitModalOpen = false"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         x-cloak>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="isLimitModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                 @click="isLimitModalOpen = false"></div>

            <div x-show="isLimitModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:max-w-md sm:w-full border border-slate-100 relative z-10 text-center p-8">
                
                <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-lock-alt text-3xl text-amber-500'></i>
                </div>
                
                <h3 class="text-xl font-black text-slate-900 mb-2">Paket Limiti Doldu</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-8">
                    Mevcut paketinizin aylık teklif oluşturma limitine ({{ $plan->limits['proposal_monthly'] ?? 30 }} teklif) ulaştınız. Yeni teklif oluşturmak için paketinizi yükseltin.
                </p>

                <div class="flex gap-3">
                    <button @click="isLimitModalOpen = false" class="flex-1 h-12 rounded-xl border border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition-all">
                        Vazgeç
                    </button>
                    <a href="{{ route('subscription.plans') }}" class="flex-1 h-12 flex items-center justify-center rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                        Paketleri İncele
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <template x-teleport="body">
    <div x-data="{ open: false }" 
         x-show="open" 
         @open-modal.window="if($event.detail === 'delete-proposal-confirm') open = true"
         @close-modal.window="open = false"
         class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false"></div>
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm relative z-10 overflow-hidden border border-slate-100 p-8 flex flex-col items-center">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mb-6">
                    <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center animate-pulse">
                        <i class='bx bx-trash text-4xl text-rose-600'></i>
                    </div>
                </div>
                <h3 class="text-xl font-black text-slate-950 mb-2">Emin misiniz?</h3>
                <p class="text-slate-500 font-bold text-center leading-relaxed mb-8">
                    <span class="text-slate-900" x-text="deleteProposal ? deleteProposal.number : ''"></span> numaralı teklifi silmek istediğinize emin misiniz? Bu işlem geri alınamaz.
                </p>
                <div class="flex flex-col w-full gap-3">
                    <form :action="'{{ url('proposals') }}/' + (deleteProposal ? deleteProposal.id : '')" method="POST" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-4 rounded-2xl bg-rose-600 text-white text-sm font-black hover:bg-rose-700 transition-all shadow-xl shadow-rose-100 active:scale-[0.98]">
                            EVET, SİL
                        </button>
                    </form>
                    <button @click="open = false" class="w-full py-4 rounded-2xl bg-slate-50 text-slate-500 text-sm font-bold hover:bg-slate-100 transition-all">
                        VAZGEÇ
                    </button>
                </div>
            </div>
        </div>
    </div>
    </template>

    <!-- Bulk Action Confirmation Modal -->
    <template x-teleport="body">
    <div x-data="{ open: false }" 
         x-show="open" 
         @open-modal.window="if($event.detail === 'bulk-action-confirm') open = true"
         @close-modal.window="open = false"
         class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false"></div>
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm relative z-10 overflow-hidden border border-slate-100 p-8 flex flex-col items-center">
                <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mb-6">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center animate-pulse">
                        <i class='bx bx-check-double text-4xl text-indigo-600'></i>
                    </div>
                </div>
                <h3 class="text-xl font-black text-slate-950 mb-2">Emin misiniz?</h3>
                <p class="text-slate-500 font-bold text-center leading-relaxed mb-8">
                    Seçili teklifler için bu işlemi gerçekleştirmek istediğinize emin misiniz?
                </p>
                <div class="flex flex-col w-full gap-3">
                    <button @click="confirmBulkAction(); open = false;" class="w-full py-4 rounded-2xl bg-indigo-600 text-white text-sm font-black hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 active:scale-[0.98]">
                        EVET, İŞLEMİ UYGULA
                    </button>
                    <button @click="open = false" class="w-full py-4 rounded-2xl bg-slate-50 text-slate-500 text-sm font-bold hover:bg-slate-100 transition-all">
                        VAZGEÇ
                    </button>
                </div>
            </div>
        </div>
    </div>
    </template>
</div>

<style>
    [data-tooltip] {
        position: relative;
    }
    [data-tooltip]::before {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-8px);
        background: #0f172a;
        color: white;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        z-index: 50;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    [data-tooltip]:hover::before {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(-12px);
    }
    /* Arrow */
    [data-tooltip]::after {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(0);
        border: 5px solid transparent;
        border-top-color: #0f172a;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        z-index: 50;
    }
    [data-tooltip]:hover::after {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(-2px);
    }
</style>
@endsection
