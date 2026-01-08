@extends('tenant.layouts.onboarding')

@php
    $questions = [
        [
            'id' => 'sector',
            'question' => 'Firmanız hangi sektörde?',
            'options' => ['Ajans', 'İnşaat', 'Yazılım', 'Üretim', 'E-ticaret', 'Danışmanlık', 'Diğer'],
            'type' => 'radio',
            'has_other' => true
        ],
        [
            'id' => 'team_size',
            'question' => 'Kaç kişilik bir ekibiniz var?',
            'options' => ['1–3', '4–10', '11–25', '25+'],
            'type' => 'radio'
        ],
        [
            'id' => 'monthly_proposals',
            'question' => 'Ayda Kaç Teklif Hazırlıyorsunuz?',
            'options' => ['1–10', '10–50', '50–200', '200+'],
            'type' => 'radio'
        ],
        [
            'id' => 'target_audience',
            'question' => 'Teklifleri en çok kime hazırlıyorsunuz?',
            'subtext' => 'Birden fazla seçebilirsiniz',
            'options' => ['Bireysel müşteriler', 'KOBİ', 'Kurumsal'],
            'type' => 'checkbox'
        ],
        [
            'id' => 'currency',
            'question' => 'En Çok Kullandığınız Para Birimi',
            'subtext' => 'Birden fazla seçebilirsiniz',
            'options' => ['₺ TRY', '$ USD', '€ EUR'],
            'type' => 'checkbox'
        ],
        [
            'id' => 'vat_usage',
            'question' => 'Teklifte KDV Kullanıyor musunuz?',
            'options' => ['Evet', 'Hayır'],
            'type' => 'radio'
        ],
        [
            'id' => 'proposal_criteria',
            'question' => 'Teklifte en önemli kriteriniz ne?',
            'options' => ['Fiyat', 'Hız', 'Detaylı açıklama', 'Profesyonel görünüm'],
            'type' => 'radio'
        ],
        [
            'id' => 'proposal_preparer',
            'question' => 'Teklifi kim hazırlıyor?',
            'options' => ['Satış', 'Patron', 'Ofis personeli'],
            'type' => 'radio'
        ],
        [
            'id' => 'previous_software',
            'question' => 'Daha önce teklif yazılımı kullandınız mı?',
            'options' => ['Evet', 'Hayır'],
            'type' => 'radio'
        ]
    ];
@endphp

@section('content')
<!-- Thin Progress Bar fixed at top -->
<div class="fixed top-0 left-0 w-full h-1 z-[60]" x-data="{ 
    step: 0, 
    total: {{ count($questions) }} 
}" x-init="$watch('$store.wizard.step', value => step = value)">
    <div class="h-full bg-slate-900 transition-all duration-500 ease-out" 
         :style="`width: ${((step + 1) / total) * 100}%`"></div>
</div>

