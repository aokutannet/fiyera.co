@extends('tenant.layouts.app')

@section('content')
<div class="max-w-8xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row items-center justify-between gap-6 mb-8">
        <div class="flex items-center gap-4 w-full lg:w-auto">
            <a href="{{ route('proposals.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-slate-900 transition-all shadow-sm shrink-0">
                <i class='bx bx-chevron-left text-2xl'></i>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-950 tracking-tight line-clamp-1">{{ $proposal->title }} </h1>
                <div class="flex flex-wrap items-center gap-3 mt-1">
                    <p class="text-slate-500 text-sm font-medium whitespace-nowrap">{{ $proposal->proposal_number }} - Teklif Detayı</p>
                    <div class="hidden sm:block h-1 w-1 rounded-full bg-slate-300"></div>
                    <p class="text-[10px] text-slate-400 items-center gap-1.5 font-medium hidden sm:flex">
                        <i class='bx bx-pencil text-[11px] text-indigo-400'></i>
                        <span class="opacity-70 text-[9px] uppercase tracking-tighter">HAZIRLAYAN:</span>
                        <span class="text-slate-700 font-black uppercase tracking-tight">
                            {{ $proposal->user->name ?? (optional(auth()->user())->name ?? 'Bilinmiyor') }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-auto" x-data="{ 
            confirmModal: false, 
            actionTitle: '', 
            actionDescription: '', 
            actionFormId: '',
            confirmText: 'Onayla',
            confirmColor: 'bg-indigo-600',
            openModal(title, desc, formId, text = 'Gönder', color = 'bg-indigo-600') {
                this.actionTitle = title;
                this.actionDescription = desc;
                this.actionFormId = formId;
                this.confirmText = text;
                this.confirmColor = color;
                this.confirmModal = true;
            }
        }">
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-4">
                
                <!-- Quick Actions (Scrollable on mobile) -->
                <div class="overflow-x-auto -mx-4 px-4 lg:mx-0 lg:px-0 pb-1 lg:pb-0">
                    <div class="flex items-center gap-2 min-w-max">
                        <a href="{{ route('proposals.edit', $proposal) }}" class="w-11 h-11 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all shadow-sm" data-tooltip="Düzenle">
                            <i class='bx bx-edit-alt text-xl'></i>
                        </a>

                        <button type="button" 
                                @click="openModal('WhatsApp ile Paylaş', '{{ $proposal->customer->mobile_phone }} numaralı telefona WhatsApp üzerinden mesaj gönderilecektir.', 'whatsapp-form', 'WhatsApp\'ı Aç', 'bg-emerald-600')"
                                class="w-11 h-11 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-all shadow-sm" data-tooltip="WhatsApp ile Gönder">
                            <i class='bx bxl-whatsapp text-xl'></i>
                        </button>

                        <button type="button" 
                                @click="openModal('SMS Gönder', '{{ $proposal->customer->mobile_phone }} numaralı telefona teklif özeti SMS olarak gönderilecektir.', 'sms-form', 'SMS Gönder', 'bg-blue-600')"
                                class="w-11 h-11 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all shadow-sm" data-tooltip="SMS Gönder">
                            <i class='bx bx-message-square-dots text-xl'></i>
                        </button>

                        <button type="button" 
                                @click="openModal('E-Posta Gönder', '{{ $proposal->customer->company_email }} adresine teklif PDF dosyası e-posta olarak gönderilecektir.', 'email-form', 'E-Posta Gönder', 'bg-rose-600')"
                                class="w-11 h-11 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all shadow-sm" data-tooltip="E-Posta Gönder">
                            <i class='bx bx-envelope text-xl'></i>
                        </button>

                        <button type="button" 
                                @click="openModal('Teklifi Sil', 'Bu teklifi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.', 'delete-form', 'Teklifi Sil', 'bg-red-600')"
                                class="w-11 h-11 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all shadow-sm" data-tooltip="Teklifi Sil">
                            <i class='bx bx-trash text-xl'></i>
                        </button>
                    </div>
                </div>

                <!-- Separator -->
                <div class="hidden lg:block h-8 w-px bg-slate-200 mx-1"></div>

                <!-- Status & Print (Stacked on mobile) -->
                <div class="flex items-center gap-2 justify-end w-full lg:w-auto">
                    <div class="dropdown relative w-full lg:w-auto" x-data="{ open: false }">
                        <button @click="open = !open" class="h-11 px-6 w-full lg:w-auto rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-50 transition-all flex items-center justify-between lg:justify-start gap-2 shadow-sm">
                            <span>Durum: {{ $proposal->status }}</span>
                            <i class='bx bx-chevron-down'></i>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-full lg:w-48 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 z-50" x-cloak x-transition>
                            <form action="{{ route('proposals.update-status', $proposal) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                @foreach(['draft' => 'Taslak', 'pending' => 'Onay Bekliyor', 'approved' => 'Onaylandı', 'rejected' => 'Reddedildi'] as $key => $label)
                                    <button type="submit" name="status" value="{{ $key }}" class="w-full text-left px-4 py-2 text-sm font-bold rounded-xl {{ $proposal->status === $key ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </form>
                        </div>
                    </div>
                    
                    <a href="{{ route('proposals.print', $proposal) }}" target="_blank" class="h-11 px-6 rounded-xl bg-slate-950 text-white text-sm font-bold hover:bg-slate-800 transition-all shadow-lg shadow-slate-200 flex items-center gap-2 whitespace-nowrap">
                        <i class='bx bx-printer'></i> Yazdır
                    </a>
                </div>
            </div>

            <!-- Hidden Forms -->
            <form id="whatsapp-form" action="{{ route('proposals.send-whatsapp', $proposal) }}" method="POST" target="_blank" class="hidden">@csrf</form>
            <form id="sms-form" action="{{ route('proposals.send-sms', $proposal) }}" method="POST" class="hidden">@csrf</form>
            <form id="email-form" action="{{ route('proposals.send-email', $proposal) }}" method="POST" class="hidden">@csrf</form>
            <form id="delete-form" action="{{ route('proposals.destroy', $proposal) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>

            <!-- Action Confirmation Modal -->
            <div x-show="confirmModal" 
                 class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/20 backdrop-blur-sm"
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div @click.away="confirmModal = false" 
                     class="bg-white rounded-[32px] shadow-2xl border border-slate-100 w-full max-w-md p-8 animate-in zoom-in-95 duration-200">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-6" :class="confirmColor + ' bg-opacity-10'">
                            <i class='bx bx-info-circle text-4xl' :class="confirmColor.replace('bg-', 'text-')"></i>
                        </div>
                        
                        <h3 class="text-xl font-black text-slate-950 mb-2" x-text="actionTitle"></h3>
                        <p class="text-slate-500 font-bold mb-8 leading-relaxed" x-text="actionDescription"></p>
                        
                        <div class="grid grid-cols-2 gap-3 w-full">
                            <button @click="confirmModal = false" 
                                    class="h-12 px-6 rounded-2xl bg-slate-50 text-slate-600 font-black uppercase tracking-tight hover:bg-slate-100 transition-all">
                                Vazgeç
                            </button>
                            <button @click="document.getElementById(actionFormId).submit(); confirmModal = false" 
                                    class="h-12 px-6 rounded-2xl text-white font-black uppercase tracking-tight shadow-lg transition-all"
                                    :class="confirmColor + ' hover:opacity-90'"
                                    x-text="confirmText">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Detay Bilgileri -->
        <div class="md:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl p-10 md:p-20 border border-slate-100 shadow-sm">
                <div class="flex justify-between items-start mb-12">
                    <div>
                        <h2 class="text-3xl font-black text-slate-950 mb-2">TEKLİF</h2>
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">{{ $proposal->proposal_number }}</p>
                    </div>
                    <div class="text-right">
                        @php
                            $logoSettings = \App\Models\Setting::whereIn('key', ['proposal_logo', 'company_logo_png', 'company_logo_jpg'])->get()->keyBy('key');
                            $logoPath = $logoSettings['proposal_logo']->value 
                                ?? $logoSettings['company_logo_png']->value 
                                ?? $logoSettings['company_logo_jpg']->value 
                                ?? null;
                        @endphp

                        @if($logoPath)
                            <img src="{{ asset('storage/'.$logoPath) }}" class="h-16 w-auto ml-auto object-contain" alt="Logo">
                        @else
                            <div class="w-16 h-16 bg-slate-950 rounded-2xl flex items-center justify-center ml-auto shadow-xl shadow-slate-200">
                                <i class='bx bxs-bolt text-white text-3xl'></i>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 mb-12">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Kimden (Hazırlayan)</p>
                        <h3 class="font-extrabold text-slate-900 text-lg mb-1">{{ $proposal->user?->tenant?->name ?? (auth()->user()?->tenant?->name ?? config('app.name')) }}</h3>
                        <p class="text-slate-500 text-sm font-medium">{{ $proposal->user?->name ?? (auth()->user()?->name ?? 'Bilinmiyor') }}</p>
                        <p class="text-slate-500 text-sm font-medium">{{ $proposal->user?->email ?? (auth()->user()?->email ?? '-') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Kime</p>
                        <h3 class="font-extrabold text-slate-900 text-lg mb-1">{{ $proposal->customer->company_name }}</h3>
                        <p class="text-slate-500 text-sm font-medium">{{ $proposal->customer->contact_person }}</p>
                        <p class="text-slate-500 text-sm font-medium">{{ $proposal->customer->company_email }}</p>
                        <p class="text-slate-500 text-sm font-medium">{{ $proposal->customer->mobile_phone }}</p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-50 mb-12">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Açıklama</th>
                                    <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Adet</th>
                                    <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Birim Fiyat</th>
                                    <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">KDV</th>
                                    <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Toplam</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($proposal->items as $item)
                                <tr>
                                    <td class="px-4 md:px-6 py-4 font-bold text-slate-800 text-sm">{{ $item->description }}</td>
                                    <td class="px-4 md:px-6 py-4 text-center text-slate-600 text-sm font-bold">{{ number_format($item->quantity, 0) }} {{ $item->unit }}</td>
                                    <td class="px-4 md:px-6 py-4 text-right">
                                        <p class="text-slate-600 text-sm font-bold">{{ number_format($item->unit_price, 2) }} {{ $proposal->currency }}</p>
                                        @if($item->discount_amount > 0)
                                        <p class="text-[10px] text-rose-500 font-bold">-{{ number_format($item->discount_amount, 2) }} İndirim</p>
                                        @endif
                                    </td>
                                    <td class="px-4 md:px-6 py-4 text-right text-slate-400 text-xs font-bold">%{{ number_format($item->tax_rate, 0) }}</td>
                                    <td class="px-4 md:px-6 py-4 text-right text-slate-950 text-sm font-black">{{ number_format($item->total_price, 2) }} {{ $proposal->currency }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Redesigned Payment Summary (Under items) -->
                @php
                    $lineDiscounts = $proposal->items->sum('discount_amount');
                    $grossTotal = $proposal->subtotal + $lineDiscounts;
                @endphp
                <div class="flex justify-end">
                    <div class="w-full md:w-80 space-y-3 pt-6 border-t border-slate-100">
                        <div class="flex justify-between items-center text-slate-500">
                            <span class="text-xs font-bold uppercase tracking-tight">Brüt Toplam</span>
                            <span class="text-sm font-black">{{ number_format($grossTotal, 2) }} {{ $proposal->currency }}</span>
                        </div>

                        @if($lineDiscounts > 0)
                        <div class="flex justify-between items-center text-rose-500 bg-rose-50/50 px-3 py-1.5 rounded-xl border border-rose-100">
                            <span class="text-[10px] font-black uppercase tracking-widest">Satır İndirimleri</span>
                            <span class="text-sm font-bold">-{{ number_format($lineDiscounts, 2) }} {{ $proposal->currency }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between items-center text-slate-800 pt-2">
                            <span class="text-xs font-bold uppercase tracking-tight">Ara Toplam (Net)</span>
                            <span class="text-sm font-black">{{ number_format($proposal->subtotal, 2) }} {{ $proposal->currency }}</span>
                        </div>

                        @if($proposal->discount_amount > 0)
                        <div class="flex justify-between items-center text-rose-500 bg-rose-50/50 px-3 py-1.5 rounded-xl border border-rose-100">
                            <span class="text-[10px] font-black uppercase tracking-widest">Genel İndirim</span>
                            <span class="text-sm font-bold">-{{ number_format($proposal->discount_amount, 2) }} {{ $proposal->currency }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between items-center text-slate-500">
                            <span class="text-xs font-bold uppercase tracking-tight">Toplam KDV</span>
                            <span class="text-sm font-black">{{ number_format($proposal->tax_amount, 2) }} {{ $proposal->currency }}</span>
                        </div>

                        <div class="pt-4 mt-2 border-t-2 border-slate-900 border-dashed animate-pulse"></div>

                        <div class="flex justify-between items-start pt-2">
                            <div>
                                <span class="block text-[10px] font-black text-indigo-500 uppercase tracking-widest leading-none mb-1">Genel Toplam</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">KDV DAHİL FİYAT</span>
                            </div>
                            <span class="text-3xl font-black text-slate-950 tracking-tighter">
                                {{ number_format($proposal->total_amount, 2) }} <span class="text-lg">{{ $proposal->currency }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                @if($proposal->notes)
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Teklif Notları (Müşteriye Gözüken)</p>
                    <div class="p-6 bg-slate-50 rounded-2xl text-slate-600 text-sm leading-relaxed border border-slate-100 whitespace-pre-line font-medium italic">
                        {{ $proposal->notes }}
                    </div>
                </div>
                @endif
            </div>

            <!-- İç Notlar Bölümü (Sadece Ekip Görür) -->
            <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm space-y-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Dahili Notlar</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight mt-1">SADECE EKİP ÜYELERİ GÖREBİLİR</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                        <i class='bx bx-message-detail text-xl'></i>
                    </div>
                </div>

                <!-- Not Ekleme Formu -->
                <form action="{{ route('proposals.store-note', $proposal) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="relative group">
                        <textarea name="note" rows="3" placeholder="Bu teklif hakkında bir not bırakın..." 
                                  class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/5 outline-none transition-all placeholder:text-slate-400 min-h-[100px]"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="h-11 px-6 rounded-xl bg-slate-900 text-white text-xs font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200">
                            Notu Kaydet
                        </button>
                    </div>
                </form>

                <!-- Not Listesi -->
                <div class="space-y-6 pt-4 border-t border-slate-50">
                    @forelse($proposal->internalNotes as $note)
                    <div class="flex gap-4 group">
                        <div class="flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($note->user->name ?? ($note->user_id == auth()->id() ? auth()->user()->name : 'User')) }}&background=f8fafc&color=0f172a" class="w-10 h-10 rounded-xl" alt="">
                        </div>
                        <div class="flex-1 bg-slate-50/50 p-4 rounded-2xl rounded-tl-none border border-slate-100/50 group-hover:bg-white group-hover:border-indigo-100 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-black text-slate-900 uppercase tracking-tight">
                                    {{ $note->user->name ?? ($note->user_id == auth()->id() ? auth()->user()->name : 'Bilinmiyor') }}
                                </span>
                                <span class="text-[10px] font-bold text-slate-400">{{ $note->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs font-medium text-slate-600 leading-relaxed whitespace-pre-line">{{ $note->note }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Henüz dahili bir not eklenmemiş</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Özet Bilgileri -->
        <div class="space-y-8">

         <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm space-y-6">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Zaman Damgası</h3>
                
                <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-400">Teklif Tarihi</span>
                    <span class="text-xs font-black text-slate-700">{{ $proposal->proposal_date->format('d.m.Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-400">Geçerlilik</span>
                    <span class="text-xs font-black text-slate-700">{{ $proposal->valid_until ? $proposal->valid_until->format('d.m.Y') : 'Belirtilmedi' }}</span>

                </div>
                <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-400">Teslim Tarihi</span>
                    <span class="text-xs font-black text-slate-700">{{ $proposal->delivery_date ? $proposal->delivery_date->format('d.m.Y') : 'Belirtilmedi' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-400">Ödeme Türü</span>
                    <span class="text-xs font-black text-slate-700">{{ $proposal->payment_type }}</span>
                </div>
                <div class="pt-4 border-t border-slate-50">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Oluşturan Kullanıcı</p>
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($proposal->user->name ?? 'User') }}&background=f1f5f9&color=0f172a" class="w-8 h-8 rounded-lg" alt="">
                        <div>
                            <p class="text-xs font-black text-slate-900">{{ $proposal->user->name ?? (optional(auth()->user())->name ?? 'Bilinmiyor') }}</p>
                            <p class="text-[10px] font-bold text-slate-400">{{ $proposal->user->email ?? (optional(auth()->user())->email ?? '-') }}</p>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Teklif Geçmişi</h3>
                
                <div class="relative space-y-6">
                    <!-- Timeline Line -->
                    <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-slate-100"></div>

                    @foreach($proposal->activities as $activity)
                    <div class="relative flex gap-4 pl-10">
                        <!-- Dot -->
                        <div class="absolute left-2.5 top-1.5 w-3.5 h-3.5 rounded-full border-2 border-white 
                            @if($activity->activity_type === 'created') bg-emerald-500
                            @elseif($activity->activity_type === 'updated') bg-blue-500
                            @elseif($activity->activity_type === 'status_changed') bg-amber-500
                            @elseif(str_starts_with($activity->activity_type, 'sent_')) bg-indigo-500
                            @else bg-slate-400 @endif">
                        </div>
                        
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-slate-800 leading-snug">{{ $activity->description }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">
                                    {{ $activity->user->name ?? ($proposal->user->name ?? (optional(auth()->user())->name ?? 'Bilinmiyor')) }}
                                </span>
                                <span class="text-[10px] text-slate-300">•</span>
                                <span class="text-[10px] font-bold text-slate-400">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @if($proposal->activities->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-xs text-slate-400 font-medium italic">Henüz bir etkinlik kaydı bulunmuyor.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
