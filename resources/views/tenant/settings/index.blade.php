@extends('tenant.layouts.app')

@section('title', 'Sistem Ayarları')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500" x-data="{ activeTab: 'general', deleteModalOpen: false }">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-indigo-600 shadow-sm flex-shrink-0">
                <i class='bx bx-cog text-2xl'></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">Sistem Ayarları</h1>
                <p class="text-slate-500 text-sm mt-1">Firmanızın genel yapılandırma ayarlarını buradan yönetebilirsiniz.</p>
            </div>
        </div>
        <button type="submit" form="settings-form" class="w-full md:w-auto h-12 md:h-10 px-6 flex items-center justify-center rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
            <i class='bx bx-save text-xl mr-2'></i>
            DEĞİŞİKLİKLERİ KAYDET
        </button>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1 space-y-2">
            <!-- Mobile Dropdown -->
            <div class="lg:hidden relative mb-4">
                <select x-model="activeTab" class="w-full h-14 pl-5 pr-10 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-600/20 shadow-sm transition-all">
                    @foreach(['general', 'logo', 'proposal', 'email', 'sms', 'danger'] as $group)
                        @php
                            if($group !== 'danger' && !$groupedSettings->has($group)) continue; 
                            $groupLabels = [
                                'general' => 'Genel Ayarlar',
                                'logo' => 'Logo Ayarları',
                                'proposal' => 'Teklif Ayarları',
                                'email' => 'E-posta Ayarları',
                                'sms' => 'SMS Ayarları',
                                'danger' => 'Verilerimi ve Hesabımı Sil',
                            ];
                        @endphp
                        <option value="{{ $group }}">{{ $groupLabels[$group] ?? ucfirst($group) }}</option>
                    @endforeach
                </select>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500 bg-white p-1">
                    <i class='bx bx-chevron-down text-xl'></i>
                </div>
            </div>

            <!-- Desktop Sidebar -->
            <div class="hidden lg:block space-y-2">
            @foreach(['general', 'logo', 'proposal', 'email', 'sms', 'danger'] as $group)
                @php
                    if($group !== 'danger' && !$groupedSettings->has($group)) continue; 

                    $groupLabels = [
                        'general' => 'Genel Ayarlar',
                        'logo' => 'Logo Ayarları',
                        'proposal' => 'Teklif Ayarları',
                        'email' => 'E-posta Ayarları',
                        'sms' => 'SMS Ayarları',
                        'danger' => 'Verilerimi ve Hesabımı Sil',
                    ];
                    $label = $groupLabels[$group] ?? ucfirst($group);
                    $icon = match($group) {
                        'general' => 'bx-slider-alt',
                        'logo' => 'bx-image',
                        'proposal' => 'bx-file-blank',
                        'email' => 'bx-envelope',
                        'sms' => 'bx-message-rounded-dots',
                        'danger' => 'bx-error-circle',
                        default => 'bx-customize',
                    };
                @endphp
                <button 
                    @click="activeTab = '{{ $group }}'"
                    :class="activeTab === '{{ $group }}' ? 'bg-white text-indigo-600 shadow-sm border-slate-200' : 'text-slate-500 hover:text-slate-900 hover:bg-white/50 border-transparent'"
                    class="w-full flex items-center gap-3 px-5 py-4 rounded-xl border transition-all duration-200 text-left group"
                >
                    <div :class="activeTab === '{{ $group }}' ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-50 text-slate-400 group-hover:bg-white group-hover:text-slate-600'" 
                         class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                        <i class='bx {{ $icon }} text-lg'></i>
                    </div>
                    <span class="font-bold text-sm">{{ $label }}</span>
                    <i class='bx bx-chevron-right ml-auto text-xl opacity-0 transition-opacity' :class="activeTab === '{{ $group }}' ? 'opacity-100' : ''"></i>
                </button>
            @endforeach
            </div>
        </div>

        <!-- Form Area -->
        <div class="lg:col-span-3">
            <form id="settings-form" action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @foreach($groupedSettings as $group => $settings)
                    <div x-show="activeTab === '{{ $group }}'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         style="display: none;"
                         @if($group === 'email') x-data="{ provider: '{{ $settings->where('key', 'mail_provider')->first()->value ?? 'smtp' }}' }" @endif
                    >
                        
                        <div class="bg-white rounded-md p-4 md:p-8 border border-slate-100 shadow-sm space-y-8">
                            <div class="border-b border-slate-50 pb-6 mb-6">
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">{{ $groupLabels[$group] ?? ucfirst($group) }}</h3>
                                <p class="text-xs text-slate-400 font-bold mt-1">Bu bölümdeki ayarlar {{ strtolower($groupLabels[$group] ?? $group) }} süreçlerini etkiler.</p>
                            </div>

                            @if($group === 'general')
                                <div class="space-y-6">
                                    <div class="space-y-6">
                                        @php $s = $settings->where('key', 'company_name')->first(); @endphp
                                        @if($s)
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">{{ $s->label }}</label>
                                                <input type="text" name="{{ $s->key }}" value="{{ $s->value }}" class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-slate-900">
                                            </div>
                                        @endif
                                        
                                        @php $s = $settings->where('key', 'tax_title')->first(); @endphp
                                        @if($s)
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">{{ $s->label }}</label>
                                                <input type="text" name="{{ $s->key }}" value="{{ $s->value }}" class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-slate-900">
                                            </div>
                                        @endif
                                    </div>

                                    @php $s = $settings->where('key', 'company_address')->first(); @endphp
                                    @if($s)
                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">{{ $s->label }}</label>
                                            <textarea name="{{ $s->key }}" rows="3" class="w-full p-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-slate-900">{{ $s->value }}</textarea>
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        @foreach(['country', 'province', 'district'] as $key)
                                            @php $s = $settings->where('key', $key)->first(); @endphp
                                            @if($s)
                                                <div>
                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">{{ $s->label }}</label>
                                                    <input type="text" name="{{ $s->key }}" value="{{ $s->value }}" class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-slate-900">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach(['tax_office', 'tax_number'] as $key)
                                            @php $s = $settings->where('key', $key)->first(); @endphp
                                            @if($s)
                                                <div>
                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">{{ $s->label }}</label>
                                                    <input type="text" name="{{ $s->key }}" value="{{ $s->value }}" class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-slate-900">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @php $s = $settings->where('key', 'company_phone')->first(); @endphp
                                        @if($s)
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">{{ $s->label }}</label>
                                                <input type="text" name="{{ $s->key }}" value="{{ $s->value }}" class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-slate-900">
                                            </div>
                                        @endif
                                        @php $s = $settings->where('key', 'company_email')->first(); @endphp
                                        @if($s)
                                            <div>
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">{{ $s->label }}</label>
                                                <input type="email" name="{{ $s->key }}" value="{{ $s->value }}" class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-slate-900">
                                            </div>
                                        @endif
                                    </div>

                                    @php $s = $settings->where('key', 'contact_person')->first(); @endphp
                                    @if($s)
                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">{{ $s->label }}</label>
                                            <input type="text" name="{{ $s->key }}" value="{{ $s->value }}" class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-slate-900">
                                        </div>
                                    @endif

                                </div>
                            
                            @elseif($group === 'logo')
                                {{-- Minimal Logo Layout with Delete --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                    @foreach($settings as $setting)
                                        <div class="col-span-1" x-data="{ 
                                            imageUrl: '{{ $setting->value ? asset('uploads/'.$setting->value) : '' }}',
                                            hasImage: {{ $setting->value ? 'true' : 'false' }},
                                            removeFile() {
                                                if(!confirm('Bu logoyu kaldırmak istediğinize emin misiniz?')) return;
                                                
                                                fetch('{{ route('settings.remove-file') }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    body: JSON.stringify({ key: '{{ $setting->key }}' })
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if(data.success) {
                                                        this.imageUrl = '';
                                                        this.hasImage = false;
                                                    }
                                                });
                                            },
                                            previewCore(event) {
                                                const file = event.target.files[0];
                                                if (file) {
                                                    this.imageUrl = URL.createObjectURL(file);
                                                    this.hasImage = true;
                                                }
                                            }
                                        }">
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">{{ $setting->label }}</label>
                                            
                                            <div class="relative group w-full aspect-square">
                                                <!-- Dropzone Area -->
                                                <div class="absolute inset-0 rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 hover:border-indigo-400 transition-colors flex flex-col items-center justify-center cursor-pointer overflow-hidden">
                                                    
                                                    <!-- Preview Image -->
                                                    <template x-if="hasImage">
                                                        <div class="relative w-full h-full flex items-center justify-center p-4 bg-white/50">
                                                            <img :src="imageUrl" class="max-w-full max-h-full object-contain">
                                                        </div>
                                                    </template>

                                                    <!-- Placeholder -->
                                                    <div x-show="!hasImage" class="flex flex-col items-center text-center p-4">
                                                        <i class='bx bx-plus text-3xl text-slate-300 mb-2'></i>
                                                        <span class="text-xs font-bold text-slate-400">Görsel Seç</span>
                                                    </div>

                                                    <!-- Hidden File Input -->
                                                    <input type="file" name="{{ $setting->key }}" @change="previewCore($event)" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                </div>

                                                <!-- Remove Button (Only visible if hasImage) -->
                                                <button type="button" x-show="hasImage" @click="removeFile()" 
                                                    class="absolute -top-2 -right-2 z-20 w-8 h-8 rounded-full bg-red-100 text-red-600 border border-red-200 shadow-sm flex items-center justify-center hover:bg-red-600 hover:text-white transition-all transform hover:scale-110"
                                                    title="Logoyu Kaldır">
                                                    <i class='bx bx-x text-xl font-bold'></i>
                                                </button>
                                            </div>
                                            
                                            <p class="mt-3 text-[10px] text-center text-slate-400 font-medium">{{ $setting->description }}</p>
                                        </div>
                                    @endforeach
                                </div>

                            @elseif($group === 'proposal')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($settings as $setting)
                                        <div>
                                            <div class="flex items-center justify-between mb-2 px-1">
                                                <label for="{{ $setting->key }}" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                    {{ $setting->label }}
                                                </label>
                                            </div>

                                            @if($setting->type === 'boolean')
                                                <div class="relative inline-block w-10 h-5 transition duration-200 ease-in-out rounded-full cursor-pointer group">
                                                    <input type="hidden" name="{{ $setting->key }}" value="0">
                                                    <input type="checkbox" name="{{ $setting->key }}" id="{{ $setting->key }}" value="1" {{ $setting->value ? 'checked' : '' }} 
                                                           class="peer absolute w-0 h-0 opacity-0" />
                                                    <label for="{{ $setting->key }}" class="block overflow-hidden h-5 rounded-full bg-slate-200 cursor-pointer peer-checked:bg-indigo-600 transition-colors duration-200"></label>
                                                    <div class="absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition-transform duration-200 peer-checked:translate-x-5 pointer-events-none shadow-sm"></div>
                                                </div>
                                            @else
                                                <input type="{{ $setting->type }}" name="{{ $setting->key }}" id="{{ $setting->key }}" value="{{ $setting->value }}" 
                                                    class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-slate-900 placeholder:text-slate-400">
                                            @endif

                                            @if($setting->description)
                                                <p class="mt-2 px-1 text-[11px] font-semibold text-slate-400">{{ $setting->description }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                            @else
                                <div class="space-y-6">
                                    @foreach($settings as $setting)
                                        <div @if($group === 'email' && in_array($setting->key, ['mail_host', 'mail_port', 'mail_encryption'])) x-show="provider === 'smtp' || provider === 'custom'" @endif>
                                            <div class="flex items-center justify-between mb-2 px-1">
                                                <label for="{{ $setting->key }}" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                    {{ $setting->label }}
                                                </label>
                                                
                                                @if($setting->type === 'boolean')
                                                    <div class="relative inline-block w-10 h-5 transition duration-200 ease-in-out rounded-full cursor-pointer group">
                                                        <input type="hidden" name="{{ $setting->key }}" value="0">
                                                        <input type="checkbox" name="{{ $setting->key }}" id="{{ $setting->key }}" value="1" {{ $setting->value ? 'checked' : '' }} 
                                                               class="peer absolute w-0 h-0 opacity-0" />
                                                        <label for="{{ $setting->key }}" class="block overflow-hidden h-5 rounded-full bg-slate-200 cursor-pointer peer-checked:bg-indigo-600 transition-colors duration-200"></label>
                                                        <div class="absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition-transform duration-200 peer-checked:translate-x-5 pointer-events-none shadow-sm"></div>
                                                    </div>
                                                @endif
                                            </div>

                                            @if($setting->type === 'text' || $setting->type === 'number' || $setting->type === 'password' || $setting->type === 'email')
                                                <input type="{{ $setting->type }}" name="{{ $setting->key }}" id="{{ $setting->key }}" value="{{ $setting->value }}" 
                                                    class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-slate-900 placeholder:text-slate-400">
                                            
                                            @elseif($setting->type === 'textarea')
                                                <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" rows="4" 
                                                    class="w-full p-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-slate-900 placeholder:text-slate-400">{{ $setting->value }}</textarea>
                                            
                                            @elseif($setting->type === 'select')
                                                <div class="relative">
                                                    <select name="{{ $setting->key }}" id="{{ $setting->key }}" 
                                                        @if($setting->key === 'mail_provider') x-model="provider" @endif
                                                        class="w-full h-12 px-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all text-slate-900 appearance-none">
                                                        @if($setting->key === 'mail_provider')
                                                            <option value="smtp">SMTP (Özel)</option>
                                                            <option value="yandex">Yandex Mail</option>
                                                            <option value="gmail">Gmail (Google Workspace)</option>
                                                            <option value="mailgun">Mailgun</option>
                                                        @elseif($setting->key === 'mail_encryption')
                                                            <option value="tls" {{ $setting->value == 'tls' ? 'selected' : '' }}>TLS</option>
                                                            <option value="ssl" {{ $setting->value == 'ssl' ? 'selected' : '' }}>SSL</option>
                                                            <option value="null" {{ $setting->value == 'null' ? 'selected' : '' }}>Yok</option>
                                                        @else
                                                            <option value="{{ $setting->value }}">{{ $setting->value }}</option>
                                                        @endif
                                                    </select>
                                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                                        <i class='bx bx-chevron-down text-xl'></i>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($setting->description)
                                                <p class="mt-2 px-1 text-[11px] font-semibold text-slate-400">{{ $setting->description }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
                
                <!-- Danger Zone Content -->
                <div x-show="activeTab === 'danger'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     style="display: none;">
                    
                    <div class="bg-white rounded-md p-4 md:p-8 border border-red-100 shadow-sm space-y-8 relative overflow-hidden">
                        <!-- Striped Background -->
                        <div class="absolute top-0 left-0 w-full h-1 bg-[repeating-linear-gradient(45deg,#fee2e2,#fee2e2_10px,#fff_10px,#fff_20px)]"></div>

                        <div class="border-b border-red-50 pb-6 mb-6">
                            <h3 class="text-sm font-black text-rose-600 uppercase tracking-widest flex items-center gap-2">
                                <i class='bx bx-error-circle text-lg'></i> Verilerimi ve Hesabımı Sil
                            </h3>
                            <p class="text-xs text-slate-400 font-bold mt-1">Bu alandaki işlemler geri alınamaz. Lütfen dikkatli işlem yapın.</p>
                        </div>

                        <div class="bg-rose-50 rounded-2xl p-6 border border-rose-100 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div>
                                <h4 class="font-bold text-rose-950 text-base mb-1">Hesabı ve Tüm Verileri Sil</h4>
                                <p class="text-xs text-rose-800/80 leading-relaxed max-w-xl">
                                    Hesabınızı sildiğinizde, şirketinize ait tüm yapılandırmalar, abonelik bilgileri ve kullanıcı erişimleri kalıcı olarak kaldırılır. 
                                    <strong>Sipariş geçmişiniz ve fatura detaylarınız yasal zorunluluklar gereği saklanmaya devam edecektir.</strong>
                                </p>
                            </div>
                            
                            <button type="button" 
                                    @click="deleteModalOpen = true"
                                    class="flex-shrink-0 px-6 py-3 bg-white border-2 border-rose-200 text-rose-600 rounded-xl font-bold text-sm hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm">
                                <i class='bx bx-trash text-lg mr-2 align-middle'></i>
                                HESABIMI SİL
                            </button>
                        </div>
                    </div>
                </div>

            </form>

            <!-- Hidden Delete Form -->
            <form id="delete-account-form" action="{{ route('settings.delete-account') }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <!-- Custom Delete Confirmation Modal -->
    <div x-show="deleteModalOpen" 
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" 
         x-cloak
         style="display: none;">
        
        <div @click.away="deleteModalOpen = false"
             x-show="deleteModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 p-0 max-w-md w-full relative overflow-hidden">
            
            <div class="bg-rose-600 p-8 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
                <!-- Warning Icon -->
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mb-4 mx-auto border border-white/30">
                    <i class='bx bx-error text-4xl text-white'></i>
                </div>
                <h3 class="text-2xl font-black text-white mb-2">Hesabınızı Siliyor Musunuz?</h3>
                <p class="text-rose-100 text-sm font-medium">Bu işlem geri alınamaz ve tüm verileriniz silinir.</p>
            </div>
            
            <div class="p-8">
                <div class="bg-rose-50 border border-rose-100 rounded-xl p-4 mb-6 flex items-start gap-4">
                    <i class='bx bxs-info-circle text-2xl text-rose-600'></i>
                    <div>
                        <h4 class="font-bold text-rose-950 text-sm mb-1">Kalıcı Veri Kaybı</h4>
                        <p class="text-xs text-rose-800/80 leading-relaxed">
                            Hesabınızı sildiğinizde sipariş geçmişi hariç tüm verileriniz, kullanıcılarınız ve ayarlarınız <strong>kalıcı olarak</strong> silinecektir.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" 
                            @click="deleteModalOpen = false" 
                            class="flex-1 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition-colors">
                        Vazgeç
                    </button>
                    <button type="button" 
                            onclick="document.getElementById('delete-account-form').submit()"
                            class="flex-1 py-3 bg-rose-600 text-white rounded-xl font-bold text-sm hover:bg-rose-700 transition-colors text-center shadow-lg shadow-rose-200">
                        Evet, Hesabımı Sil
                    </button>
                </div>
        </div>
    </div>
</div>
@endsection