<div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8" 
     x-data="{ 
        step: 0, 
        total: {{ count($questions) }},
        answers: {},
        sectorOther: '',
        init() {
            this.$store.wizard = { step: 0 };
            this.$watch('step', value => this.$store.wizard.step = value);
        },
        nextStep() {
            if (this.step < this.total - 1) {
                this.step++;
            } else {
                $refs.form.submit();
            }
        },
        prevStep() {
            if (this.step > 0) {
                this.step--;
            }
        },
        toggleArray(key, value) {
            if (!this.answers[key]) this.answers[key] = [];
            const index = this.answers[key].indexOf(value);
            if (index === -1) {
                this.answers[key].push(value);
            } else {
                this.answers[key].splice(index, 1);
            }
        },
        setRadio(key, value) {
            this.answers[key] = value;
            if (value !== 'Diğer') {
                setTimeout(() => this.nextStep(), 250);
            }
        },
        isSelected(key, value) {
             if (Array.isArray(this.answers[key])) {
                return this.answers[key].includes(value);
             }
             return this.answers[key] === value;
        }
     }">
    
    <div class="max-w-screen-md w-full">
        
        <form x-ref="form" method="POST" action="{{ route('onboarding.store') }}">
            @csrf

            @foreach($questions as $index => $q)
                <div x-show="step === {{ $index }}" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     style="display: none;"
                     class="flex flex-col items-center text-center">
                    
                    <span class="text-xs font-bold text-slate-400 tracking-[0.2em] mb-6 uppercase">Adım {{ $index + 1 }} / {{ count($questions) }}</span>

                    <h2 class="text-xl md:text-xl font-bold text-slate-900 mb-4 leading-tight tracking-tight">
                        {{ $q['question'] }}
                    </h2>
                    
                    @if(isset($q['subtext']))
                    <p class="text-slate-500 text-lg mb-10 font-medium">{{ $q['subtext'] }}</p>
                    @else
                    <div class="mb-10"></div>
                    @endif
                    
                    <div class="w-full max-w-2xl grid {{ count($q['options']) <= 4 ? 'grid-cols-1 md:grid-cols-2' : 'grid-cols-1 md:grid-cols-3' }} gap-4">
                        @foreach($q['options'] as $option)
                            <div class="relative">
                                @if($q['type'] === 'radio')
                                    <button 
                                        type="button"
                                        @click="setRadio('{{ $q['id'] }}', '{{ $option }}')"
                                        class="w-full py-4 px-6 rounded-lg text-lg font-medium transition-all duration-200 border"
                                        :class="answers['{{ $q['id'] }}'] === '{{ $option }}' 
                                            ? 'bg-slate-900 border-slate-900 text-white shadow-lg scale-[1.02]' 
                                            : 'bg-transparent border-slate-200 text-slate-600 hover:border-slate-400 hover:text-slate-900'"
                                    >
                                        {{ $option }}
                                    </button>
                                     <input type="hidden" name="{{ $q['id'] }}" :value="answers['{{ $q['id'] }}']">
                                @else
                                    <button 
                                        type="button"
                                        @click="toggleArray('{{ $q['id'] }}', '{{ $option }}')"
                                        class="w-full py-4 px-6 rounded-lg text-lg font-medium transition-all duration-200 border relative overflow-hidden"
                                        :class="isSelected('{{ $q['id'] }}', '{{ $option }}')
                                            ? 'bg-slate-900 border-slate-900 text-white shadow-lg' 
                                            : 'bg-transparent border-slate-200 text-slate-600 hover:border-slate-400 hover:text-slate-900'"
                                    >
                                        {{ $option }}
                                    </button>
                                    <!-- Hidden inputs for array submission -->
                                    <template x-for="val in answers['{{ $q['id'] }}']">
                                         <input type="hidden" name="{{ $q['id'] }}[]" :value="val">
                                    </template>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Custom Input for "Other" -->
                    @if(isset($q['has_other']))
                        <div x-show="answers['{{ $q['id'] }}'] === 'Diğer'" x-collapse class="mt-6 w-full max-w-lg">
                            <input type="text" 
                                   name="{{ $q['id'] }}_custom" 
                                   x-model="sectorOther" 
                                   @keydown.enter.prevent="nextStep()"
                                   class="w-full text-center bg-transparent border-b-2 border-slate-200 text-2xl py-2 focus:outline-none focus:border-slate-900 transition-colors placeholder:text-slate-300" 
                                   placeholder="Sektörünüzü yazın..."
                                   autofocus
                            >
                            <div class="mt-4 flex justify-center">
                                <button type="button" @click="nextStep()" class="text-sm font-bold text-slate-900 hover:text-indigo-600 transition-colors flex items-center gap-1">
                                    Devam Et <i class='bx bx-right-arrow-alt'></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Continue Button for Checkbox -->
                    @if($q['type'] === 'checkbox')
                        <div class="mt-10" x-show="answers['{{ $q['id'] }}'] && answers['{{ $q['id'] }}'].length > 0">
                             <button type="button" @click="nextStep()" class="bg-indigo-600 text-white px-10 py-3 rounded-full font-bold hover:bg-slate-900 transition-all transform hover:-translate-y-1 shadow-lg shadow-indigo-200 hover:shadow-xl">
                                Devam Et
                            </button>
                        </div>
                    @endif

                </div>
            @endforeach
        </form>
    </div>

    <!-- Navigation Footer -->
    <div class="fixed bottom-0 left-0 w-full p-6 flex justify-between items-center z-50 pointer-events-none">
        <div>
             <button 
                type="button" 
                @click="prevStep()" 
                x-show="step > 0"
                class="pointer-events-auto flex items-center gap-2 text-slate-400 hover:text-slate-900 transition-colors font-medium px-4 py-2"
            >
                <i class='bx bx-left-arrow-alt text-xl'></i> Geri
            </button>
        </div>
        
    </div>

</div>
@endsection
