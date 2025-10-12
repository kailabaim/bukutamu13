@extends('layouts.app')

@section('title', 'Kunjungan - Tamuin')
@section('page-title', 'Kunjungan')

@section('content')
<div class="space-y-6">
    <!-- Success/Error Alerts -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-green-700 hover:text-green-900" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-red-700 hover:text-red-900" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <!-- Total Tamu -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tamu</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_tamu'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-white text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Total Kunjungan -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Kunjungan</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_kunjungan'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar text-white text-lg"></i>
                </div>
            </div>
        </div>


        <!-- Tamu Orang Tua -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tamu Orang Tua</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['tamu_orangtua'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-friends text-white text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Tamu Instansi -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tamu Instansi</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['tamu_instansi'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-building text-white text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Tamu Umum -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tamu Umum</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['tamu_tamuUmum'] }}</p>
                </div>
                <div class="w-12 h-12 bg-pink-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Graph -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Performa Kunjungan Tamu {{ $year ?? date('Y') }}</h3>
            <div class="text-sm text-gray-600">
                Data tahun: {{ $year ?? date('Y') }}
            </div>
        </div>
        <div class="h-64">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Kunjungan Terbaru</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-600 border-b">
                        <th class="py-3 px-4">NAMA</th>
                        <th class="py-3 px-4">TIPE</th>
                        <th class="py-3 px-4">TANGGAL</th>
                        <th class="py-3 px-4">KEPERLUAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestVisits as $visit)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <span class="font-medium text-gray-900">{{ $visit->nama }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $visit->tipe === 'Orang Tua' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $visit->tipe }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-700">
                            {{ \Illuminate\Support\Carbon::parse($visit->tanggal)->format('d M Y') }}
                        </td>
                        <td class="py-3 px-4 text-gray-700">{{ $visit->keperluan }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-500">
                            <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                            <p>Belum ada data kunjungan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('performanceChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    if (!ctx) return;
    
    const performanceData = @json($performanceData);
    
    if (!performanceData || performanceData.length === 0) {
        // Tampilkan pesan jika tidak ada data
        const container = canvas.parentElement;
        container.innerHTML = '<div class="flex items-center justify-center h-64 text-gray-500"><i class="fas fa-chart-bar text-3xl mr-2"></i><p>Belum ada data untuk ditampilkan</p></div>';
        return;
    }
    
    try {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                    {
                        label: 'Total Kunjungan',
                        data: performanceData,
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' kunjungan';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.max(...performanceData) > 0 ? Math.max(...performanceData) + 2 : 10,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return value + ' kunjungan';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error creating chart:', error);
        const container = canvas.parentElement;
        container.innerHTML = '<div class="flex items-center justify-center h-64 text-red-500"><i class="fas fa-exclamation-triangle text-3xl mr-2"></i><p>Error loading chart</p></div>';
    }
});
</script>
@endpush
@endsection