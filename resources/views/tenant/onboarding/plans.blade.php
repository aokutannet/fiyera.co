@extends('tenant.layouts.onboarding')

@section('content')
<!-- Decorative BG -->
<div class="fixed inset-0 z-0 bg-white"></div>

<!-- Progress Bar -->
<div class="fixed top-0 left-0 w-full h-1 z-[60]">
    <div class="h-full bg-indigo-600 w-full"></div>
</div>

<div class="relative z-10 min-h-screen py-16 px-4 font-['Plus_Jakarta_Sans']"
     x-data="{ 
        billing: 'monthly',
        period() { return this.billing === 'yearly' ? '/yıl' : '/ay'; }
     }">
    @if(isset($selectedPlan) && $selectedPlan)
        <div class="max-w-7xl mx-auto">
            <!-- Back Link -->
            <a href="{{ route('subscription.upgrade') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-900 font-bold text-sm mb-6 transition-colors group">
                <i class='bx bx-arrow-back text-lg group-hover:-translate-x-1 transition-transform'></i>
                <span>{{ __('Paketlere Geri Dön') }}</span>
            </a>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-start gap-3">
                    <i class='bx bxs-error-circle text-xl shrink-0 mt-0.5'></i>
                    <ul class="list-disc list-inside text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    
                    <!-- Left Column: Form Area -->
                    <div class="p-6 md:p-10 lg:p-12 bg-white">
                        <form id="payment-form" action="{{ route('subscription.store') }}" method="POST" class="space-y-10">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $selectedPlan->id }}">
                            <input type="hidden" name="billing_period" value="{{ request('billing', 'monthly') }}">

                            <!-- Invoice Details Section -->
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                                    <i class='bx bxs-business text-indigo-600'></i>
                                    {{ __('Fatura Bilgileri') }}
                                </h2>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <!-- Company Name -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Firma Ünvanı') }} <span class="text-red-500">*</span></label>
                                        <input type="text" name="company_name" required value="{{ $billingDetails['company_name'] ?? '' }}" placeholder="{{ __('Şirket Tam Adı') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-900 font-bold placeholder:text-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    </div>

                                    <!-- Tax Info -->
                                    <div>
                                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Vergi Dairesi') }} <span class="text-red-500">*</span></label>
                                         <input type="text" name="tax_office" required value="{{ $billingDetails['tax_office'] ?? '' }}" placeholder="{{ __('Örn: Maslak') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-900 font-medium placeholder:text-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    </div>
                                    <div>
                                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Vergi Numarası') }} <span class="text-red-500">*</span></label>
                                         <input type="text" name="tax_number" required value="{{ $billingDetails['tax_number'] ?? '' }}" placeholder="{{ __('10 Haneli Vergi No') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-900 font-medium placeholder:text-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    </div>

                                    <!-- Address -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Fatura Adresi') }} <span class="text-red-500">*</span></label>
                                        <textarea name="address" required rows="2" placeholder="{{ __('Mahalle, Cadde, Sokak, No...') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-900 font-medium placeholder:text-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all resize-none">{{ $billingDetails['address'] ?? '' }}</textarea>
                                    </div>

                                    <!-- Location -->
                                    <div>
                                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('İl') }} <span class="text-red-500">*</span></label>
                                         <input type="text" name="city" required value="{{ $billingDetails['city'] ?? '' }}" placeholder="{{ __('İstanbul') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-900 font-medium placeholder:text-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    </div>
                                    <div>
                                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('İlçe') }} <span class="text-red-500">*</span></label>
                                         <input type="text" name="district" required value="{{ $billingDetails['district'] ?? '' }}" placeholder="{{ __('Kadıköy') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-900 font-medium placeholder:text-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    </div>
                                    <div>
                                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('İlçe') }}</label>
                                         <input type="text" name="district" value="{{ $billingDetails['district'] ?? '' }}" placeholder="{{ __('Kadıköy') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-900 font-medium placeholder:text-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Ülke') }}</label>
                                        <select name="country" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-900 font-medium shadow-sm outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all appearance-none cursor-pointer">
                                            <option value="TR" {{ ($billingDetails['country'] ?? 'TR') == 'TR' ? 'selected' : '' }}>{{ __('Türkiye') }}</option>
                                            <option value="US" {{ ($billingDetails['country'] ?? '') == 'US' ? 'selected' : '' }}>{{ __('United States') }}</option>
                                            <option value="EU" {{ ($billingDetails['country'] ?? '') == 'EU' ? 'selected' : '' }}>{{ __('Europe') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="h-px bg-slate-100"></div>

                            <!-- Payment Details Section -->
                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                        <i class='bx bxs-credit-card text-indigo-600'></i>
                                        {{ __('Ödeme Bilgileri') }}
                                    </h2>
                                    <div class="flex gap-1 opacity-60">
                                        <i class='bx bxl-visa text-2xl text-slate-600'></i>
                                        <i class='bx bxl-mastercard text-2xl text-slate-600'></i>
                                    </div>
                                </div>
                                
                                <!-- Email Field -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('E-posta Adresi') }}</label>
                                    <div class="relative">
                                        <input type="email" value="{{ auth()->user()->email }}" readonly class="w-full bg-slate-50 border border-slate-200 text-slate-500 font-medium rounded-lg px-4 py-3 outline-none cursor-not-allowed">
                                        <i class='bx bx-check-circle absolute right-4 top-3.5 text-emerald-500 text-lg'></i>
                                    </div>
                                </div>

                                <!-- Card Information -->
                                <div class="space-y-4" x-data="{
                                    formatCardNumber(e) {
                                        let value = e.target.value.replace(/\D/g, '');
                                        value = value.substring(0, 16);
                                        let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
                                        e.target.value = formatted;
                                    },
                                    formatExpiry(e) {
                                        let value = e.target.value.replace(/\D/g, '');
                                        if (value.length >= 2) {
                                            value = value.substring(0, 2) + ' / ' + value.substring(2, 4);
                                        }
                                        e.target.value = value.substring(0, 7);
                                    },
                                    formatCVC(e) {
                                        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
                                    },
                                    onlyLetters(e) {
                                        e.target.value = e.target.value.replace(/[^a-zA-Z\sğüşıöçĞÜŞİÖÇ]/g, '');
                                    }
                                }">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Kart Bilgileri') }} <span class="text-red-500">*</span></label>
                                        <div class="border border-slate-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-all shadow-sm">
                                            <div class="relative border-b border-slate-100">
                                                <input type="text" name="card_number" required maxlength="19" @input="formatCardNumber" placeholder="{{ __('Kart Numarası') }}" class="w-full bg-white px-4 py-3.5 text-slate-900 font-bold placeholder:text-slate-300 outline-none">
                                                <div class="absolute right-4 top-3.5 text-slate-400">
                                                    <i class='bx bx-credit-card text-xl'></i>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 divide-x divide-slate-100">
                                                <input type="text" name="expiry" required maxlength="7" @input="formatExpiry" placeholder="AA / YY" class="w-full bg-white px-4 py-3.5 text-slate-900 font-bold placeholder:text-slate-300 outline-none text-center">
                                                <input type="text" name="cvc" required maxlength="4" @input="formatCVC" placeholder="CVC" class="w-full bg-white px-4 py-3.5 text-slate-900 font-bold placeholder:text-slate-300 outline-none text-center">
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Kart Üzerindeki İsim') }} <span class="text-red-500">*</span></label>
                                        <input type="text" name="holder_name" required @input="onlyLetters" placeholder="{{ __('Ad Soyad') }}" class="w-full bg-white border border-slate-200 rounded-lg px-4 py-3 text-slate-900 font-bold placeholder:text-slate-300 outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm">
                                    </div>
                                </div>

                                @php
                                    $basePrice = request('billing') === 'yearly' ? $selectedPlan->price_yearly : $selectedPlan->price_monthly;
                                    $finalPrice = max(0, $basePrice - ($prorationDiscount ?? 0));
                                @endphp
                                <button type="submit" class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-xl shadow-slate-900/10 transform active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                                    <span>{{ number_format($finalPrice, 2) }} ₺ {{ __('Güvenli Öde') }}</span>
                                    <i class='bx bx-lock-alt text-lg'></i>
                                </button>
                                
                                <div class="text-center">
                                    <p class="text-[10px] text-slate-400 font-medium">
                                        {{ __('Ödemeniz 256-bit SSL şifreleme ile güvence altındadır.') }}
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Right Column: Order Summary -->
                    <div class="bg-indigo-50/50 p-6 md:p-10 lg:p-12 border-t md:border-t-0 md:border-l border-slate-100 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-6">{{ __('Sipariş Özeti') }}</h3>
                            
                            <div class="flex gap-4 mb-8">
                                <div class="w-16 h-16 bg-white rounded-xl shadow-sm border border-indigo-100 flex items-center justify-center text-indigo-600 text-2xl">
                                    <i class='bx bxs-zap'></i>
                                </div>
                                <div>
                                    <div class="text-lg font-black text-slate-900">{{ $selectedPlan->name }}</div>
                                    <div class="text-sm font-medium text-slate-500">{{ request('billing') === 'yearly' ? __('Yıllık Plan') : __('Aylık Plan') }}</div>
                                </div>
                            </div>

                            <!-- Subscription Details Block -->
                            <div class="bg-indigo-100/50 rounded-xl p-5 border border-indigo-100 mb-6">
                                <h4 class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider mb-3">{{ __('Abonelik Süresi') }}</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-slate-600 font-medium">{{ __('Başlangıç') }}</span>
                                        <span class="font-bold text-slate-900">{{ now()->translatedFormat('d F Y') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-slate-600 font-medium">{{ __('Bitiş') }}</span>
                                        <span class="font-bold text-slate-900">
                                            {{ request('billing') === 'yearly' ? now()->addMonths(15)->translatedFormat('d F Y') : now()->addMonth()->translatedFormat('d F Y') }}
                                        </span>
                                    </div>
                                    
                                    @if(request('billing') === 'yearly')
                                        <div class="pt-2 mt-2 border-t border-indigo-200/50 flex justify-between items-center">
                                            <div class="flex items-center gap-1.5 text-xs font-bold text-indigo-600">
                                                <i class='bx bxs-gift'></i>
                                                <span>{{ __('+3 Ay Hediye') }}</span>
                                            </div>
                                            <span class="bg-indigo-600 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">{{ __('AKTİF') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex justify-between text-sm text-slate-600 font-medium">
                                    <span>{{ __('Paket Bedeli') }}</span>
                                    <span>₺{{ number_format(request('billing') === 'yearly' ? $selectedPlan->price_yearly : $selectedPlan->price_monthly, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm text-slate-600 font-medium">
                                    <span>{{ __('İşlem Ücreti') }}</span>
                                    <span class="text-green-600">{{ __('Ücretsiz') }}</span>
                                </div>
                                @if(request('billing') === 'yearly')
                                    <div class="flex justify-between text-sm text-indigo-600 font-bold bg-indigo-50 p-2 rounded-lg border border-indigo-100">
                                        <span>{{ __('Yıllık Avantaj') }}</span>
                                        <span>{{ __('/%20 İndirim') }}</span>
                                    </div>
                                @endif
                                @if(isset($prorationDiscount) && $prorationDiscount > 0)
                                    <div class="flex justify-between text-sm text-emerald-600 font-bold bg-emerald-50 p-2 rounded-lg border border-emerald-100">
                                        <span>{{ __('Kalan Süre İndirimi') }}</span>
                                        <span>-₺{{ number_format($prorationDiscount, 2) }}</span>
                                    </div>
                                @endif
                                <div class="h-px bg-slate-200"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-baseline mb-2">
                                <span class="text-base font-bold text-slate-700">{{ __('Toplam Ödenecek') }}</span>
                                <span class="text-3xl font-black text-slate-900 tracking-tight">
                                    @php
                                        $basePrice = request('billing') === 'yearly' ? $selectedPlan->price_yearly : $selectedPlan->price_monthly;
                                        $finalPrice = max(0, $basePrice - ($prorationDiscount ?? 0));
                                    @endphp
                                    ₺{{ number_format($finalPrice, 2) }}
                                </span>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Trust Badges -->
            <div class="mt-8 flex justify-center gap-6 opacity-40 grayscale hover:grayscale-0 transition-all duration-500">
                <i class='bx bxl-visa text-3xl'></i>
                <i class='bx bxl-mastercard text-3xl'></i>
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed text-center">Ödemeyi gerçekleştirerek <a href="#" class="text-indigo-600 font-bold hover:underline">{{ __('Hizmet Şartları') }}</a>'nı kabul etmiş olursunuz.</p>        
        </div>

    @else
        <!-- ============================================== -->
        <!-- PLAN SELECTION STATE -->
        <!-- ============================================== -->
        
        @if(request()->routeIs('subscription.upgrade') || request()->routeIs('subscription.plans'))
            <div class="max-w-[1400px] mx-auto mb-8 text-center">
                 <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-900 font-bold text-sm transition-colors group">
                    <i class='bx bx-arrow-back text-lg group-hover:-translate-x-1 transition-transform'></i>
                    <span>{{ __('Panele Dön') }}</span>
                </a>
            </div>
        @endif

        <!-- Hero Header -->
        <div class="text-center max-w-4xl mx-auto mb-16">
            <h1 class="text-xl md:text-xl font-extrabold text-[#2e2e2e] mb-6 tracking-tight">
                {{ (request()->routeIs('subscription.upgrade') || request()->routeIs('subscription.plans')) ? __('Paketinizi Yükseltin') : __('İhtiyacınıza en uygun planı seçin') }}
            </h1>
            
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg max-w-2xl mx-auto">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(request()->routeIs('subscription.upgrade') || request()->routeIs('subscription.plans'))
                <p class="text-slate-500 font-medium text-lg">
                    {{ __('İhtiyacınıza en uygun planı seçerek hemen kullanmaya başlayın.') }}
                </p>
                <p class="text-slate-500 font-medium mb-10 text-lg">
                    <span class="font-bold text-indigo-600">{{ __('İstediğiniz zaman iptal edebilirsiniz.') }}</span> 
                </p>
            @else
                <p class="text-slate-500 font-medium  text-lg">
                    {{ __('Tüm paketlerde') }} <span class="font-bold text-indigo-600">{{ __('14 gün boyunca ücretsiz') }}</span> {{ __('deneme hakkınız var.') }} 
                </p>
                 <p class="text-slate-500 font-medium mb-10 text-lg">
                   <span class="font-bold text-indigo-600">{{ __('Üstelik kart bilgisi gerekmez!') }}</span> 
                </p>
            @endif

            <!-- Hostinger-style Toggle -->
            <div class="inline-flex bg-slate-100 p-1.5 rounded-lg">
                <button 
                    @click="billing = 'monthly'"
                    class="px-6 py-2.5 rounded-md text-sm font-bold transition-all"
                    :class="billing === 'monthly' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                >
                    {{ __('Aylık') }}
                </button>
                <button 
                    @click="billing = 'yearly'"
                    class="px-6 py-2.5 rounded-md text-sm font-bold transition-all relative"
                    :class="billing === 'yearly' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                >
                    {{ __('Tasarruf Edin (Yıllık)') }}
                </button>
            </div>
        </div>

        <!-- Pricing Cards Container -->
        <div class="max-w-[1400px] mx-auto grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 items-start">
            
            @foreach($plans as $plan)
                <!-- Card -->
                <div class="relative flex flex-col bg-white rounded-2xl transition-all duration-300
                    {{ $plan->is_popular 
                        ? 'border-2 border-indigo-600 shadow-xl overflow-hidden pt-0 mt-0 z-10' 
                        : 'border border-slate-200 pt-8 hover:shadow-lg' 
                    }}
                ">
                    <!-- Popular Header -->
                    @if($plan->is_popular)
                        <div class="bg-indigo-600 text-white text-center py-2.5 text-xs font-bold uppercase tracking-widest mb-6">
                            {{ __('En Çok Tercih Edilen') }}
                        </div>
                    @endif

                    <div class="px-6 pb-8 flex flex-col">
                        
                        <!-- Discount Badge -->
                        <div class="mb-4 flex flex-col gap-1 min-h-[50px]">
                            @php
                                $monthlyTotal = $plan->price_monthly * 12;
                                $discountPct = $monthlyTotal > 0 ? round((($monthlyTotal - $plan->price_yearly) / $monthlyTotal) * 100) : 0;
                            @endphp
                            
                            <div class="self-start px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wide"
                                 :class="billing === 'yearly' ? 'bg-[#D9F99D] text-slate-900' : 'bg-[#D9F99D] text-slate-900'">
                                <span x-show="billing === 'yearly'">%{{ $discountPct }} {{ __('İNDİRİM') }}</span>
                                <span x-show="billing === 'monthly'">%20 {{ __('İNDİRİM') }}</span>
                            </div>

                            <h3 class="text-xl font-bold text-slate-800">{{ $plan->name }}</h3>
                            <p class="text-xs text-slate-500 font-medium leading-relaxed">{{ $plan->description }}</p>
                        </div>

                        @if(isset($tenant) && $tenant->subscription_plan_id === $plan->id)
                            <div class="mb-4">
                                <span class="bg-indigo-50 text-indigo-700 border border-indigo-100 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide flex items-center gap-1 w-fit">
                                    <i class='bx bx-check-circle'></i> {{ __('Mevcut Paketiniz') }}
                                </span>
                            </div>
                        @endif

                        <!-- Price Block -->
                        <div class="mb-8">
                            <!-- Old Price -->
                            <div class="text-slate-400 text-xs font-medium line-through mb-0.5" 
                                 x-text="billing === 'yearly' ? '₺' + ({{ floatval($plan->price_monthly) }} * 1.5).toFixed(2) : '₺' + ({{ floatval($plan->price_monthly) }} * 1.25).toFixed(2)" 
                                 style="min-h:16px"></div>
                            
                            <!-- Current Price -->
                            <div class="flex items-baseline gap-1">
                                <span class="text-xs font-bold text-slate-700">₺</span>
                                <span class="text-5xl font-extrabold text-slate-900 tracking-tight" 
                                      x-text="billing === 'monthly' ? '{{ floatval($plan->price_monthly) }}' : '{{ floatval($plan->price_yearly) }}'">
                                </span>
                                <span class="text-lg font-bold text-slate-700" x-text="period()"></span>
                            </div>
                            
                            <!-- Bonus Text -->
                            <p class="text-indigo-600 text-xs font-bold mt-2" x-show="billing === 'yearly' && {{ floatval($plan->price_yearly) }} > 0">
                                {{ __('+3 ay ücretsiz') }}
                            </p>
                        </div>

                        <!-- CTA Button (Dynamic based on logic) -->
                        @if(request()->routeIs('subscription.upgrade') || request()->routeIs('subscription.plans'))
                             @php
                                $isDowngrade = false;
                                if(isset($tenant) && $tenant->activeSubscription && $tenant->activeSubscription->isActive() && now()->lt($tenant->activeSubscription->ends_at)) {
                                    if(optional($tenant->plan)->price_monthly > $plan->price_monthly) {
                                        $isDowngrade = true;
                                    }
                                }
                             @endphp

                             @if(isset($tenant) && $tenant->subscription_plan_id === $plan->id)
                                <button disabled class="w-full py-3.5 rounded-lg font-bold text-sm text-center block mb-8 bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200">
                                    {{ __('Mevcut Paket') }}
                                </button>
                             @elseif($isDowngrade)
                                <div class="group relative">
                                    <button disabled class="w-full py-3.5 rounded-lg font-bold text-sm text-center block mb-8 bg-slate-50 text-slate-400 cursor-not-allowed border border-dashed border-slate-300">
                                        {{ __('Süre Dolmadan Geçilemez') }}
                                    </button>
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-2 bg-slate-800 text-white text-[10px] rounded shadow-lg text-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-20">
                                        {{ __('Mevcut paketinizin süresi dolmadan alt bir pakete geçiş yapamazsınız.') }}
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-slate-800"></div>
                                    </div>
                                </div>
                             @else
                                <a :href="'{{ route('subscription.upgrade') }}?plan={{ $plan->id }}&billing=' + billing" class="w-full py-3.5 rounded-lg font-bold text-sm text-center transition-all duration-200 transform hover:scale-[1.02] block mb-8
                                    {{ $plan->is_popular 
                                        ? 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200' 
                                        : 'bg-white border-2 border-slate-900 text-slate-900 hover:bg-slate-50' 
                                    }}">
                                    {{ __('Paketi Yükselt') }}
                                </a>
                             @endif
                        @else
                            {{-- Logic for New Users / Trial Users --}}
                            @php
                                $isOnTrial = isset($tenant) && $tenant->onTrial();
                                $currentPlanId = isset($tenant) ? $tenant->subscription_plan_id : null;
                                $price = (request('billing', $billing ?? 'monthly') === 'monthly' ? $plan->price_monthly : $plan->price_yearly);
                                $isFree = $price == 0;
                            @endphp

                            <form action="{{ route('onboarding.subscribe') }}" method="POST" class="mb-3">
                                @csrf
                                <input type="hidden" name="plan" value="{{ $plan->slug }}">
                                <input type="hidden" name="billing_cycle" :value="billing">
                                
                                {{-- Main Action Button --}}
                                <button type="submit" class="w-full py-3.5 rounded-lg font-bold text-sm transition-all duration-200 transform hover:scale-[1.02]
                                    {{ $plan->is_popular 
                                        ? 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200' 
                                        : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200' 
                                    }}">
                                    <span x-text="(billing === 'monthly' ? {{ $plan->price_monthly }} : {{ $plan->price_yearly }}) == 0 ? '{{ __('Ücretsiz Hemen Başla') }}' : ({{ $isOnTrial ? 'true' : 'false' }} ? '{{ __('Pakete Geçiş Yap') }}' : '{{ __('14 Gün Ücretsiz Dene') }}')"></span>
                                </button>
                            </form>

                            {{-- Buy Now Link (Only for Paid Plans & If on Trial or basic view) --}}
                            <a :href="'?plan={{ $plan->id }}&billing=' + billing" 
                               x-show="(billing === 'monthly' ? {{ $plan->price_monthly }} : {{ $plan->price_yearly }}) > 0"
                               class="w-full py-2 rounded-lg font-bold text-xs text-center block transition-all hover:bg-slate-100 text-slate-400 hover:text-slate-600 mb-8">
                                {{ __('veya Hemen Satın Al') }}
                            </a>
                        @endif

                        <!-- Features List -->
                        <div x-data="{ expanded: false }" class="flex-grow">
                            <!-- Limits Grid -->
                            @php $limits = $plan->limits ?? []; @endphp
                            <ul class="mb-6 space-y-2 text-sm text-slate-700">
                                 @if(isset($limits['user_count']))
                                    <li class="flex items-center gap-2 font-bold">
                                        <i class='bx bx-user text-slate-400'></i>
                                        {{ $limits['user_count'] == -1 ? __('Sınırsız') : $limits['user_count'] }} {{ __('Kullanıcı') }}
                                    </li>
                                @endif
                                @if(isset($limits['proposal_monthly']))
                                    <li class="flex items-center gap-2 font-bold">
                                        <i class='bx bx-file text-slate-400'></i>
                                        {{ $limits['proposal_monthly'] == -1 ? __('Sınırsız') : $limits['proposal_monthly'] }} {{ __('Teklif') }}
                                    </li>
                                @endif
                                @if(isset($limits['customer_count']))
                                    <li class="flex items-center gap-2 font-bold">
                                        <i class='bx bx-group text-slate-400'></i>
                                        {{ $limits['customer_count'] == -1 ? __('Sınırsız') : $limits['customer_count'] }} {{ __('Müşteri') }}
                                    </li>
                                @endif
                                @if(isset($limits['product_count']))
                                    <li class="flex items-center gap-2 font-bold">
                                        <i class='bx bx-package text-slate-400'></i>
                                        {{ $limits['product_count'] == -1 ? __('Sınırsız') : $limits['product_count'] }} {{ __('Ürün') }}
                                    </li>
                                @endif
                            </ul>

                            <div class="h-px bg-slate-100 mb-6"></div>

                            <!-- Main Features -->
                            <ul class="space-y-3">
                                @php 
                                    $featureLabels = \App\Models\Plan::getAvailableFeatures();
                                    $planFeatures = $plan->features ?? [];
                                @endphp

                                @foreach($planFeatures as $loopIndex => $featureKey)
                                    @if(isset($featureLabels[$featureKey]))
                                        <li class="flex items-start gap-3"
                                            x-show="expanded || {{ $loopIndex }} < 5"
                                            x-transition
                                        >
                                            <i class='bx bx-check text-green-500 text-lg shrink-0 mt-0.5'></i>
                                            <span class="text-sm font-medium text-slate-600 leading-tight">
                                                {{ $featureLabels[$featureKey] }}
                                                @if(in_array($featureKey, ['ai_creation', 'netgsm_integration']))
                                                    <span class="ml-1 text-[10px] bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded font-bold uppercase">{{ __('YENİ') }}</span>
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>

                             @if(count($planFeatures) > 5)
                                <button @click="expanded = !expanded" class="mt-4 flex items-center gap-1 text-sm font-bold text-slate-400 hover:text-indigo-600 transition-colors">
                                    <span x-text="expanded ? '{{ __('Daha az göster') }}' : '{{ __('Tüm özellikleri gör') }}'"></span>
                                    <i class='bx' :class="expanded ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Trust Footer -->
        <div class="mt-20 text-center">
            <p class="text-slate-400 text-xs">
                {{ __('30-gün para iade garantisi • 7/24 Destek • Güvenli Ödeme') }}
            </p>
            <p class="text-slate-400 text-xs pt-6">
                powered by fiyera.co
            </p>
        </div>
    @endif
</div>
@endsection
