<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Buku Tamu Umum - SMKN 13 Bandung</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
  <!-- Tombol Kembali -->
  <a href="{{ route('landing') }}" class="back-btn">
    <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
  </a>

  <!-- Header -->
  <header>
    <h1><i class="fas fa-users animate-pulse"></i> BUKU TAMU UMUM<br>SMKN 13 BANDUNG</h1>
  </header>

  <div class="container">
    <h2><i class="fas fa-clipboard-list"></i> FORM TAMU UMUM</h2>

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


    <form action="{{ route('guest.umum.store') }}" method="POST" enctype="multipart/form-data" id="umumForm">
      @csrf

      <div class="form-row">
        <div class="form-group">
          <label><i class="fas fa-user"></i> Nama Lengkap</label>
          <input type="text" name="nama" required placeholder="Masukkan nama lengkap Anda">
        </div>

        <div class="form-group">
          <label><i class="fas fa-id-card"></i> Identitas (KTP/NISN/Alumni)</label>
          <input type="text" name="identitas" required placeholder="Masukkan nomor identitas Anda">
        </div>
      </div>

      <div class="form-group full-width">
        <label><i class="fas fa-clipboard"></i> Keperluan</label>
        <textarea name="keperluan" required rows="4" placeholder="Jelaskan keperluan kunjungan Anda"></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label><i class="fas fa-phone"></i> Nomor Kontak</label>
          <input type="text" name="kontak" placeholder="Contoh: 08123456789">
        </div>

        <div class="form-group">
          <label><i class="fas fa-chalkboard-teacher"></i> Guru/Bagian yang Dituju</label>
          <select name="guru_dituju">
            <option value="">-- Pilih Guru --</option>
            @foreach($gurus as $guru)
              <option value="{{ $guru }}">{{ $guru }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
            <label><i class="fas fa-clock"></i> Waktu Kunjungan</label>
            <input type="time" name="waktu_kunjungan" required readonly value="{{ now()->format('H:i') }}">
        </div>

        <div class="form-group">
            <label><i class="fas fa-calendar"></i> Tanggal Kunjungan</label>
            <input type="date" name="tanggal_kunjungan" required readonly value="{{ now()->toDateString() }}">
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
  </footer>

  <script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>
