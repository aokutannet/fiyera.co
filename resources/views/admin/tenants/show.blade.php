@extends('admin.layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Header -->
    <div>
        <a href="{{ route('admin.tenants.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-slate-600 mb-4 transition-colors">
            <i class='bx bx-arrow-back'></i> Firmalara Dön
        </a>
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-indigo-200">
                    {{ substr($tenant->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $tenant->name }}</h1>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-slate-500 font-bold text-sm">ID: #{{ $tenant->id }}</span>
                        @if($tenant->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase bg-slate-100 text-slate-500 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Pasif
                            </span>
                        @endif
                        <span class="text-slate-300">|</span>
                        <span class="text-slate-500 text-xs font-medium">Kayıt: {{ $tenant->created_at->format('d.m.Y') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.tenants.impersonate', $tenant) }}" method="POST">
                    @csrf
                    <button type="submit" class="h-10 px-4 flex items-center gap-2 rounded-xl bg-slate-900 text-white border border-slate-900 text-xs font-bold hover:bg-slate-800 hover:shadow-lg hover:shadow-slate-900/20 transition-all">
                        <i class='bx bx-log-in-circle text-lg'></i> Yönetici Olarak Gir
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tenant Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:border-indigo-100 transition-all">
            <div class="absolute right-0 top-0 p-6 opacity-10 group-hover:opacity-20 transition-all">
                <i class='bx bx-user text-6xl text-indigo-600'></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">Kullanıcı Sayısı</p>
            <div class="relative z-10">
                <h3 class="text-3xl font-black text-slate-900">{{ $usersCount }}</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:border-indigo-100 transition-all">
            <div class="absolute right-0 top-0 p-6 opacity-10 group-hover:opacity-20 transition-all">
                <i class='bx bx-group text-6xl text-emerald-600'></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">Toplam Müşteri</p>
            <div class="relative z-10">
                <h3 class="text-3xl font-black text-slate-900">{{ $customerCount }}</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:border-indigo-100 transition-all">
            <div class="absolute right-0 top-0 p-6 opacity-10 group-hover:opacity-20 transition-all">
                <i class='bx bx-file text-6xl text-blue-600'></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">Oluşturulan Teklif</p>
            <div class="relative z-10">
                <h3 class="text-3xl font-black text-slate-900">{{ $proposalCount }}</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:border-indigo-100 transition-all">
            <div class="absolute right-0 top-0 p-6 opacity-10 group-hover:opacity-20 transition-all">
                <i class='bx bx-cube text-6xl text-purple-600'></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">Tanımlı Ürün</p>
            <div class="relative z-10">
                <h3 class="text-3xl font-black text-slate-900">{{ $productCount }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Settings and Orders -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Edit Settings Form -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                    <h2 class="text-base font-black text-slate-900">Firma Bilgileri & Ayarlar</h2>
                    <span class="text-xs font-medium text-slate-400">Son güncelleme: {{ $tenant->updated_at->diffForHumans() }}</span>
                </div>
                
                <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Firma Adı</label>
                            <input type="text" value="{{ $tenant->name }}" disabled class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-500 cursor-not-allowed">
                            <p class="text-[10px] text-slate-400 mt-1">Firma adı yasal nedenlerle kilitlenmiştir.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hesap Durumu</label>
                            <select name="status" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500">
                                <option value="active" {{ $tenant->status === 'active' ? 'selected' : '' }}>Aktif - Hizmet Veriliyor</option>
                                <option value="passive" {{ $tenant->status === 'passive' ? 'selected' : '' }}>Pasif - Hesabı Dondur</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-2">
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Abonelik Paketi</label>
                         <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($plans as $plan)
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="subscription_plan_id" value="{{ $plan->id }}" class="peer sr-only" {{ $tenant->subscription_plan_id == $plan->id ? 'checked' : '' }}>
                                <div class="p-4 rounded-xl border border-slate-200 bg-white peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 hover:border-slate-300 transition-all h-full relative overflow-hidden">
                                    <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <i class='bx bxs-check-circle text-indigo-500 text-lg'></i>
                                    </div>
                                    <div class="flex flex-col h-full justify-between">
                                        <div>
                                            <span class="text-sm font-bold text-slate-900 block mb-1">{{ $plan->name }}</span>
                                            <p class="text-[11px] text-slate-500 leading-snug">{{ $plan->description ?? 'Özellikler paketi' }}</p>
                                        </div>
                                        <p class="text-sm font-bold text-slate-900 mt-3 pt-3 border-t border-slate-100">₺{{ number_format($plan->price_monthly, 0) }}<span class="text-xs font-normal text-slate-500">/ay</span></p>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                         </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-50">
                        <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/10 flex items-center gap-2">
                            <i class='bx bx-save'></i> Değişiklikleri Kaydet
                        </button>
                    </div>
                </form>
            </div>

            <!-- Payment History -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                    <h2 class="text-base font-black text-slate-900">Sipariş Geçmişi</h2>
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-xs font-bold text-slate-600">{{ $attempts->count() }}</span>
                </div>
                <div class="overflow-x-auto max-h-[400px] overflow-y-auto custom-scrollbar">
                    <table class="w-full text-left">
                        <thead class="sticky top-0 bg-white z-10 shadow-sm">
                            <tr class="bg-slate-50/50 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Tarih</th>
                                <th class="px-6 py-4">Paket</th>
                                <th class="px-6 py-4">Tutar</th>
                                <th class="px-6 py-4">Periyot</th>
                                <th class="px-6 py-4">Durum</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($attempts as $attempt)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-xs font-bold text-slate-900">{{ $attempt->created_at->format('d.m.Y') }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $attempt->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-slate-900">{{ $attempt->plan->name ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-slate-900">₺{{ number_format($attempt->price, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-slate-500 uppercase">{{ $attempt->billing_period === 'yearly' ? 'Yıllık' : 'Aylık' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($attempt->status === 'success')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <i class='bx bxs-check-circle'></i> Başarılı
                                        </span>
                                    @elseif($attempt->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase bg-amber-50 text-amber-600 border border-amber-100">
                                            <i class='bx bxs-watch'></i> Bekliyor
                                        </span>
                                    @else
                                        <div class="flex flex-col">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase bg-red-50 text-red-600 border border-red-100 w-fit">
                                                <i class='bx bxs-error-circle'></i> Başarısız
                                            </span>
                                            @if($attempt->error_message)
                                                <span class="text-[10px] text-red-400 mt-1 max-w-[150px] truncate" title="{{ $attempt->error_message }}">{{ $attempt->error_message }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class='bx bx-history text-xl text-slate-400'></i>
                                    </div>
                                    <p class="text-sm font-medium text-slate-500">Henüz işlem kaydı bulunmuyor.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Info & Actions -->
        <div class="space-y-6">
            
            @php
                $owner = $tenant->users()->where('is_owner', true)->first() ?? $tenant->users()->first();
            @endphp

            <!-- Owner Info Card -->
            @if($owner)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5">
                    <i class='bx bxs-crown text-8xl text-indigo-900'></i>
                </div>
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 relative z-10">Firma Sahibi</h3>
                <div class="flex items-start gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-lg">
                        {{ substr($owner->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="text-sm font-bold text-slate-900 truncate" title="{{ $owner->name }}">{{ $owner->name }}</h4>
                        <p class="text-xs text-slate-500 truncate mt-1" title="{{ $owner->email }}">{{ $owner->email }}</p>
                        @if($owner->phone)
                            <p class="text-xs text-slate-400 mt-1"><i class='bx bx-phone'></i> {{ $owner->phone }}</p>
                        @endif
                        <div class="mt-3 flex items-center gap-2">
                             <a href="mailto:{{ $owner->email }}" class="p-1.5 rounded-lg bg-slate-50 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                <i class='bx bx-envelope'></i>
                             </a>
                             <span class="text-[10px] text-slate-400 font-medium px-2 py-1 bg-slate-50 rounded-md">
                                Kayıt: {{ $owner->created_at->format('d.m.Y') }}
                             </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif



            <!-- Onboarding Details Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                 <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Onboarding (Wizard) Bilgileri</h3>
                 @if(empty($tenant->onboarding_data))
                    <p class="text-xs text-slate-400 italic">Henüz onboarding verisi bulunmuyor.</p>
                 @else
                     <div class="space-y-4">
                        @foreach($tenant->onboarding_data as $data)
                        <div class="border-b border-slate-50 last:border-0 pb-2 last:pb-0">
                             <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">{{ $data['label'] ?? $data['key'] }}</span>
                             <span class="text-xs font-bold text-slate-700 block whitespace-pre-wrap">{{ is_array($data['value']) ? implode(', ', $data['value']) : $data['value'] }}</span>
                        </div>
                        @endforeach
                     </div>
                 @endif
            </div>

            <!-- Billing Details Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                 <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Fatura Bilgileri</h3>
                 @php
                    // Use data passed from controller (Settings Priority)
                    $billing = $billingDetails ?? ($tenant->billing_details ?? []);
                 @endphp
                 
                 @if(empty($billing) || empty($billing['company_name']))
                    <p class="text-xs text-slate-400 italic">Henüz fatura bilgisi girilmemiş.</p>
                 @else
                     <div class="space-y-4">
                        @if(!empty($billing['company_name']))
                        <div>
                             <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Firma Ünvanı</span>
                             <span class="text-xs font-bold text-slate-700 block">{{ $billing['company_name'] }}</span>
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            @if(!empty($billing['tax_office']))
                            <div>
                                 <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Vergi Dairesi</span>
                                 <span class="text-xs font-bold text-slate-700">{{ $billing['tax_office'] }}</span>
                            </div>
                            @endif
                            @if(!empty($billing['tax_number']))
                            <div>
                                 <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Vergi No</span>
                                 <span class="text-xs font-bold text-slate-700 font-mono">{{ $billing['tax_number'] }}</span>
                            </div>
                            @endif
                        </div>

                        @if(!empty($billing['address']))
                        <div>
                             <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Adres</span>
                             <p class="text-xs font-medium text-slate-600 leading-relaxed">{{ $billing['address'] }}</p>
                             <p class="text-xs font-bold text-slate-700 mt-1">
                                {{ $billing['district'] ?? '' }} / {{ $billing['city'] ?? '' }} {{ isset($billing['country']) ? '('.$billing['country'].')' : '' }}
                             </p>
                        </div>
                        @endif
                     </div>
                 @endif
            </div>

            <!-- System Details Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                 <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Teknik Bilgiler</h3>
                 <div class="space-y-4">
                   
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                             <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Oluşturulma</span>
                             <span class="text-xs font-bold text-slate-700">{{ $tenant->created_at->format('d.m.Y') }}</span>
                        </div>
                        <div>
                             <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Son İşlem</span>
                             <span class="text-xs font-bold text-slate-700">{{ $tenant->updated_at->format('d.m.Y') }}</span>
                        </div>
                    </div>
                 </div>
            </div>

            <!-- Danger Zone -->
            <div x-data="{ showDeleteModal: false }" class="bg-rose-50/50 rounded-2xl border border-rose-100 p-6">
                <h3 class="text-xs font-black text-rose-600 uppercase tracking-widest mb-3">Tehlikeli Bölge</h3>
                <p class="text-[11px] text-rose-900/60 mb-4 leading-relaxed">
                    Firmanın silinmesi geri alınamaz. Firma veritabanı ve tüm yedekler yok edilecektir.
                </p>
                
                <button @click="showDeleteModal = true" type="button" class="w-full py-2.5 bg-white border border-rose-200 text-rose-600 text-xs font-bold rounded-xl hover:bg-rose-600 hover:text-white transition-all">
                    Firmayı Kalıcı Olarak Sil
                </button>

                <!-- Custom Modal Overlay -->
                <div x-show="showDeleteModal" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm" 
                     x-cloak>
                    
                    <div @click.away="showDeleteModal = false"
                         class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden relative">
                         
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class='bx bx-alarm-exclamation text-3xl text-rose-600'></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-2">Firmayı Silmek İstiyor musunuz?</h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-8">
                                <strong>{{ $tenant->name }}</strong> firmasına ait veritabanı ve tüm kullanıcı verileri sunucudan kalıcı olarak silinecektir.
                            </p>

                            <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" class="grid grid-cols-2 gap-3">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="showDeleteModal = false" class="py-2.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-200 transition-all">
                                    Vazgeç
                                </button>
                                <button type="submit" class="py-2.5 bg-rose-600 text-white text-xs font-bold rounded-xl hover:bg-rose-700 transition-all shadow-lg shadow-rose-200">
                                    Evet, Sil
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
