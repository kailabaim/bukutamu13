@extends('layouts.app')

@section('title', 'Tambah Orang Tua')
@section('page-title', 'Tambah Data Orang Tua')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-8">Form Tambah Tamu</h2>

                <form method="POST" action="{{ route('ortu.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Orang Tua -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Orang Tua <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_orangtua" placeholder="Masukkan nama lengkap orang tua"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required>
                        </div>

                        <!-- Nama Siswa -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Siswa <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_siswa" placeholder="Masukkan nama lengkap siswa"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required>
                        </div>

                        <!-- kelas -->
                        <!-- kelas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Kelas <span class="text-red-500">*</span>
                            </label>
                            <select name="kelas" class="w-full px-4 py-3 border border-gray-300 rounded-md text-gray-900
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">-- Pilih Kelas --</option>

                                <!-- kelas 10 -->
                                <option value="X RPL 1">X RPL 1 </option>
                                <option value="X RPL 2">X RPL 2</option>
                                <option value="X TKJ 1">X TKJ 1</option>
                                <option value="X TKJ 2">X TKJ 2</option>
                                <option value="X TKJ 3">X TKJ 3 </option>
                                <option value="X KA 1">X KA 1</option>
                                <option value="X KA 2">X KA 2</option>
                                <option value="X KA 3">X KA 3</option>
                                <option value="X KA 4">X KA 4</option>
                                <option value="X KA 5">X KA 5</option>
                                <option value="X KA 6">X KA 6</option>

                                <!-- kelas 11 -->
                                <option value="XI RPL 1">XI RPL 1</option>
                                <option value="XI RPL 2">XI RPL 2</option>
                                <option value="XI TKJ 1">XI TKJ 2</option>
                                <option value="XI TKJ 2">XI TKJ 2</option>
                                <option value="XI TKJ 2">XI TKJ 3</option>
                                <option value="XI KA 1">XI KA 1</option>
                                <option value="XI KA 2">XI KA 2</option>
                                <option value="XI KA 3">XI KA 3</option>
                                <option value="XI KA 4">XI KA 4</option>
                                <option value="XI KA 5">XI KA 5</option>
                                <option value="XI KA 6">XI KA 6</option>

                                <!-- kelas 12 -->
                                <option value="XII RPL 1">XII RPL 1</option>
                                <option value="XII RPL 2">XII RPL 2</option>
                                <option value="XII TKJ 1">XII TKJ 1</option>
                                <option value="XII TKJ 2">XII TKJ 2</option>
                                <option value="XII TKJ 3">XII TKJ 3</option>
                                <option value="XII KA 1">XII KA 1</option>
                                <option value="XII KA 2">XII KA 2</option>
                                <option value="XII KA 3">XII KA 3</option>
                                <option value="XII KA 4">XII KA 4</option>
                                <option value="XII KA 5">XII KA 5</option>
                                <option value="XII KA 6">XII KA 6</option>

                                <!-- kelas 13 -->
                                <option value="XIII KA 1">XIII KA 1</option>
                                <option value="XIII KA 2">XIII KA 2</option>
                                <option value="XIII KA 3">XIII KA 3</option>
                                <option value="XIII KA 4">XIII KA 4</option>
                                <option value="XIII KA 5">XIII KA 5</option>
                                <option value="XIII KA 6">XIII KA 6</option>
                            </select>
                        </div>
                        <!-- alamat -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="alamat" placeholder="Masukkan alamat"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required>
                        </div>
                        <!-- Kontak -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Kontak <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="kontak" placeholder="Masukkan nomor telepon"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required>
                        </div>

                        <div>
                            <label for="guru" class="block text-sm font-medium text-gray-700 mb-2">
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
                        <!-- Keperluan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Keperluan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="keperluan" rows="3" placeholder="Jelaskan keperluan kunjungan Anda"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                required></textarea>
                        </div>
                        <!-- Waktu Kunjungan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Waktu Kunjungan <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="waktu_kunjungan" value="{{ date('H:i') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required>
                        </div>

                        <!-- Tanggal Kunjungan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Kunjungan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required>
                        </div>
                        <!-- Foto -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Foto
                            </label>
                            <div class="flex items-center justify-center w-full">
                                <label for="foto"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500">
                                            <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG atau JPEG (MAX. 2MB)</p>
                                    </div>
                                    <input id="foto" type="file" name="foto" class="hidden" accept="image/*">
                                </label>
                            </div>
                            <div id="preview-container" class="mt-4 hidden">
                                <div class="relative inline-block">
                                    <img id="image-preview" src="" alt="Preview"
                                        class="w-24 h-24 object-cover rounded-lg border border-gray-300">
                                    <button type="button" id="remove-image"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('ortu.index') }}"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200 font-medium">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 font-medium">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('foto').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('image-preview').src = e.target.result;
                    document.getElementById('preview-container').classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('remove-image').addEventListener('click', function () {
            document.getElementById('foto').value = '';
            document.getElementById('preview-container').classList.add('hidden');
        });
    </script>
@endsection