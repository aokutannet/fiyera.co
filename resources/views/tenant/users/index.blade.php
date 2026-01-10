@extends('tenant.layouts.app')

@section('content')
@php
    $allPermissionKeys = collect($availablePermissions)->pluck('permissions')->map(fn($p) => array_keys($p))->flatten()->values()->toArray();
@endphp

<div class="space-y-8" x-data="{ 
    editUser: null,
    isAddModalOpen: false,
    isEditModalOpen: false,
    isLimitModalOpen: false,
    selectedPermissions: [],
    allPermissions: {{ json_encode($allPermissionKeys) }},

    get allSelected() {
        return this.allPermissions.every(perm => this.selectedPermissions.includes(perm));
    },

    toggleAll() {
        if (this.allSelected) {
            this.selectedPermissions = [];
        } else {
            this.selectedPermissions = [...this.allPermissions];
        }
    },

    openAddModal() {
        this.selectedPermissions = [];
        this.isAddModalOpen = true;
    },

    openEditModal(user) {
        this.editUser = user;
        this.selectedPermissions = (user.permissions && Array.isArray(user.permissions)) ? [...user.permissions] : [];
        this.isEditModalOpen = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">Kullanıcı Yönetimi</h1>
            <p class="text-slate-500 text-sm mt-1">Ekibinizi yönetin ve yeni alt kullanıcılar ekleyin.</p>
        </div>
        <button 
            @if($limitReached)
                @click="isLimitModalOpen = true"
            @else
                @click="openAddModal()" 
            @endif
            class="w-full md:w-auto h-11 px-6 flex items-center justify-center gap-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 {{ $limitReached ? 'opacity-70' : '' }}"
        >
            <i class='bx bx-user-plus text-xl'></i> Yeni Kullanıcı Ekle
        </button>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3">
        <i class='bx bx-check-circle text-lg'></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3">
        <i class='bx bx-x-circle text-lg'></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- Mobile User Cards -->
    <div class="grid grid-cols-1 gap-4 md:hidden">
        @foreach($users as $user)
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm relative overflow-hidden {{ $user->status === 'passive' ? 'opacity-75' : '' }}">
            <div class="flex items-start gap-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ $user->status === 'passive' ? 'e2e8f0' : 'f1f5f9' }}&color=64748b" class="w-12 h-12 rounded-xl" alt="">
                <div class="flex-1 min-w-0">
                     <div class="flex justify-between items-start">
                        <div>
                             <h3 class="text-sm font-bold text-slate-900 truncate pr-6">{{ $user->name }}</h3>
                             <p class="text-xs text-slate-500 font-medium truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                     <div class="flex items-center gap-2 mt-2 flex-wrap">
                        @if($user->is_owner)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-100 uppercase tracking-wider">
                                Ana Yönetici
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 uppercase tracking-wider">
                                Alt Kullanıcı
                            </span>
                        @endif
                        <span class="text-xs text-slate-400">@if($user->position) • {{ $user->position }} @endif</span>
                        <span class="text-xs text-slate-400">• {{ $user->proposals_count }} Teklif Hazırladı</span>
                    </div>
                </div>
                 <!-- Status Badge (Absolute Top Right) -->
                <div class="absolute top-4 right-4">
                     @if($user->status === 'active')
                        <span class="w-2 h-2 rounded-full bg-emerald-500 block shadow-sm shadow-emerald-200"></span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-slate-300 block"></span>
                    @endif
                </div>
            </div>

            <!-- Mobile Actions -->
            <div class="grid grid-cols-{{ !$user->is_owner ? '3' : '1' }} gap-2 mt-4 pt-4 border-t border-slate-50">
                 <button @click="openEditModal({{ json_encode($user) }})" class="h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-600 text-xs font-bold hover:bg-slate-100 transition-all">
                    Düzenle
                </button>
                @if(!$user->is_owner)
                <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="contents">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="h-10 flex items-center justify-center rounded-xl {{ $user->status === 'active' ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }} text-xs font-bold transition-all">
                        {{ $user->status === 'active' ? 'Pasife Al' : 'Aktif Et' }}
                    </button>
                </form>

                <form action="{{ route('users.destroy', $user) }}" method="POST" class="contents">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')" class="h-10 flex items-center justify-center rounded-xl bg-rose-50 text-rose-600 text-xs font-bold transition-all">
                        Sil
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white rounded-md border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Kullanıcı</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Teklifler</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Rol</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Durum</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">E-posta</th>
                        <th class="px-4 md:px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors {{ $user->status === 'passive' ? 'opacity-60' : '' }}">
                        <td class="px-4 md:px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ $user->status === 'passive' ? 'e2e8f0' : 'f1f5f9' }}&color=64748b" class="w-9 h-9 rounded-lg" alt="">
                                <div>
                                    <p class="text-sm font-bold text-slate-950">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-400">@if($user->position) {{ $user->position }} @else Üye @endif</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 md:px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600">
                                {{ $user->proposals_count }} Adet
                            </span>
                        </td>
                        <td class="px-4 md:px-6 py-4">
                            @if($user->is_owner)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-100 uppercase tracking-wider">
                                Ana Yönetici
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 uppercase tracking-wider">
                                Alt Kullanıcı
                            </span>
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-4">
                            @if($user->status === 'active')
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
                        <td class="px-4 md:px-6 py-4 text-sm text-slate-600 font-medium">
                            {{ $user->email }}
                        </td>
                        <td class="px-4 md:px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Edit Button -->
                                <button @click="openEditModal({{ json_encode($user) }})" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all" title="Düzenle">
                                    <i class='bx bx-edit-alt text-lg'></i>
                                </button>

                                @if(!$user->is_owner)
                                <!-- Toggle Status Button -->
                                <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 {{ $user->status === 'active' ? 'hover:text-amber-600 hover:bg-amber-50' : 'hover:text-emerald-600 hover:bg-emerald-50' }} transition-all" title="{{ $user->status === 'active' ? 'Pasife Al' : 'Aktif Et' }}">
                                        <i class='bx {{ $user->status === 'active' ? 'bx-pause-circle' : 'bx-play-circle' }} text-lg'></i>
                                    </button>
                                </form>

                                <!-- Delete Button -->
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Sil">
                                        <i class='bx bx-trash text-lg'></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
                    Mevcut paketinizin kullanıcı ekleme limitine ({{ $plan->limits['user_count'] ?? 1 }} kullanıcı) ulaştınız. Ekibinizi büyütmek için paketinizi yükseltin.
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

    <!-- Add User Modal -->
    <div x-show="isAddModalOpen" 
         @keydown.escape.window="isAddModalOpen = false"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         x-cloak>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="isAddModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                 @click="isAddModalOpen = false"></div>

            <div x-show="isAddModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:max-w-md sm:w-full border border-slate-100 relative z-10">
                
                <div class="px-8 pt-8 pb-4">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-950 tracking-tight">Yeni Kullanıcı Ekle</h3>
                            <p class="text-slate-500 text-xs mt-1">Ekibinize yeni bir çalışma arkadaşı katın.</p>
                        </div>
                        <button @click="isAddModalOpen = false" class="text-slate-400 hover:text-slate-600">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>

                    <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Ad Soyad</label>
                            <input type="text" name="name" required class="w-full h-12 px-4 rounded-xl bg-slate-50 border border-slate-100 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">E-posta Adresi</label>
                            <input type="email" name="email" required class="w-full h-12 px-4 rounded-xl bg-slate-50 border border-slate-100 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Şifre</label>
                            <input type="password" name="password" required class="w-full h-12 px-4 rounded-xl bg-slate-50 border border-slate-100 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition-all">
                            <p class="text-[10px] text-slate-400 mt-2 font-medium">Kullanıcı daha sonra şifresini değiştirebilir.</p>
                        </div>

                        <!-- Permissions Section -->
                        <div class="border-t border-slate-100 pt-4">
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Yetkiler</label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" @click="toggleAll()" :checked="allSelected" class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-600">
                                    <span class="text-xs font-bold text-indigo-600 group-hover:text-indigo-700 transition-colors">Tümünü Ekle</span>
                                </label>
                            </div>
                            <div class="space-y-4 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($availablePermissions as $groupKey => $group)
                                <div>
                                    <h4 class="text-xs font-bold text-slate-950 mb-2">{{ $group['label'] }}</h4>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($group['permissions'] as $permKey => $permLabel)
                                        <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-100 hover:bg-slate-50 cursor-pointer transition-colors">
                                            <input type="checkbox" name="permissions[]" value="{{ $permKey }}" x-model="selectedPermissions" class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-600">
                                            <span class="text-xs font-medium text-slate-600">{{ $permLabel }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="pt-4 pb-8">
                            <button type="submit" class="w-full h-12 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                                Kullanıcıyı Oluştur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div x-show="isEditModalOpen" 
         @keydown.escape.window="isEditModalOpen = false"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         x-cloak>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="isEditModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                 @click="isEditModalOpen = false"></div>

            <div x-show="isEditModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:max-w-md sm:w-full border border-slate-100 relative z-10">
                
                <div class="px-8 pt-8 pb-4">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-950 tracking-tight">Kullanıcıyı Düzenle</h3>
                            <p class="text-slate-500 text-xs mt-1">Kullanıcı bilgilerini güncelleyin.</p>
                        </div>
                        <button @click="isEditModalOpen = false" class="text-slate-400 hover:text-slate-600">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>

                    <form :action="'/users/' + (editUser ? editUser.id : '')" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Ad Soyad</label>
                            <input type="text" name="name" :value="editUser ? editUser.name : ''" required class="w-full h-12 px-4 rounded-xl bg-slate-50 border border-slate-100 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">E-posta Adresi</label>
                            <input type="email" name="email" :value="editUser ? editUser.email : ''" required class="w-full h-12 px-4 rounded-xl bg-slate-50 border border-slate-100 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Yeni Şifre (İsteğe bağlı)</label>
                            <input type="password" name="password" class="w-full h-12 px-4 rounded-xl bg-slate-50 border border-slate-100 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition-all">
                            <p class="text-[10px] text-slate-400 mt-2 font-medium">Değiştirmek istemiyorsanız boş bırakın.</p>
                        </div>

                        <!-- Permissions Section -->
                        <template x-if="!editUser || !editUser.is_owner">
                            <div class="border-t border-slate-100 pt-4">
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Yetkiler</label>
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" @click="toggleAll()" :checked="allSelected" class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-600">
                                        <span class="text-xs font-bold text-indigo-600 group-hover:text-indigo-700 transition-colors">Tümünü Ekle</span>
                                    </label>
                                </div>
                                <div class="space-y-4 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach($availablePermissions as $groupKey => $group)
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-950 mb-2">{{ $group['label'] }}</h4>
                                        <div class="grid grid-cols-2 gap-2">
                                            @foreach($group['permissions'] as $permKey => $permLabel)
                                            <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-100 hover:bg-slate-50 cursor-pointer transition-colors">
                                                <input type="checkbox" name="permissions[]" value="{{ $permKey }}" x-model="selectedPermissions" class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-600">
                                                <span class="text-xs font-medium text-slate-600">{{ $permLabel }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </template>
                        <template x-if="editUser && editUser.is_owner">
                             <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-start gap-3 mt-4">
                                <i class='bx bxs-star text-indigo-600 text-xl mt-0.5'></i>
                                <div>
                                    <h4 class="text-sm font-bold text-indigo-900">Tam Erişim Yetkisi</h4>
                                    <p class="text-xs text-indigo-700 mt-1">Bu kullanıcı "Ana Yönetici" olduğu için sistemdeki tüm yetkilere sahiptir. Yetki kısıtlaması yapılamaz.</p>
                                </div>
                            </div>
                        </template>
                        
                        <div class="pt-4 pb-8">
                            <button type="submit" class="w-full h-12 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                                Bilgileri Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
