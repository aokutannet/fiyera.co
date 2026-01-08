@extends('tenant.layouts.app')

@section('content')
<div class="space-y-8" x-data="{ 
    editMode: false,
    showModal: false,
    deleteMode: false,
    deleteId: null,
    deleteName: '',
    currentCategory: { id: null, name: '', description: '' },
    
    openAddModal() {
        this.editMode = false;
        this.currentCategory = { id: null, name: '', description: '' };
        this.showModal = true;
    },
    
    openEditModal(category) {
        this.editMode = true;
        this.currentCategory = { ...category };
        this.showModal = true;
    },

    openDeleteModal(category) {
        this.deleteMode = true;
        this.deleteId = category.id;
        this.deleteName = category.name;
    },

    closeModal() {
        this.showModal = false;
        this.deleteMode = false;
    }
}">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('products.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-slate-900 transition-all shadow-sm">
                    <i class='bx bx-chevron-left text-2xl'></i>
                </a>
                <h1 class="text-xl font-extrabold text-slate-950 tracking-tight">Kategori Yönetimi</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1 ml-14">Ürün kategorilerinizi oluşturun ve düzenleyin.</p>
        </div>
        
        <button @click="openAddModal()" class="h-11 px-6 flex items-center justify-center gap-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 whitespace-nowrap">
            <i class='bx bx-plus text-xl'></i> Yeni Kategori
        </button>
    </div>

    <!-- Alert Messages (Success/Error) -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-medium flex items-center gap-2 animate-in fade-in slide-in-from-top-2">
        <i class='bx bx-check-circle text-lg'></i>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-rose-50 border border-rose-100 text-rose-700 px-6 py-4 rounded-2xl text-sm font-medium flex items-center gap-2 animate-in fade-in slide-in-from-top-2">
        <i class='bx bx-error-circle text-lg'></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- Categories List -->
    <div class="bg-white rounded-md border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori Adı</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Açıklama</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Ürün Sayısı</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($categories as $category)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-slate-900">{{ $category->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-slate-500 font-medium">{{ $category->description ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700">
                            {{ $category->products_count }} Ürün
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button @click="openEditModal({{ json_encode($category) }})" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all" title="Düzenle">
                                <i class='bx bx-edit-alt text-lg'></i>
                            </button>
                            <button @click="openDeleteModal({{ json_encode($category) }})" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Sil">
                                <i class='bx bx-trash text-lg'></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($categories->isEmpty())
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <i class='bx bx-category text-4xl text-slate-200'></i>
                            <p class="text-slate-400 text-sm font-medium">Henüz kategori eklenmemiş.</p>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Create/Edit Modal -->
    <div x-cloak x-show="showModal" 
        class="fixed inset-0 z-[100] overflow-y-auto" 
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                 @click="closeModal"></div>

            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full">
                
                <div class="bg-white px-8 pt-8 pb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-black text-slate-900" id="modal-title" x-text="editMode ? 'Kategori Düzenle' : 'Yeni Kategori'"></h3>
                        <button @click="closeModal" class="text-slate-400 hover:text-slate-500">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>

                    <form :action="editMode ? '{{ url('categories') }}/' + currentCategory.id : '{{ route('categories.store') }}'" method="POST">
                        @csrf
                        <template x-if="editMode">
                            @method('PUT')
                        </template>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Kategori Adı <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="currentCategory.name" required
                                    class="w-full h-11 px-4 rounded-xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all placeholder:font-medium"
                                    placeholder="Örn: Elektronik">
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Açıklama</label>
                                <textarea name="description" x-model="currentCategory.description" rows="3"
                                    class="w-full p-4 rounded-xl bg-slate-50 border border-slate-100 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 transition-all placeholder:font-medium"
                                    placeholder="Kategori açıklaması..."></textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" @click="closeModal" class="px-6 py-3 rounded-xl bg-slate-50 text-slate-500 text-sm font-bold hover:bg-slate-100 transition-all">İptal</button>
                            <button type="submit" class="px-8 py-3 rounded-xl bg-indigo-600 text-white text-sm font-black hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                                <span x-text="editMode ? 'GÜNCELLE' : 'OLUŞTUR'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-cloak x-show="deleteMode" 
        class="fixed inset-0 z-[100] overflow-y-auto" 
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm relative z-10 overflow-hidden border border-slate-100 p-8 flex flex-col items-center">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mb-6">
                    <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center animate-pulse">
                        <i class='bx bx-trash text-4xl text-rose-600'></i>
                    </div>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-2">Emin misiniz?</h3>
                <p class="text-slate-500 font-bold text-center leading-relaxed mb-8">
                    <span class="text-slate-900" x-text="deleteName"></span> kategorisini silmek istediğinize emin misiniz?
                </p>
                <div class="flex flex-col w-full gap-3">
                    <form :action="'{{ url('categories') }}/' + deleteId" method="POST" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-4 rounded-2xl bg-rose-600 text-white text-sm font-black hover:bg-rose-700 transition-all shadow-xl shadow-rose-100 active:scale-[0.98]">
                            EVET, SİL
                        </button>
                    </form>
                    <button @click="closeModal" class="w-full py-4 rounded-2xl bg-slate-50 text-slate-500 text-sm font-bold hover:bg-slate-100 transition-all">
                        VAZGEÇ
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
