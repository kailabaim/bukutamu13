<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Buku Tamu Instansi - SMKN 13 Bandung</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/assets/css/styles.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
  <!-- Tombol Kembali -->
  <a href="{{ route('landing') }}" class="back-btn">
    <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
  </a>

  <!-- Header -->
  <header>
    <h1><i class="fas fa-building animate-pulse"></i> BUKU TAMU INSTANSI<br>SMKN 13 BANDUNG</h1>
    <div class="social-icons">
      <a href="https://www.facebook.com/share/1FhqvT1T2P/"><i class="fab fa-facebook"></i></a>
      <a href="https://youtube.com/@smkn13bandungofficial?si=Y6pBBdaYOLR9Ls51"><i class="fab fa-youtube"></i></a>
      <a href="https://www.instagram.com/smkn13bandung?igsh=MTY3aGh3NDF0eno1dQ=="><i class="fab fa-instagram"></i></a>
    </div>
  </header>

  <div class="container">
    <h2><i class="fas fa-clipboard-list"></i> FORM KUNJUNGAN INSTANSI</h2>

<!-- Alert sukses -->
@if(session('success'))
  <div class="alert-success">
    {{ session('success') }}
  </div>
@endif

<!-- Alert error validasi -->
@if($errors->any())
  <div class="alert-error">
    <ul>
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif


    <form action="{{ route('guest.instansi.store') }}" method="POST" enctype="multipart/form-data" id="instansiForm">
      @csrf
      <div class="form-row">
        <div class="form-group">
          <label><i class="fas fa-user"></i> Nama Lengkap</label>
          <input type="text" name="nama" required placeholder="Masukkan nama lengkap Anda">
        </div>

        <div class="form-group">
          <label><i class="fas fa-building"></i> Instansi Asal</label>
          <input type="text" name="instansi_asal" required placeholder="Masukkan instansi Anda">
        </div>
      </div>

      <div class="form-group full-width">
        <label><i class="fas fa-clipboard"></i> Keperluan</label>
        <textarea name="keperluan" required rows="4" placeholder="Jelaskan tujuan kunjungan Anda secara singkat"></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label><i class="fas fa-phone"></i> Nomor Kontak</label>
          <input type="text" name="kontak" placeholder="Contoh: 08123456789">
        </div>

        <div class="form-group">
          <label><i class="fas fa-chalkboard-teacher"></i> Guru yang Dituju</label>
          <select name="guru">
            <option value="">-- Pilih Guru --</option>
            @foreach($gurus as $guru)
              <option value="{{ $guru }}">{{ $guru }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
            <label><i class="fas fa-users"></i> Jumlah Peserta</label>
            <input type="number" name="jumlah_peserta" min="1" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-clock"></i> Waktu Kunjungan</label>
            <input type="time" name="waktu" required readonly value="{{ now()->format('H:i') }}">
        </div>

        <div class="form-group">
            <label><i class="fas fa-calendar"></i> Tanggal Kunjungan</label>
            <input type="date" name="tanggal" required readonly value="{{ now()->toDateString() }}">
        </div>
    </div>

      <div class="form-group full-width">
        <label><i class="fas fa-camera"></i> Foto Pengunjung</label>

        <div class="camera-container">
          <!-- 🟢 Tambahan penting untuk peringatan kamera -->
          <div id="camera-warning" class="camera-warning" style="display:none;">
            <i class="fas fa-exclamation-triangle"></i>
            Pastikan browser memiliki akses kamera dan Anda mengizinkannya.
          </div>

          <div id="photo-preview" class="photo-preview">
            <div class="camera-placeholder">
              <i class="fas fa-user-circle"></i>
              <p>Klik tombol kamera untuk mengambil foto</p>
            </div>
          </div>

          <video id="camera" autoplay playsinline style="display:none;"></video>
          <canvas id="canvas" style="display:none;"></canvas>

          <div class="camera-controls">
            <button type="button" id="start-camera" class="camera-btn"><i class="fas fa-camera"></i> Buka Kamera</button>
            <button type="button" id="take-photo" class="camera-btn" style="display:none;"><i class="fas fa-camera-retro"></i> Ambil Foto</button>
            <button type="button" id="retake-photo" class="camera-btn" style="display:none;"><i class="fas fa-redo"></i> Foto Ulang</button>
            <button type="button" id="stop-camera" class="camera-btn" style="display:none;"><i class="fas fa-stop"></i> Tutup Kamera</button>
          </div>
        </div>
        <input type="hidden" id="foto_data" name="foto_data">
      </div>

      <button type="submit" class="submit-btn">
        <i class="fas fa-paper-plane"></i> Kirim Data Kunjungan
      </button>
    </form>
  </div>

  <footer>
    <p><i class="fas fa-school"></i> <strong>SMKN 13 BANDUNG</strong></p>
    <p>Menjadi sekolah kejuruan yang menghasilkan tamatan kompeten dan berkarakter</p>
    <p style="margin-top:10px;font-size:12px;">Dibuat Oleh Curi | Menggunakan HTML, CSS dan JavaScript</p>
  </footer>

  <script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>
