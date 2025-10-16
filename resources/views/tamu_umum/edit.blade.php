@extends('layouts.app')

@section('title', 'Edit Tamu Umum')
@section('page-title', 'Edit Data Tamu Umum')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-8">Form Edit Tamu Umum</h2>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ✅ Perbaikan di sini: kirim model langsung -->
            <form method="POST" action="{{ route('tamu_umum.update', $tamu_umum) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="nama"
                               value="{{ old('nama', $tamu_umum->nama) }}"
                               placeholder="Masukkan nama lengkap"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md"
                               required>
                    </div>

                    <!-- Identitas -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Identitas <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="identitas"
                               value="{{ old('identitas', $tamu_umum->identitas) }}"
                               placeholder="Masukkan identitas"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md"
                               required>
                    </div>

                    <!-- Keperluan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Keperluan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="keperluan" rows="3"
                                  placeholder="Jelaskan keperluan kunjungan"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-md"
                                  required>{{ old('keperluan', $tamu_umum->keperluan) }}</textarea>
                    </div>

                    <!-- Kontak -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kontak <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="kontak"
                               value="{{ old('kontak', $tamu_umum->kontak) }}"
                               placeholder="Nomor telepon atau email"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md"
                               required>
                    </div>

                    <!-- Guru Dituju -->
                    <div>
                        <label for="guru_dituju" class="block text-sm font-medium text-gray-700 mb-2">
                            Guru yang Dituju
                        </label>
                        <select id="guru_dituju" name="guru_dituju" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru }}">{{ $guru }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Waktu Kunjungan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Waktu Kunjungan <span class="text-red-500">*</span>
                        </label>
                        <input type="time"
                               name="waktu_kunjungan"
                               value="{{ old('waktu_kunjungan', $tamu_umum->waktu_kunjungan ? substr($tamu_umum->waktu_kunjungan, 0, 5) : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md"
                               required>
                    </div>

                    <!-- Tanggal Kunjungan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Kunjungan <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="tanggal_kunjungan"
                               value="{{ old('tanggal_kunjungan', $tamu_umum->tanggal_kunjungan ? (\Carbon\Carbon::parse($tamu_umum->tanggal_kunjungan)->format('Y-m-d')) : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-md">
                    </div>

                    <!-- Foto -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                        @if($tamu_umum->foto)
                            <div class="mb-4">
                                <img src="{{ asset('storage/' . $tamu_umum->foto) }}" class="w-24 h-24 object-cover rounded-lg">
                            </div>
                        @endif
                        <input type="file" name="foto" accept="image/*">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('tamu_umum.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-md">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-md">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
