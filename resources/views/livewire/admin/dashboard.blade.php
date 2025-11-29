<?php

use function Livewire\Volt\{layout, mount, state};
use App\Models\User;
use App\Models\Konsultasi;
use App\Models\AreaRiset;
use App\Models\Pertanyaan;
use Illuminate\Support\Facades\DB;

layout('components.layouts.app-admin');

state([
    'totalStudents' => 0,
    'totalConsultations' => 0,
    'totalQuestions' => 0,
    'topRecommendedTopic' => 'Belum ada',
    'recentConsultations' => [],
    'chartData' => [],
    'showAllConsultations' => false,
]);

mount(function () {
    // Stats
    $this->totalStudents = User::where('role', '!=', 'admin')->count();
    $this->totalConsultations = Konsultasi::count();
    $this->totalQuestions = Pertanyaan::count();
    
    // Top Recommended Topic
    $topTopic = Konsultasi::whereNotNull('area_riset_final_id')
        ->select('area_riset_final_id', DB::raw('count(*) as total'))
        ->groupBy('area_riset_final_id')
        ->orderByDesc('total')
        ->first();
    
    if ($topTopic) {
        $areaRiset = AreaRiset::find($topTopic->area_riset_final_id);
        $this->topRecommendedTopic = $areaRiset ? $areaRiset->nama_area : 'Belum ada';
    } else {
        $this->topRecommendedTopic = 'Belum ada';
    }
    
    // Recent Consultations
    $this->recentConsultations = Konsultasi::with(['user', 'areaRisetFinal'])
        ->latest()
        ->take(5)
        ->get();
    
    // Chart Data - Consultations per Month for current year
    $this->chartData = Konsultasi::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->mapWithKeys(function ($item) {
            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return [$monthNames[$item->month - 1] => $item->count];
        })
        ->toArray();
});

?>

<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <!-- Welcome Card -->
    <div class="p-6 rounded-xl shadow-lg bg-gradient-to-l from-blue-800/50 to-blue-900/80 backdrop-blur-lg border border-white/20">
        <h2 class="text-2xl font-semibold text-white mb-2">Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}!</h2>
        <p class="text-blue-200">Kelola sistem pakar di sini.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid gap-4 grid-cols-1 md:grid-cols-4">
        <!-- Total Students -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-neutral-700">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-4">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Total Students</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalStudents }}</p>
                </div>
            </div>
        </div>

        <!-- Total Consultations -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-neutral-700">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-4">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-green-100 dark:bg-green-900/30">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Total Konsultasi</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalConsultations }}</p>
                </div>
            </div>
        </div>

        <!-- Top Recommended Topic -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-neutral-700">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-4">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-purple-100 dark:bg-purple-900/30">
                        <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Top Recommended Topic</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white line-clamp-1">{{ $topRecommendedTopic }}</p>
                </div>
            </div>
        </div>

        <!-- Total Questions -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-neutral-700">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-4">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-orange-100 dark:bg-orange-900/30">
                        <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Total Questions</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalQuestions }}</p>
                </div>
            </div>
        </div>
    </div>



    <!-- Recent Activity Table -->
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md border border-gray-200 dark:border-neutral-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Aktivitas Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-900">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Student Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Result</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-neutral-700">
                    @forelse(($showAllConsultations ? $recentConsultations : $recentConsultations->take(5)) as $consultation)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $consultation->user->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-neutral-400">{{ $consultation->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $consultation->areaRisetFinal->nama_area ?? 'Belum selesai' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($consultation->status === 'selesai')
                                    <a href="{{ route('admin.riwayat-konsultasi.index') }}?id={{ $consultation->id }}" 
                                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" 
                                       wire:navigate>
                                        Detail
                                    </a>
                                @else
                                    <span class="text-gray-400 dark:text-neutral-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-neutral-400">Belum ada konsultasi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($recentConsultations->count() > 5)
            <div class="p-6 border-t border-gray-200 dark:border-neutral-700 flex justify-center">
                <button wire:click="$set('showAllConsultations', !$showAllConsultations)" 
                        class="px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                    {{ $showAllConsultations ? 'Tampilkan Lebih Sedikit' : 'Tampilkan Lebih Banyak' }}
                </button>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData);
        const ctx = document.getElementById('consultationsChart');
        
        if (ctx && typeof Chart !== 'undefined') {
            const labels = Object.keys(chartData);
            const data = Object.values(chartData);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Konsultasi',
                        data: data,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    });
</script>

