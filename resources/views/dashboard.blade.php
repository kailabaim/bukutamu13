@extends('layouts.app')

@section('title', 'Dashboard - Buku Tamu')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    .chart-container {
        position: relative;
        height: 250px;
    }
    .badge-type {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
        border-radius: 0.375rem;
        font-weight: 500;
    }
    .badge-orang-tua { background-color: #dcfce7; color: #166534; }
    .badge-instansi { background-color: #dbeafe; color: #1d4ed8; }
    .badge-umum { background-color: #fef3c7; color: #92400e; }
    .avatar-circle {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.75rem;
    }
    .orang-tua-bg { background-color: #10b981; }
    .instansi-bg { background-color: #3b82f6; }
    .umum-bg { background-color: #f59e0b; }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-600">Total Tamu</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ $totalTamu }}</p>
        </div>
        <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-600">Total Kunjungan</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ $totalKunjungan }}</p>
        </div>
        <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-600">Orang Tua</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ $totalOrangtua }}</p>
        </div>
        <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-600">Instansi</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ $totalInstansi }}</p>
        </div>
        <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-600">Umum</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ $totalUmum }}</p>
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3">Distribusi Tipe Tamu</h3>
        <div class="chart-container">
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <!-- Daftar Tamu Terbaru -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3">Tamu Terbaru</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 px-1 font-semibold text-gray-700">Nama</th>
                        <th class="text-left py-2 px-1 font-semibold text-gray-700">Tipe</th>
                        <th class="text-left py-2 px-1 font-semibold text-gray-700">Tanggal</th>
                        <th class="text-left py-2 px-1 font-semibold text-gray-700">Keperluan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($latestGuests as $guest)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-1">
                                <div class="flex items-center">
                                    <div class="avatar-circle mr-2
                                        @if($guest->tipe == 'Orang Tua') orang-tua-bg
                                        @elseif($guest->tipe == 'Instansi') instansi-bg
                                        @else umum-bg
                                        @endif
                                    "></div>
                                    <span class="font-medium text-gray-900">{{ $guest->nama }}</span>
                                </div>
                            </td>
                            <td class="py-2 px-1">
                                <span class="badge-type
                                    @if($guest->tipe == 'Orang Tua') badge-orang-tua
                                    @elseif($guest->tipe == 'Instansi') badge-instansi
                                    @else badge-umum
                                    @endif
                                ">{{ $guest->tipe }}</span>
                            </td>
                            <td class="py-2 px-1 text-gray-700">
                                {{ \Carbon\Carbon::parse($guest->tanggal)->format('d M Y') }}
                            </td>
                            <td class="py-2 px-1 text-gray-700">
                                {{ Str::limit($guest->keperluan, 20) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-500">
                                Belum ada tamu
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    const chartData = @json($chartData);
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: chartData.data,
                backgroundColor: chartData.colors,
                borderWidth: 2,
                borderColor: '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        font: { size: 11 }
                    }
                }
            }
        }
    });
</script>
@endpush