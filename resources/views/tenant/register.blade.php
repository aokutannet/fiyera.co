<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Hesap Oluştur') }} - {{ config('app.name', 'fiyera.co') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            body { 
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: #fafafa;
                letter-spacing: -0.01em; 
            }
        </style>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body class="min-h-screen flex flex-col bg-[#fafafa]" x-data="{ modal: null }">

        
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-12">
            <div class="w-full max-w-[480px]">
                <!-- Logo -->
                <div class="mb-10 text-center">
                    <div class="flex items-center justify-center gap-2.5 mb-6">
                        <div class="w-10 h-10 bg-slate-950 rounded-xl flex items-center justify-center shadow-lg shadow-slate-200">
                            <i class='bx bxs-bolt text-white text-2xl'></i>
                        </div>
                        <span class="text-2xl font-extrabold tracking-tight text-slate-950">fiyera<span class="text-indigo-600">.co</span></span>
                    </div>
                    <h1 class="text-xl font-bold text-slate-900">{{ __('Ücretsiz Denemeye Başla') }}</h1>
                    <p class="text-slate-500 text-sm mt-2 font-medium">{{ __('14 gün boyunca tüm özellikleri ücretsiz dene. Kredi kartı gerekmez.') }}</p>
                </div>

                <!-- Card -->
                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40">
                    @if($errors->any())
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-xl flex items-start gap-3">
                        <i class='bx bx-error-circle text-rose-500 text-xl mt-0.5'></i>
                        <div class="space-y-1">
                            @foreach($errors->all() as $error)
                                <p class="text-rose-600 text-xs font-bold">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Ad Soyad') }}</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder:text-slate-400"
                                    placeholder="Örn: Ahmet Yılmaz">
                            </div>
                            <div>
                                <label for="company_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Firma Adı') }}</label>
                                <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required 
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder:text-slate-400"
                                    placeholder="Örn: Teknoloji A.Ş.">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Email Adresiniz') }}</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder:text-slate-400"
                                placeholder="ornek@sirket.com">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Şifre') }}</label>
                                <input type="password" id="password" name="password" required 
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder:text-slate-400"
                                    placeholder="••••••••">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Şifre Tekrar') }}</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required 
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder:text-slate-400"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <div class="space-y-3 mt-4">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="marketing_consent" name="marketing_consent" value="1" {{ old('marketing_consent') ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors cursor-pointer">
                                <label for="marketing_consent" class="text-xs text-slate-500 leading-snug cursor-pointer select-none">
                                    Kampanyalardan ve güncellemelerden haberdar olabilmem için tarafıma <a href="#" @click.prevent="modal = 'marketing'" class="text-indigo-600 font-bold hover:underline decoration-indigo-600/30 underline-offset-2">ticari elektronik ileti</a> gönderilmesini kabul ediyorum.
                                </label>
                            </div>
                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="privacy_consent" name="privacy_consent" value="1" required {{ old('privacy_consent') ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors cursor-pointer">
                                <label for="privacy_consent" class="text-xs text-slate-500 leading-snug cursor-pointer select-none">
                                    Kişisel verilerimin işlenmesine yönelik <a href="#" @click.prevent="modal = 'privacy'" class="text-indigo-600 font-bold hover:underline decoration-indigo-600/30 underline-offset-2">aydınlatma ve açık rıza metni</a>'ni okudum, onaylıyorum.
                                </label>
                            </div>
                        </div>

                        <div class="g-recaptcha my-4" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>

                        <button type="submit" 
                            class="w-full bg-slate-950 text-white font-bold py-3.5 rounded-xl text-sm hover:bg-slate-800 transition-all duration-200 shadow-lg shadow-slate-900/20 active:scale-[0.98] flex items-center justify-center gap-2 mt-2">
                            <span>{{ __('Hesabımı Oluştur') }}</span>
                            <i class='bx bx-right-arrow-alt text-xl'></i>
                        </button>

                        <div class="text-center px-4">
                             <p class="text-[11px] text-slate-400 font-medium">
                                Hesabımı Oluştur'a tıklayarak <a href="#" @click.prevent="modal = 'terms'" class="text-blue-600 font-bold hover:underline decoration-blue-600/30 underline-offset-2">Kullanım Sözleşmesi</a>'ni onaylıyorum.
                             </p>
                        </div>

                        <div class="relative py-2">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-100"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="bg-white px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Veya') }}</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <a href="{{ route('auth.google') }}" class="flex w-full items-center justify-center gap-3 px-4 py-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition-all duration-200 group relative overflow-hidden bg-white shadow-sm hover:shadow-md hover:border-slate-300">
                                <div class="absolute inset-0 bg-slate-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <i class='bx bxl-google text-2xl text-slate-500 group-hover:text-[#ea4335] transition-colors relative z-10'></i>
                                <span class="text-sm font-bold text-slate-700 group-hover:text-slate-900 relative z-10">{{ __('Google ile Kayıt Ol') }}</span>
                            </a>
                        </div>
                        <div class="mt-6">
                     
                </div>

            </form>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center text-sm">
                    <p class="text-slate-500 font-medium">{{ __('Zaten hesabınız var mı?') }} <a href="{{ route('login') }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                        {{ __('Giriş Yapın') }}
                    </a></p>
                </div>
            </div>
        </div>
         <div class="w-full py-6 flex flex-col items-center gap-4 z-50 bg-[#fafafa]">
             <!-- Language Switcher -->
             <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-full border border-slate-200 shadow-sm hover:shadow text-xs font-bold text-slate-600 transition-all">
                    @if(app()->getLocale() == 'tr')
                        <img src="https://flagcdn.com/w20/tr.png" class="w-4 rounded-sm" alt="Türkçe">
                        <span>Türkçe</span>
                    @else
                        <img src="https://flagcdn.com/w20/us.png" class="w-4 rounded-sm" alt="English">
                        <span>English</span>
                    @endif
                    <i class='bx bx-chevron-down text-lg text-slate-400'></i>
                </button>
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition.origin.bottom.center
                     class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-32 bg-white rounded-lg shadow-xl border border-slate-100 overflow-hidden py-1 z-50">
                    <a href="{{ route('locale.switch', 'tr') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium hover:bg-slate-50 transition-colors {{ app()->getLocale() == 'tr' ? 'text-indigo-600 bg-indigo-50' : 'text-slate-700' }}">
                        <img src="https://flagcdn.com/w20/tr.png" class="w-3.5 rounded-sm" alt="Türkçe">
                        Türkçe
                    </a>
                    <a href="{{ route('locale.switch', 'en') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium hover:bg-slate-50 transition-colors {{ app()->getLocale() == 'en' ? 'text-indigo-600 bg-indigo-50' : 'text-slate-700' }}">
                        <img src="https://flagcdn.com/w20/us.png" class="w-3.5 rounded-sm" alt="English">
                        English
                    </a>
                </div>
            </div>

            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                &copy; {{ date('Y') }} Fiyera.co
            </div>
        </div>

        <!-- Modals -->
        <div x-show="modal" class="fixed inset-0 z-[100] flex items-center justify-center px-4" style="display: none;">
            <!-- Backdrop -->
            <div x-show="modal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="modal = null" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            
            <!-- Modal Content -->
            <div x-show="modal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="relative bg-white w-full max-w-2xl max-h-[80vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden">
                
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-900" x-text="modal === 'marketing' ? 'Ticari Elektronik İleti İzni' : (modal === 'privacy' ? 'Aydınlatma ve Açık Rıza Metni' : 'Kullanım Sözleşmesi')"></h3>
                    <button @click="modal = null" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 overflow-y-auto text-sm text-slate-600 leading-relaxed space-y-4">
                    
                    <!-- Ticari İleti İçeriği -->
                    <template x-if="modal === 'marketing'">
                        <div class="space-y-4">
                            <p class="font-bold text-slate-900">Ticari Elektronik İleti Onayı</p>
                            <p>Fiyera.co Inc. olarak, sizlere daha iyi hizmet sunabilmek, yeniliklerden, kampanyalardan ve özel fırsatlardan haberdar olmanızı sağlamak amacıyla ticari elektronik iletiler göndermek istiyoruz.</p>
                            <p>Bu metni onaylayarak; Fiyera.co Inc. tarafından sağlanan hizmetlere ilişkin olarak, tarafima e-posta, SMS, arama ve benzeri yollarla tanıtım, kampanya, bilgilendirme ve benzeri içerikli ticari elektronik iletilerin gönderilmesine açık rıza gösteriyorum.</p>
                            <p>İstediğiniz zaman, tarafınıza gönderilen iletilerdeki yönlendirmeleri takip ederek veya bizimle iletişime geçerek bu izni iptal etme hakkına sahipsiniz.</p>
                        </div>
                    </template>

                    <!-- Aydınlatma Metni İçeriği -->
                    <template x-if="modal === 'privacy'">
                        <div class="space-y-4">
                            <p class="font-bold text-slate-900">Kişisel Verilerin Korunması ve İşlenmesi Hakkında Aydınlatma Metni</p>
                            <p>Fiyera.co Inc. ("Şirket") olarak, kişisel verilerinizin güvenliği ve gizliliği konusuna azami hassasiyet göstermekteyiz. 6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") uyarınca, kişisel verileriniz aşağıda açıklanan kapsamda işlenebilecektir.</p>
                            
                            <h4 class="font-bold text-slate-900">1. Kişisel Verilerin Toplanma Yöntemi ve Hukuki Sebebi</h4>
                            <p>Kişisel verileriniz, web sitemiz, mobil uygulamalarımız, çağrı merkezimiz ve diğer kanallar aracılığıyla, sözleşmenin kurulması ve ifası, hukuki yükümlülüklerimizin yerine getirilmesi, meşru menfaatlerimiz ve açık rızanız hukuki sebeplerine dayalı olarak toplanmaktadır.</p>
                            
                            <h4 class="font-bold text-slate-900">2. Kişisel Verilerin İşlenme Amaçları</h4>
                            <p>Toplanan kişisel verileriniz; hizmetlerimizin sunulması, kullanıcı deneyiminin iyileştirilmesi, hukuki yükümlülüklerin yerine getirilmesi, güvenliğin sağlanması ve tarafınızla iletişime geçilmesi amaçlarıyla işlenmektedir.</p>

                            <h4 class="font-bold text-slate-900">3. Kişisel Verilerin Aktarılması</h4>
                            <p>Kişisel verileriniz, yasal düzenlemelerin öngördüğü kapsamda, iş ortaklarımıza, tedarikçilerimize, kanunen yetkili kamu kurum ve kuruluşlarına aktarılabilecektir.</p>
                            
                            <p>Bu metni onaylayarak, kişisel verilerinizin yukarıda belirtilen amaçlar doğrultusunda işlenmesini ve aydınlatma metnini okuduğunuzu beyan etmektesiniz.</p>
                        </div>
                    </template>

                    <!-- Kullanım Sözleşmesi İçeriği -->
                    <template x-if="modal === 'terms'">
                        <div class="space-y-4">
                            <p class="font-bold text-slate-900">Kullanıcı Hizmet Sözleşmesi</p>
                            <p>İşbu Kullanıcı Hizmet Sözleşmesi ("Sözleşme"), Fiyera.co Inc. ("Fiyera") ile Fiyera.co platformuna üye olan kullanıcı ("Kullanıcı") arasında, aşağıdaki şartlar dahilinde akdedilmiştir.</p>
                            
                            <h4 class="font-bold text-slate-900">1. Sözleşmenin Konusu</h4>
                            <p>İşbu Sözleşme'nin konusu, Kullanıcı'nın Fiyera.co tarafından sunulan SaaS (Hizmet Olarak Yazılım) hizmetlerinden faydalanmasına ilişkin şartların belirlenmesidir.</p>
                            
                            <h4 class="font-bold text-slate-900">2. Kullanım Koşulları</h4>
                            <p>Kullanıcı, platformu hukuka ve genel ahlaka uygun olarak kullanacağını, üçüncü kişilerin haklarını ihlal etmeyeceğini kabul ve taahhüt eder.</p>

                            <h4 class="font-bold text-slate-900">3. Ücretlendirme ve Ödeme</h4>
                            <p>Hizmetin kullanımı, seçilen paket ve ödeme planına göre ücretlendirilir. Fiyera, fiyat politikasında değişiklik yapma hakkını saklı tutar.</p>

                            <h4 class="font-bold text-slate-900">4. Gizlilik ve Güvenlik</h4>
                            <p>Taraflar, işbu sözleşme kapsamında edindikleri gizli bilgileri üçüncü kişilerle paylaşmayacağını taahhüt eder.</p>
                            
                            <p>Hesabınızı oluşturarak bu sözleşmenin tüm hükümlerini okuduğunuzu, anladığınızı ve kabul ettiğinizi beyan etmektesiniz.</p>
                        </div>
                    </template>
                </div>
                
                <!-- Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                    <button @click="modal = null" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-sm transition-colors">
                        Anladım ve Kapat
                    </button>
                </div>
            </div>
        </div>
    </body>
</html>
