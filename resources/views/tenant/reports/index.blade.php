@extends('tenant.layouts.app')

@section('content')
<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <!-- Header & Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-950 tracking-tight">Raporlar & Analizler</h1>
            <p class="text-slate-500 text-sm font-medium">Teklif ve performans analizlerinizi tek bir yerden takip edin.</p>
        </div>
        
        <form id="filterForm" action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="bg-white p-1 rounded-xl border border-slate-200 flex items-center">
                <button type="submit" name="filter" value="today" class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $filter === 'today' ? 'bg-slate-950 text-white shadow-md' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }}">Bugün</button>
                <button type="submit" name="filter" value="week" class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $filter === 'week' ? 'bg-slate-950 text-white shadow-md' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }}">Bu Hafta</button>
                <button type="submit" name="filter" value="this_month" class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $filter === 'this_month' ? 'bg-slate-950 text-white shadow-md' : 'text-slate-500 hover:text-slate-950 hover:bg-slate-50' }}">Bu Ay</button>
                
                <div class="h-4 w-px bg-slate-200 mx-2"></div>
                
                <div class="flex items-center gap-2 px-2" x-data>
                     <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="text-xs font-bold text-slate-600 bg-transparent outline-none border-b border-transparent focus:border-indigo-500 transition-all w-28">
                     <span class="text-slate-300">-</span>
                     <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="text-xs font-bold text-slate-600 bg-transparent outline-none border-b border-transparent focus:border-indigo-500 transition-all w-28">
                     <button type="submit" name="filter" value="custom" class="w-6 h-6 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-all">
                        <i class='bx bx-check'></i>
                     </button>
                </div>
            </div>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Revenue -->
        <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm relative overflow-hidden group hover:border-indigo-100 transition-all">
            <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-50/50 rounded-full blur-3xl -mr-16 -mt-16 transition-all group-hover:bg-indigo-100/50"></div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 relative z-10">Toplam Ciro (Onaylanan)</p>
            <h3 class="text-3xl font-black text-slate-950 tracking-tight mb-1 relative z-10">
                {{ number_format($totalRevenue, 2) }} <span class="text-lg text-slate-400 font-bold">₺</span>
            </h3>
            <div class="flex items-center gap-1 text-xs font-bold text-emerald-500 relative z-10">
                <i class='bx bx-trending-up'></i>
                <span>{{ $totalProposals }} Teklif Üzerinden</span>
            </div>
        </div>

        <!-- Total Proposals -->
        <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm relative overflow-hidden group hover:border-blue-100 transition-all">
            <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50/50 rounded-full blur-3xl -mr-16 -mt-16 transition-all group-hover:bg-blue-100/50"></div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 relative z-10">Toplam Teklif</p>
            <h3 class="text-3xl font-black text-slate-950 tracking-tight mb-1 relative z-10">{{ $totalProposals }}</h3>
            <p class="text-xs font-bold text-slate-400 relative z-10">Oluşturulan</p>
        </div>

        <!-- Conversion Rate -->
        <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm relative overflow-hidden group hover:border-emerald-100 transition-all">
            <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-50/50 rounded-full blur-3xl -mr-16 -mt-16 transition-all group-hover:bg-emerald-100/50"></div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 relative z-10">Dönüşüm Oranı</p>
            <h3 class="text-3xl font-black text-slate-950 tracking-tight mb-1 relative z-10">%{{ number_format($conversionRate, 1) }}</h3>
            <p class="text-xs font-bold text-slate-400 relative z-10">Onaylanma Başarısı</p>
        </div>

        <!-- Avg Deal Size (Calculated in View for simplicity) -->
        @php
            $approvedCount = \App\Models\Proposal::where('status', 'approved')->whereBetween('proposal_date', [$startDate, $endDate])->count();
            $avgDeal = $approvedCount > 0 ? $totalRevenue / $approvedCount : 0;
        @endphp
        <div class="bg-white p-6 rounded-md border border-slate-100 shadow-sm relative overflow-hidden group hover:border-amber-100 transition-all">
            <div class="absolute right-0 top-0 w-32 h-32 bg-amber-50/50 rounded-full blur-3xl -mr-16 -mt-16 transition-all group-hover:bg-amber-100/50"></div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 relative z-10">Ort. Anlaşma Tutarı</p>
            <h3 class="text-3xl font-black text-slate-950 tracking-tight mb-1 relative z-10">
                {{ number_format($avgDeal, 0) }} <span class="text-lg text-slate-400 font-bold">₺</span>
            </h3>
            <p class="text-xs font-bold text-slate-400 relative z-10">Onay başına ortalama</p>
        </div>
    </div>

    <!-- Revenue Trend Chart -->
    <div class="bg-white p-8 rounded-md border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-black text-slate-950">Gelir Trendi</h3>
                <p class="text-sm font-medium text-slate-500">Seçili dönem için günlük onaylanan ciro analizi</p>
            </div>
            <div class="flex gap-2">
                 <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                    <span class="w-3 h-3 rounded-full bg-indigo-500"></span> Gelir
                 </span>
            </div>
        </div>
        <div id="revenueChart" class="min-h-[300px]"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Status Chart -->
        <div class="bg-white p-8 rounded-md border border-slate-100 shadow-sm h-fit">
            <h3 class="text-lg font-black text-slate-950 mb-6">Teklif Durum Dağılımı</h3>
            <div id="statusChart" class="min-h-[300px]"></div>
        </div>

        <!-- User Performance Table -->
        <div class="lg:col-span-2 bg-white p-8 rounded-md border border-slate-100 shadow-sm flex flex-col h-fit">
            <h3 class="text-lg font-black text-slate-950 mb-6">Ekip Performansı</h3>
            <div class="overflow-x-auto -mx-8 px-8 flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="py-4 text-xs font-black text-slate-400 uppercase tracking-widest w-1/3">Kullanıcı</th>
                            <th class="py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-center">Teklif / Onay</th>
                            <th class="py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-center">Başarı</th>
                            <th class="py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Ciro Katkısı</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($userPerformance as $user)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user['avatar'] }}" class="w-10 h-10 rounded-xl bg-slate-100" alt="">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">{{ $user['name'] }}</p>
                                        <div class="w-24 h-1.5 bg-slate-100 rounded-full mt-1 overflow-hidden">
                                            <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $user['conversion_rate'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-center">
                                <span class="text-xs font-bold text-slate-600">{{ $user['approved_proposals'] }} / {{ $user['total_proposals'] }}</span>
                            </td>
                            <td class="py-4 text-center">
                                <span class="text-sm font-bold {{ $user['conversion_rate'] >= 50 ? 'text-emerald-500' : ($user['conversion_rate'] >= 25 ? 'text-amber-500' : 'text-slate-400') }}">
                                    %{{ number_format($user['conversion_rate'], 1) }}
                                </span>
                            </td>
                            <td class="py-4 text-right">
                                <p class="text-sm font-black text-slate-900">{{ number_format($user['revenue'], 2) }} ₺</p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bottom Grids: Top Products & Customers -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Top Products -->
        <div class="bg-white p-8 rounded-md border border-slate-100 shadow-sm h-fit">
            <h3 class="text-lg font-black text-slate-950 mb-6 flex items-center gap-2">
                <i class='bx bxs-star text-amber-400'></i> En Çok Satan Ürünler/Hizmetler
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="pb-3 text-xs font-black text-slate-400 uppercase tracking-widest">Ürün</th>
                            <th class="pb-3 text-xs font-black text-slate-400 uppercase tracking-widest text-center">Adet</th>
                            <th class="pb-3 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Gelir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($topProducts as $product)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-3 font-bold text-slate-700 text-sm truncate max-w-[150px]">{{ $product->description }}</td>
                            <td class="py-3 text-center text-xs font-bold text-slate-500">{{ number_format($product->total_qty, 0) }}</td>
                            <td class="py-3 text-right font-black text-slate-900 text-sm">{{ number_format($product->total_revenue, 2) }} ₺</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-4 text-center text-xs text-slate-400 italic">Veri bulunamadı.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="bg-white p-8 rounded-md border border-slate-100 shadow-sm h-fit">
            <h3 class="text-lg font-black text-slate-950 mb-6 flex items-center gap-2">
                <i class='bx bxs-crown text-indigo-400'></i> En İyi Müşteriler
            </h3>
             <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="pb-3 text-xs font-black text-slate-400 uppercase tracking-widest">Müşteri</th>
                            <th class="pb-3 text-xs font-black text-slate-400 uppercase tracking-widest text-center">Onaylı</th>
                            <th class="pb-3 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Toplam Ciro</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($topCustomers as $customer)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-3 font-bold text-slate-700 text-sm truncate max-w-[150px]">{{ $customer->company_name }}</td>
                            <td class="py-3 text-center text-xs font-bold text-slate-500">{{ $customer->approved_count }}</td>
                            <td class="py-3 text-right font-black text-slate-900 text-sm">{{ number_format($customer->total_revenue, 2) }} ₺</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-4 text-center text-xs text-slate-400 italic">Veri bulunamadı.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Status Chart
    const statusData = {
        series: [
            {{ $statusDistribution['approved'] ?? 0 }}, 
            {{ $statusDistribution['pending'] ?? 0 }}, 
            {{ $statusDistribution['draft'] ?? 0 }}, 
            {{ $statusDistribution['rejected'] ?? 0 }}
        ],
        labels: ['Onaylandı', 'Bekliyor', 'Taslak', 'Reddedildi']
    };

    var options = {
        series: statusData.series,
        chart: {
            type: 'donut',
            height: 320,
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        labels: statusData.labels,
        colors: ['#10b981', '#f59e0b', '#64748b', '#ef4444'],
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Toplam',
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#64748b',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                            }
                        },
                        value: {
                            fontSize: '24px',
                            fontWeight: 800,
                            color: '#0f172a',
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        stroke: { show: false },
        legend: {
            position: 'bottom',
            fontFamily: 'Plus Jakarta Sans',
            fontWeight: 600,
            markers: { radius: 12 }
        }
    };

    var chart = new ApexCharts(document.querySelector("#statusChart"), options);
    chart.render();

    // Revenue Trend Chart
    var revenueOptions = {
        series: [{
            name: 'Ciro',
            data: {!! json_encode($chartData['revenue']) !!}
        }],
        chart: {
            height: 350,
            type: 'area',
            fontFamily: 'Plus Jakarta Sans, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: {!! json_encode($chartData['labels']) !!},
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: { colors: '#64748b', fontSize: '12px', fontWeight: 600 }
            }
        },
        yaxis: {
            labels: {
                style: { colors: '#64748b', fontSize: '11px', fontWeight: 600 },
                formatter: (value) => { return new Intl.NumberFormat('tr-TR', { notation: "compact", compactDisplay: "short" }).format(value) + ' ₺' }
            }
        },
        colors: ['#6366f1'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(val)
                }
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } }
        }
    };
    
    var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
    revenueChart.render();
</script>
@endsection
