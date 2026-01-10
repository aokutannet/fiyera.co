@extends('tenant.layouts.app')

@section('content')
<div class="space-y-8" x-data="{ 
    deleteCustomer: null,
    selected: [],
    get allSelected() {
        return this.selected.length === {{ $customers->count() }} && this.selected.length > 0;
    },
    confirmDelete(customer) {
        this.deleteCustomer = customer;
        $dispatch('open-modal', 'delete-customer-confirm');
    },
    toggleAll() {
        if (this.allSelected) {
            this.selected = [];
        } else {
            this.selected = {{ $customers->pluck('id') }};
        }
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">Müşteri Yönetimi</h1>
            <p class="text-slate-500 text-sm mt-1">Müşterilerinizi listeleyin ve yeni müşteriler ekleyin.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <!-- Search Area -->
            <form action="{{ route('customers.index') }}" method="GET" class="relative group" id="searchForm">
                <i class='bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-indigo-600 transition-colors'></i>
                <input type="text" name="search" x-data="{ search: '{{ request('search') }}' }" x-model="search"
                    @input.debounce.500ms="if(search.length >= 3 || search.length == 0) $el.closest('form').submit()"
                    x-init="if('{{ request('search') }}'.length > 0) { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                    placeholder="Müşteri, yetkili veya VKN ara..." 
                    class="h-11 w-full sm:w-72 pl-11 pr-4 rounded-xl bg-white border border-slate-200 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all shadow-sm">
            </form>

            @if(auth()->user()->hasPermission('customers.create'))
            <a href="{{ route('customers.create') }}" class="h-11 px-6 flex items-center justify-center gap-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 whitespace-nowrap">
                <i class='bx bx-plus text-xl'></i> Yeni Müşteri Ekle
            </a>
            @endif
        </div>
    </div>

    <!-- Bulk Actions (Floating) -->
    <div x-show="selected.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-full"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-full"
         class="fixed bottom-8 left-1/2 -translate-x-1/2 z-40 w-full max-w-2xl px-4"
         style="display: none;">
        
        <div class="bg-indigo-50/90 backdrop-blur-xl border border-indigo-100 rounded-full p-2 shadow-2xl shadow-indigo-900/10 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 pl-4">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-200/50 text-indigo-700 font-black text-sm">
                    <span x-text="selected.length"></span>
                </div>
                <span class="text-sm font-bold text-slate-700">Adet müşteri seçildi</span>
            </div>
            
            <div class="flex items-center gap-2 pr-2">
                <button @click="selected = []" class="h-10 px-6 rounded-full text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-white/50 transition-all flex items-center justify-center leading-none">
                    Vazgeç
                </button>
                
                <form action="{{ route('customers.bulk-destroy') }}" method="POST" class="flex items-center m-0" onsubmit="return confirm('Seçili müşterileri silmek istediğinize emin misiniz?');">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" class="h-10 px-6 rounded-full bg-rose-600 text-white text-xs font-black hover:bg-rose-700 transition-all shadow-lg shadow-rose-200 hover:shadow-rose-300 transform active:scale-95 flex items-center justify-center gap-2 leading-none">
                        <i class='bx bx-trash text-base'></i>
                        <span>SEÇİLENLERİ SİL</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="block md:hidden space-y-4">
        @foreach($customers as $customer)
        <div class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm space-y-4 {{ $customer->status === 'passive' ? 'opacity-60' : '' }}">
            <!-- Header: Company & Status -->
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                        <i class='bx bx-buildings text-xl'></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-950">{{ $customer->company_name }}</h3>
                        @if($customer->company_email)
                        <p class="text-[10px] text-slate-400 font-medium">{{ $customer->legal_title }}</p>
                        @endif
                    </div>
                </div>
                <div class="shrink-0">
                    @if($customer->status === 'active')
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-emerald-50 text-xs font-bold text-emerald-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Aktif
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-slate-50 text-xs font-bold text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Pasif
                    </span>
                    @endif
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-3 py-3 border-y border-slate-50">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">İlgili Kişi</p>
                    <p class="text-xs font-bold text-slate-700">{{ $customer->contact_person ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kategori</p>
                    <p class="text-xs font-bold text-slate-700">{{ $customer->category ?? 'Genel' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tür</p>
                    <span class="text-[10px] font-bold uppercase {{ $customer->type === 'legal' ? 'text-indigo-500' : 'text-amber-500' }}">
                        {{ $customer->type === 'legal' ? 'Tüzel Kişi' : 'Gerçek Kişi' }}
                    </span>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">İrtibat</p>
                    <p class="text-xs font-medium text-slate-600">{{ $customer->mobile_phone ?? $customer->landline_phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Teklifler</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700">
                        {{ $customer->proposals_count }} Adet
                    </span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                @if(auth()->user()->hasPermission('customers.edit'))
                <a href="{{ route('customers.edit', $customer) }}" class="flex-1 h-9 flex items-center justify-center gap-2 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-bold hover:bg-indigo-100 transition-all">
                    <i class='bx bx-edit-alt text-base'></i> Düzenle
                </a>
                <form action="{{ route('customers.toggle-status', $customer) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full h-9 flex items-center justify-center gap-2 rounded-lg text-xs font-bold transition-all {{ $customer->status === 'active' ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }}">
                        <i class='bx {{ $customer->status === 'active' ? 'bx-pause-circle' : 'bx-play-circle' }} text-base'></i>
                        {{ $customer->status === 'active' ? 'Pasife Al' : 'Aktif Et' }}
                    </button>
                </form>
                @endif
                @if(auth()->user()->hasPermission('customers.delete'))
                <button type="button" @click="confirmDelete({{ json_encode($customer) }})" class="h-9 w-9 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-all">
                    <i class='bx bx-trash text-base'></i>
                </button>
                @endif
            </div>
        </div>
        @endforeach

        @if($customers->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 bg-slate-50 rounded-xl border border-slate-100 border-dashed">
            <i class='bx bx-buildings text-4xl text-slate-300 mb-2'></i>
            <p class="text-slate-400 text-sm font-medium">Henüz müşteri eklenmemiş.</p>
        </div>
        @endif
    </div>

    <!-- Desktop Table View -->
    <div class="hidden md:block bg-white rounded-md border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="pl-6 py-4 w-4">
                        <input type="checkbox" @click="toggleAll()" :checked="allSelected" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 focus:ring-offset-0 w-4 h-4 cursor-pointer">
                    </th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Firma / İlgili Kişi</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori / Tür</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">İrtibat</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Teklifler</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($customers as $customer)
                <tr class="hover:bg-slate-50/50 transition-colors" :class="{'bg-indigo-50/50 hover:bg-indigo-50/80': selected.includes({{ $customer->id }})}">
                    <td class="pl-6 py-4">
                        <input type="checkbox" value="{{ $customer->id }}" x-model="selected" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 focus:ring-offset-0 w-4 h-4 cursor-pointer">
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i class='bx bx-buildings text-xl'></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-950">{{ $customer->company_name }}</p>
                                <p class="text-xs text-slate-500 font-medium">{{ $customer->contact_person ?? 'İlgili Kişi Belirtilmedi' }}</p>
                                @if($customer->company_email)
                                <p class="text-[10px] text-slate-400">{{ $customer->legal_title }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-slate-700">{{ $customer->category ?? 'Genel' }}</p>
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $customer->type === 'legal' ? 'text-indigo-500' : 'text-amber-500' }}">
                            {{ $customer->type === 'legal' ? 'Tüzel Kişi' : 'Gerçek Kişi' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-slate-600">{{ $customer->mobile_phone ?? $customer->landline_phone ?? '-' }}</p>
                        <p class="text-[10px] text-slate-400 font-medium">{{ $customer->company_email }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            {{ $customer->proposals_count }} Adet
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($customer->status === 'active')
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                            Pasif
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if(auth()->user()->hasPermission('customers.edit'))
                            <a href="{{ route('customers.edit', $customer) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all" title="Düzenle">
                                <i class='bx bx-edit-alt text-lg'></i>
                            </a>
                            <form action="{{ route('customers.toggle-status', $customer) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 {{ $customer->status === 'active' ? 'hover:text-amber-600 hover:bg-amber-50' : 'hover:text-emerald-600 hover:bg-emerald-50' }} transition-all" title="{{ $customer->status === 'active' ? 'Pasife Al' : 'Aktif Et' }}">
                                    <i class='bx {{ $customer->status === 'active' ? 'bx-pause-circle' : 'bx-play-circle' }} text-lg'></i>
                                </button>
                            </form>
                            @endif
                            @if(auth()->user()->hasPermission('customers.delete'))
                            <button type="button" @click="confirmDelete({{ json_encode($customer) }})" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Sil">
                                <i class='bx bx-trash text-lg'></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($customers->isEmpty())
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <i class='bx bx-buildings text-4xl text-slate-200'></i>
                            <p class="text-slate-400 text-sm font-medium">Henüz müşteri eklenmemiş.</p>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $customers->links() }}
    </div>

<!-- Add/Edit Customer Modals (Simplified for brevity, but I will include all fields) -->
@php
    $fields = [
        ['name' => 'company_name', 'label' => 'Firma Adı', 'required' => true],
        ['name' => 'contact_person', 'label' => 'İlgili Kişi'],
        ['name' => 'category', 'label' => 'Kategori'],
        ['name' => 'landline_phone', 'label' => 'Sabit Tel'],
        ['name' => 'mobile_phone', 'label' => 'Mobil Tel'],
        ['name' => 'legal_title', 'label' => 'Firma Ünvanı'],
        ['name' => 'tax_number', 'label' => 'VKN / TCKN'],
        ['name' => 'tax_office', 'label' => 'Vergi Dairesi'],
        ['name' => 'city', 'label' => 'İl'],
        ['name' => 'district', 'label' => 'İlçe'],
        ['name' => 'country', 'label' => 'Ülke', 'default' => 'Türkiye'],
    ];
@endphp




<!-- Delete Confirmation Modal -->
<template x-teleport="body">
    <div x-data="{ open: false }" 
         x-show="open" 
         @open-modal.window="if($event.detail === 'delete-customer-confirm') open = true"
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
                    <span class="text-slate-900" x-text="deleteCustomer ? deleteCustomer.company_name : ''"></span> isimli müşteriyi silmek istediğinize emin misiniz? Bu işlem geri alınamaz.
                </p>
                <div class="flex flex-col w-full gap-3">
                    <form :action="'{{ url('customers') }}/' + (deleteCustomer ? deleteCustomer.id : '')" method="POST" class="w-full">
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
</div>
@endsection
