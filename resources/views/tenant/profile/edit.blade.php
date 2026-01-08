@extends('tenant.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">Kullanıcı Ayarları</h1>
        <p class="text-slate-500 text-sm mt-1">Profil bilgilerinizi ve hesap ayarlarınızı buradan yönetebilirsiniz.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-md flex items-center gap-3 text-emerald-600">
            <i class='bx bx-check-circle text-xl'></i>
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Navigation (Optional for Settings) -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-md border border-slate-100 p-4 space-y-1">
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 bg-slate-50 text-slate-950 rounded-xl text-sm font-bold transition-all">
                    <i class='bx bx-user text-xl'></i> Profil Bilgileri
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-slate-500 hover:text-slate-950 hover:bg-slate-50 rounded-xl text-sm font-semibold transition-all">
                    <i class='bx bx-shield-alt-2 text-xl'></i> Güvenlik
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-slate-500 hover:text-slate-950 hover:bg-slate-50 rounded-xl text-sm font-semibold transition-all">
                    <i class='bx bx-bell text-xl'></i> Bildirimler
                </a>
            </div>

            <div class="bg-indigo-600 rounded-md p-6 text-white relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-xs font-bold text-indigo-200 uppercase tracking-widest mb-2">Yardıma mı ihtiyacınız var?</p>
                    <p class="text-sm leading-relaxed mb-4 opacity-90">Hesap ayarlarıyla ilgili bir sorun yaşıyorsanız destek ekibimizle iletişime geçin.</p>
                    <button class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-xs font-bold transition-all backdrop-blur-md">Destek Al</button>
                </div>
                <i class='bx bx-support absolute -right-4 -bottom-4 text-8xl opacity-10 group-hover:scale-110 transition-transform duration-500'></i>
            </div>
        </div>

        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-8">
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Profile Section -->
                <div class="bg-white rounded-md border border-slate-100 overflow-hidden shadow-sm">
                    <div class="px-4 md:px-8 py-6 border-b border-slate-50">
                        <h2 class="text-lg font-bold text-slate-950">Kişisel Bilgiler</h2>
                    </div>
                    <div class="p-4 md:p-8 space-y-6">
                        <div class="flex items-center gap-6 mb-4">
                            <div class="relative group">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0f172a&color=fff&size=128" class="w-24 h-24 rounded-3xl object-cover ring-4 ring-slate-50 group-hover:ring-slate-100 transition-all shadow-lg shadow-slate-200" alt="Avatar">
                                <label class="absolute inset-0 flex items-center justify-center bg-slate-950/40 rounded-3xl opacity-0 group-hover:opacity-100 cursor-pointer transition-all backdrop-blur-[2px]">
                                    <i class='bx bx-camera text-white text-2xl'></i>
                                    <input type="file" class="hidden">
                                </label>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-950">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $user->position ?? 'Görev Belirtilmedi' }}</p>
                                <p class="text-xs text-indigo-600 font-bold uppercase tracking-wider mt-2">{{ $user->tenant->name ?? 'fiyera.co' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Ad Soyad</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all @error('name') border-rose-500 @enderror">
                                @error('name') <p class="text-[11px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">E-posta Adresi</label>
                                <div class="relative group/disabled">
                                    <input type="email" value="{{ $user->email }}" readonly class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-medium text-slate-500 cursor-not-allowed outline-none transition-all">
                                    <i class='bx bx-lock-alt absolute right-4 top-1/2 -translate-y-1/2 text-slate-300'></i>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1 ml-1 leading-relaxed">Güvenlik nedeniyle e-posta adresi değiştirilemez.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Telefon</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+90 5XX XXX XX XX" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all @error('phone') border-rose-500 @enderror">
                                @error('phone') <p class="text-[11px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Pozisyon / Ünvan</label>
                                <input type="text" name="position" value="{{ old('position', $user->position) }}" placeholder="Örn: Satış Yöneticisi" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all @error('position') border-rose-500 @enderror">
                                @error('position') <p class="text-[11px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Biyografi</label>
                            <textarea name="bio" rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all @error('bio') border-rose-500 @enderror" placeholder="Kendinizden bahsedin...">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio') <p class="text-[11px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="bg-white rounded-md border border-slate-100 overflow-hidden shadow-sm">
                    <div class="px-4 md:px-8 py-6 border-b border-slate-50">
                        <h2 class="text-lg font-bold text-slate-950">Güvenlik ve Şifre</h2>
                        <p class="text-slate-400 text-xs mt-1 italic">Boş bırakırsanız şifreniz değişmeyecektir.</p>
                    </div>
                    <div class="p-4 md:p-8 space-y-6">
                        <!-- 2FA Toggle -->
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex gap-4 items-center">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100/50 flex items-center justify-center text-indigo-600">
                                    <i class='bx bx-shield-quarter text-2xl'></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">İki Faktörlü Doğrulama (2FA)</h4>
                                    <p class="text-xs text-slate-500 font-medium">Giriş yaparken e-posta onayı iste.</p>
                                </div>
                            </div>
                            
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="two_factor_enabled" value="1" class="sr-only peer" {{ $user->two_factor_enabled ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Mevcut Şifre</label>
                            <div class="relative">
                                <input type="password" name="current_password" class="w-full pl-4 pr-12 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all @error('current_password') border-rose-500 @enderror">
                                <i class='bx bx-lock-alt absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 text-xl'></i>
                            </div>
                            @error('current_password') <p class="text-[11px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Yeni Şifre</label>
                                <input type="password" name="password" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all @error('password') border-rose-500 @enderror">
                                @error('password') <p class="text-[11px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Yeni Şifre (Tekrar)</label>
                                <input type="password" name="password_confirmation" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <button type="button" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-all">Vazgeç</button>
                    <button type="submit" class="px-8 py-3 bg-slate-950 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">
                        Değişiklikleri Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
