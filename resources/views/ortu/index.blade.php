@extends('layouts.app')

@section('title', 'Daftar Orang Tua - Tamuin')
@section('page-title', 'Daftar Orang Tua')

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

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span class="font-medium">Terjadi kesalahan:</span>
            </div>
            <ul class="mt-2 ml-4 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-red-700 hover:text-red-900" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <form method="GET" action="{{ route('ortu.index') }}" class="flex items-center gap-3 w-full">
                {{-- Search --}}
                <div class="relative flex-1">
                    <input name="search" value="{{ $search }}" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Cari orang tua..." />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>

                {{-- Filter Bulan --}}
                <select name="bulan" class="border px-2 py-1 rounded" onchange="this.form.submit()">
                    <option value="">-- Pilih Bulan --</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>

                {{-- Sorting --}}
                <select name="sort" class="border px-2 py-1 rounded" onchange="this.form.submit()">
                    <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Terlama</option>
                </select>

                {{-- Export Excel (ikut bulan, search, sort) --}}
                <a href="{{ route('ortu.export.excel', ['bulan' => $bulan, 'search' => $search, 'sort' => $sort]) }}"
                   class="bg-green-600 text-white px-3 py-1 rounded">
                    Export Excel
                </a>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('ortu.create') }}" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                        <i class="fas fa-plus mr-2"></i> Tambah Orang Tua
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-600 border-b">
                        <th class="py-3 px-4">NAMA ORANG TUA</th>
                        <th class="py-3 px-4">NAMA SISWA</th>
                        <th class="py-3 px-4">KELAS</th>
                        <th class="py-3 px-4">ALAMAT</th>
                        <th class="py-3 px-4">KONTAK</th>
                        <th class="py-3 px-4">GURU DITUJU</th>
                        <th class="py-3 px-4">KEPERLUAN</th>
                        <th class="py-3 px-4">WAKTU KUNJUNGAN</th>
                        <th class="py-3 px-4">TANGGAL KUNJUNGAN</th>
                        <th class="py-3 px-4">FOTO</th>
                        <th class="py-3 px-4 w-24">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orangTua as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <span class="font-medium text-gray-900">{{ $item->nama_orangtua }}</span>
                        </td>
                        <td class="py-3 px-4 text-gray-700">{{ $item->nama_siswa }}</td>
                        <td class="py-3 px-4 text-gray-700">{{ $item->kelas }}</td>
                        <td class="py-3 px-4 text-gray-700">{{ $item->alamat }}</td>
                        <td class="py-3 px-4 text-gray-700">{{ $item->kontak ?? '-' }}</td>
                        <td class="py-3 px-4 text-gray-700">{{ $item->guru_dituju }}</td>
                        <td class="py-3 px-4 text-gray-700">{{ Str::limit($item->keperluan, 50) ?? '-' }}</td>
                        <td class="py-3 px-4 text-gray-700">{{ $item->waktu_kunjungan ?? '-' }}</td>
                         <td class="py-3 px-4 text-gray-700">
                            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('M j, Y') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-gray-700">
                            @if($item->foto)
                            <img src="{{ $item->foto }}" alt="Foto {{ $item->nama_orangtua }}" class="w-12 h-12 object-cover rounded">
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('ortu.edit', $item) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('ortu.destroy', $item) }}" onsubmit="return confirm('Hapus data ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="py-6 text-center text-gray-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between mt-4">
            <p class="text-sm text-gray-600">Showing {{ $orangTua->firstItem() ?? 0 }} from {{ $orangTua->total() }} data</p>
            <div class="flex items-center space-x-2">
                {{ $orangTua->links() }}
            </div>
        </div>
    </div>
</div>
@endsection