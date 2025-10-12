<!-- <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Buku Tamu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e6e0f8; }
        .form-container {
            max-width: 600px; margin: 30px auto; background: #fff;
            padding: 25px 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .btn-purple { background-color: #7d3cff; color: #fff; }
        .btn-purple:hover { background-color: #5e2dbd; color: #fff; }
        .form-label { font-weight: 500; }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h3 class="text-center mb-4">Form Buku Tamu</h3>

            {{-- Error validasi global --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tamu.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama') }}" required>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Keperluan</label>
                    <input type="text" class="form-control @error('keperluan') is-invalid @enderror" name="keperluan" value="{{ old('keperluan') }}" placeholder="Contoh: Antar barang, rapat, seminar" required>
                    @error('keperluan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Kontak</label>
                    <input type="text" class="form-control @error('kontak') is-invalid @enderror" name="kontak" value="{{ old('kontak') }}" placeholder="No Handphone" required>
                    @error('kontak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Identitas</label>
                    <select class="form-select" name="identitas" id="identitas" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Orang Tua Siswa" {{ old('identitas')=='Orang Tua Siswa' ? 'selected' : '' }}>Orang Tua Siswa</option>
                        <option value="Alumni" {{ old('identitas')=='Alumni' ? 'selected' : '' }}>Alumni</option>
                        <option value="Lainnya" {{ old('identitas')=='Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    <input type="text" 
                           class="form-control mt-2" 
                           name="identitas_lainnya" 
                           id="identitas_lainnya" 
                           placeholder="Tulis identitas Anda" 
                           style="display: none;" 
                           value="{{ old('identitas_lainnya') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Dituju</label>
                    <input type="text" class="form-control @error('dituju') is-invalid @enderror" name="dituju" value="{{ old('dituju') }}" placeholder="Contoh: Kepala Sekolah, TU, Guru" required>
                    @error('dituju') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Jam Datang</label>
                    <input type="time" class="form-control @error('jam_datang') is-invalid @enderror" name="jam_datang" value="{{ old('jam_datang') }}" required>
                    @error('jam_datang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" name="tanggal" value="{{ old('tanggal') }}" required>
                    @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-purple w-100">Submit</button>
            </form>
        </div>
    </div>

    {{-- Script Identitas & Success Popup --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const select = document.getElementById("identitas");
            const inputLainnya = document.getElementById("identitas_lainnya");

            function toggleInput() {
                if (select.value === "Lainnya") {
                    inputLainnya.style.display = "block";
                    inputLainnya.required = true;
                } else {
                    inputLainnya.style.display = "none";
                    inputLainnya.required = false;
                    inputLainnya.value = "";
                }
            }

            select.addEventListener("change", toggleInput);
            toggleInput(); 
        });
    </script>

    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                alert(@json(session('success')));
                const f = document.querySelector('form');
                if (f) f.reset();
            });
        </script>
    @endif
</body>
</html>
-->