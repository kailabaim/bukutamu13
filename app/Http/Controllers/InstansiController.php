<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use App\Exports\InstansiExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class InstansiController extends Controller
{
    /**
     * Tampilkan daftar instansi dengan pencarian, filter bulan, dan urutan.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search'));
        $bulan  = $request->get('bulan');
        $sort   = $request->get('sort', 'newest');

        $instansi = Instansi::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('instansi_asal', 'like', "%{$search}%")
                      ->orWhere('keperluan', 'like', "%{$search}%")
                      ->orWhere('guru_dituju', 'like', "%{$search}%")
                      ->orWhere('kontak', 'like', "%{$search}%");
                });
            })
            ->when($bulan, fn($query) => $query->whereMonth('tanggal_kunjungan', $bulan))
            ->orderBy('tanggal_kunjungan', $sort === 'oldest' ? 'asc' : 'desc')
            ->select([
                'id',
                'nama',
                'instansi_asal',
                'keperluan',
                'guru_dituju',
                'kontak',
                'jumlah_peserta',
                'waktu_kunjungan',
                'tanggal_kunjungan',
                'foto' // ✅ PENTING: Jangan lupa foto!
            ])
            ->paginate(15)
            ->appends($request->only(['search', 'bulan', 'sort']));

        return view('instansi.index', compact('instansi', 'search', 'bulan', 'sort'));
    }

    /**
     * Export data ke Excel.
     */
    public function export(Request $request)
    {
        $filename = 'instansi_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new InstansiExport(
                $request->get('bulan'),
                $request->get('search'),
                $request->get('sort', 'newest')
            ),
            $filename
        );
    }

    /**
     * Form tambah instansi (untuk ADMIN).
     */
    public function create()
    {
        $gurus = \App\Models\Guru::orderBy('guru_nama', 'asc')->pluck('guru_nama');
        return view('instansi.create', compact('gurus'));
    }

    /**
     * Simpan data baru - SUPPORT 2 CARA:
     * 1. BASE64 dari camera (form guest)
     * 2. FILE UPLOAD biasa (form admin)
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama'           => 'required|string|max:255',
            'instansi_asal'  => 'required|string|max:255',
            'keperluan'      => 'required|string',
            'kontak'         => 'nullable|string|max:20',
            'guru'           => 'nullable|string|max:255', // ← Dari form GUEST (name="guru")
            'guru_dituju'    => 'nullable|string|max:255', // ← Dari form ADMIN (name="guru_dituju")
            'jumlah_peserta' => 'required|integer|min:1',
            'tanggal'        => 'nullable|date',          // ← Form GUEST
            'waktu'          => 'nullable|date_format:H:i', // ← Form GUEST
            'tanggal_kunjungan' => 'nullable|date',       // ← Form ADMIN
            'waktu_kunjungan'   => 'nullable|date_format:H:i', // ← Form ADMIN
            'foto_data'      => 'nullable|string',        // ← BASE64 dari camera (form guest)
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // ← FILE UPLOAD (form admin)
        ]);

        // Persiapkan data untuk disimpan
        $data = [
            'nama'              => $validated['nama'],
            'instansi_asal'     => $validated['instansi_asal'],
            'keperluan'         => $validated['keperluan'],
            'kontak'            => $validated['kontak'] ?? null,
            // ✅ Handle guru_dituju dari 2 form berbeda
            'guru_dituju'       => $validated['guru_dituju'] ?? $validated['guru'] ?? null,
            'jumlah_peserta'    => $validated['jumlah_peserta'],
            // ✅ Handle tanggal/waktu dari 2 form berbeda
            'tanggal_kunjungan' => $validated['tanggal_kunjungan'] ?? $validated['tanggal'] ?? now()->toDateString(),
            'waktu_kunjungan'   => $validated['waktu_kunjungan'] ?? $validated['waktu'] ?? now()->format('H:i'),
            'foto'              => null,
        ];

        // ============================================
        // HANDLE FOTO - 2 CARA:
        // ============================================
        
        // 1️⃣ CARA 1: FILE UPLOAD BIASA (dari form admin)
        if ($request->hasFile('foto')) {
            try {
                $path = $request->file('foto')->store('instansi', 'public');
                $data['foto'] = '/storage/' . $path;
                Log::info('✅ Foto berhasil diupload (file upload): ' . $data['foto']);
            } catch (\Exception $e) {
                Log::error('❌ Error upload foto: ' . $e->getMessage());
            }
        }
        
        // 2️⃣ CARA 2: BASE64 dari JavaScript Camera (dari form guest)
        elseif (!empty($validated['foto_data'])) {
            try {
                $base64String = $validated['foto_data'];
                
                // Validasi format base64 (data:image/jpeg;base64,...)
                if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
                    
                    // Ambil ekstensi file (jpg, png, gif)
                    $imageType = strtolower($type[1]);
                    
                    // Validasi tipe image yang diizinkan
                    if (!in_array($imageType, ['jpg', 'jpeg', 'png', 'gif'])) {
                        Log::warning('Format foto tidak valid: ' . $imageType);
                        return back()->withInput()->with('error', 'Format foto tidak valid. Gunakan JPG, PNG, atau GIF.');
                    }
                    
                    // Hapus prefix "data:image/jpeg;base64," 
                    $base64String = substr($base64String, strpos($base64String, ',') + 1);
                    $base64String = str_replace(' ', '+', $base64String);
                    
                    // Decode base64 ke binary
                    $imageData = base64_decode($base64String);
                    
                    if ($imageData === false) {
                        throw new \Exception('Gagal decode base64 image');
                    }
                    
                    // Cek ukuran file (max 5MB)
                    $fileSize = strlen($imageData);
                    if ($fileSize > 5 * 1024 * 1024) {
                        Log::warning('Foto terlalu besar: ' . round($fileSize / 1024) . ' KB');
                        return back()->withInput()->with('error', 'Ukuran foto terlalu besar (max 5MB).');
                    }
                    
                    // Generate nama file unik
                    $fileName = 'instansi_' . time() . '_' . uniqid() . '.' . $imageType;
                    
                    // Simpan ke storage/app/public/instansi/
                    Storage::disk('public')->put('instansi/' . $fileName, $imageData);
                    
                    // Path untuk database (akan diakses via /storage/instansi/...)
                    $data['foto'] = '/storage/instansi/' . $fileName;
                    
                    Log::info('✅ Foto berhasil disimpan (base64): ' . $data['foto'] . ' (' . round($fileSize / 1024) . ' KB)');
                    
                } else {
                    Log::warning('⚠️ Format base64 tidak valid, tidak ada header data:image');
                }
                
            } catch (\Exception $e) {
                Log::error('❌ Error menyimpan foto base64: ' . $e->getMessage());
                // Tidak return error, lanjutkan tanpa foto
            }
        } else {
            Log::info('ℹ️ Tidak ada foto yang dikirim');
        }

        // Simpan ke database
        Instansi::create($data);

        Log::info('✅ Data instansi berhasil disimpan: ' . $validated['nama']);

        // Redirect berdasarkan dari mana form disubmit
        if ($request->has('foto_data')) {
            // Dari form guest (dengan camera)
            return redirect()->route('landing')->with('success', '✅ Data kunjungan instansi berhasil disimpan! Terima kasih.');
        } else {
            // Dari form admin
            return redirect()->route('instansi.index')->with('success', '✅ Data instansi berhasil ditambahkan!');
        }
    }

    /**
     * Form edit data instansi (untuk admin).
     */
    public function edit(Instansi $instansi)
    {
        $gurus = \App\Models\Guru::orderBy('guru_nama', 'asc')->pluck('guru_nama');
        return view('instansi.edit', compact('instansi', 'gurus'));
    }

    /**
     * Update data instansi (untuk admin).
     */
    public function update(Request $request, Instansi $instansi)
    {
        $validated = $request->validate([
            'nama'              => 'required|string|max:255',
            'instansi_asal'     => 'required|string|max:255',
            'keperluan'         => 'required|string',
            'kontak'            => 'nullable|string|max:20',
            'guru_dituju'       => 'required|string|max:255',
            'jumlah_peserta'    => 'required|integer|min:1',
            'waktu_kunjungan'   => 'required|date_format:H:i',
            'tanggal_kunjungan' => 'required|date',
            'foto'              => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Jika ada upload foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($instansi->foto) {
                $oldPath = str_replace('/storage/', '', $instansi->foto);
                Storage::disk('public')->delete($oldPath);
                Log::info('🗑️ Foto lama dihapus: ' . $oldPath);
            }
            
            // Upload foto baru
            $path = $request->file('foto')->store('instansi', 'public');
            $validated['foto'] = '/storage/' . $path;
            
            Log::info('✅ Foto baru diupload: ' . $validated['foto']);
        }

        $instansi->update($validated);

        Log::info('✅ Data instansi berhasil diupdate: ID ' . $instansi->id);

        return redirect()->route('instansi.index')->with('success', 'Data instansi berhasil diperbarui!');
    }

    /**
     * Hapus data instansi.
     */
    public function destroy(Instansi $instansi)
    {
        // Hapus foto jika ada
        if ($instansi->foto) {
            $path = str_replace('/storage/', '', $instansi->foto);
            Storage::disk('public')->delete($path);
            Log::info('🗑️ Foto dihapus: ' . $path);
        }

        $instansi->delete();

        Log::info('✅ Data instansi berhasil dihapus: ID ' . $instansi->id);

        return redirect()->route('instansi.index')->with('success', 'Data instansi berhasil dihapus!');
    }
}