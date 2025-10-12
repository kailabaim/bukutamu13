<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buku Tamu SMKN 13 Bandung</title>

  {{-- Tailwind & FontAwesome --}}
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  {{-- Local CSS --}}
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

  <style>
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    @keyframes spin-slow {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    .animate-float { animation: float 3s ease-in-out infinite; }
    .animate-spin-slow { animation: spin-slow 4s linear infinite; }
    .category-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .category-card:hover { transform: translateY(-10px) scale(1.05); }
  </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

  <!-- Header -->
  <header class="relative">
    <img src="{{ asset('assets/img/sekolah.jpg') }}" alt="SMKN 13 Bandung" class="w-full h-96 object-cover">

    <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col justify-center items-start p-8 text-white">
      <h1 class="text-3xl md:text-4xl font-bold mb-4 leading-tight">
        BUKU TAMU<br>SMKN 13 BANDUNG
      </h1>
      <p class="max-w-xl text-sm md:text-base">
        Selamat Datang di Buku Tamu SMKN 13 Bandung.<br>
        Kehadiran Anda menjadi bagian penting dalam menjalin hubungan antara sekolah dengan masyarakat.
        Mohon isi data sesuai dengan kategori yang tersedia untuk mendukung kelancaran kunjungan Anda.
      </p>
    </div>

    <!-- Sosmed -->
    <div class="absolute top-4 right-4 flex items-center space-x-3 text-white text-xl z-10">

      <!-- Tombol Login -->
      <a href="{{ route('login') }}"
         class="text-sm bg-white/20 hover:bg-white/40 text-white px-4 py-2 rounded-full backdrop-blur-md transition-all duration-300 border border-white/30">
         <i class="fas fa-lock mr-2 text-xs"></i> Login Admin
      </a>

      <!-- Ikon Sosmed -->
      <div class="flex space-x-3">
        <a href="https://www.facebook.com/share/1FhqvT1T2P/" class="hover:text-blue-400 transition-colors duration-300"><i class="fab fa-facebook"></i></a>
        <a href="https://www.instagram.com/smkn13bandung?igsh=MTY3aGh3NDF0eno1dQ==" class="hover:text-pink-400 transition-colors duration-300"><i class="fab fa-instagram"></i></a>
        <a href="https://youtube.com/@smkn13bandungofficial?si=Y6pBBdaYOLR9Ls51" class="hover:text-red-500 transition-colors duration-300"><i class="fab fa-youtube"></i></a>
      </div>
    </div>
  </header>

  <!-- Kategori -->
  <main class="flex-grow bg-gradient-to-b from-gray-50 to-gray-100 py-20">
    <div class="container mx-auto px-6">
      <div class="text-center mb-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Pilih Kategori Kunjungan</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">Silakan pilih kategori yang sesuai dengan tujuan kunjungan Anda ke SMKN 13 Bandung</p>
      </div>

      <!-- Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-12 max-w-6xl mx-auto">

        <!-- Instansi -->
        <a href="{{ route('guest.instansi') }}"
           class="category-card bg-white p-8 rounded-3xl shadow-xl flex flex-col items-center hover:shadow-2xl group border border-gray-200">
          <div class="bg-gradient-to-r from-red-500 to-red-700 p-6 rounded-full mb-6 group-hover:from-red-600 group-hover:to-red-800 transition-all duration-300">
            <i class="fas fa-building text-4xl text-white animate-float"></i>
          </div>
          <h3 class="font-bold text-xl text-gray-800 mb-3 text-center">INSTANSI</h3>
          <p class="text-gray-600 text-sm text-center leading-relaxed">
            Kunjungan resmi dari lembaga pemerintah, swasta, atau organisasi lainnya
          </p>
          <div class="mt-4 flex items-center text-red-600 group-hover:text-red-700 transition-colors duration-300">
            <span class="text-sm font-medium mr-2">Klik untuk melanjutkan</span>
            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform duration-300"></i>
          </div>
        </a>

        <!-- Umum -->
        <a href="{{ route('guest.umum') }}"
           class="category-card bg-white p-8 rounded-3xl shadow-xl flex flex-col items-center hover:shadow-2xl group border border-gray-200">
          <div class="bg-gradient-to-r from-blue-500 to-blue-700 p-6 rounded-full mb-6 group-hover:from-blue-600 group-hover:to-blue-800 transition-all duration-300">
            <i class="fas fa-users text-4xl text-white animate-spin-slow"></i>
          </div>
          <h3 class="font-bold text-xl text-gray-800 mb-3 text-center">TAMU UMUM</h3>
          <p class="text-gray-600 text-sm text-center leading-relaxed">
            Kunjungan masyarakat umum, calon siswa, atau keperluan informasi sekolah
          </p>
          <div class="mt-4 flex items-center text-blue-600 group-hover:text-blue-700 transition-colors duration-300">
            <span class="text-sm font-medium mr-2">Klik untuk melanjutkan</span>
            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform duration-300"></i>
          </div>
        </a>

        <!-- Orang Tua -->
        <a href="{{ route('guest.ortu') }}"
           class="category-card bg-white p-8 rounded-3xl shadow-xl flex flex-col items-center hover:shadow-2xl group border border-gray-200">
          <div class="bg-gradient-to-r from-green-500 to-green-700 p-6 rounded-full mb-6 group-hover:from-green-600 group-hover:to-green-800 transition-all duration-300">
            <i class="fas fa-heart text-4xl text-white animate-pulse"></i>
          </div>
          <h3 class="font-bold text-xl text-gray-800 mb-3 text-center">ORANG TUA SISWA</h3>
          <p class="text-gray-600 text-sm text-center leading-relaxed">
            Kunjungan wali murid untuk keperluan akademik atau konsultasi pendidikan
          </p>
          <div class="mt-4 flex items-center text-green-600 group-hover:text-green-700 transition-colors duration-300">
            <span class="text-sm font-medium mr-2">Klik untuk melanjutkan</span>
            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform duration-300"></i>
          </div>
        </a>
      </div>

      <!-- Info -->
      <div class="text-center mt-16">
        <div class="bg-white p-6 rounded-xl shadow-lg max-w-4xl mx-auto border border-gray-200">
          <div class="flex items-center justify-center mb-4">
            <i class="fas fa-info-circle text-blue-500 text-2xl mr-3"></i>
            <h3 class="text-lg font-semibold text-gray-800">Informasi Penting</h3>
          </div>
          <p class="text-gray-600 text-sm leading-relaxed">
            Pastikan Anda membawa identitas diri yang valid. Untuk kunjungan instansi, harap membawa surat resmi.
            Jam operasional: Senin–Jumat (07:00–17:00).
          </p>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-red-900 text-white py-6 text-center">
    <h2 class="font-bold">SMKN 13 BANDUNG</h2>
    <p class="text-sm max-w-xl mx-auto mt-2">
      Menjadi sekolah kejuruan yang menghasilkan tamatan kompeten, berkarakter, berbudaya, serta mampu bersaing di era global.
    </p>
    <p class="mt-4 text-xs">Dibuat oleh Curi ©2025</p>
  </footer>

  {{-- Local JS --}}
  <script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>
