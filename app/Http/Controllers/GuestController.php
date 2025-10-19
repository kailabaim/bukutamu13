<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Guru;

class GuestController extends Controller
{
    /**
     * Landing Page
     */
    public function landing()
    {
        return view('landing');
    }

    /**
     * Form Pages
     */
    public function instansi()
    {
        $gurus = Guru::orderBy('guru_nama', 'asc')->pluck('guru_nama');
        return view('guest.instansi', compact('gurus'));
    }

    public function umum()
    {
        $gurus = Guru::orderBy('guru_nama', 'asc')->pluck('guru_nama');
        return view('guest.umum', compact('gurus'));
    }

    public function ortu()
    {
        $gurus = Guru::orderBy('guru_nama', 'asc')->pluck('guru_nama');
        return view('guest.ortu', compact('gurus'));
    }

    /**
     * ==========================================
     * STORE INSTANSI
     * ==========================================
     */
    public function storeInstansi(Request $request)
    {
        try {
            Log::info('=== INSTANSI REQUEST START ===');
            Log::info('All Request Data:', $request->except(['foto_data', '_token']));

            // Validasi
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'instansi_asal' => 'required|string|max:255',
                'keperluan' => 'required|string',
                'kontak' => 'nullable|string|max:20',
                'guru' => 'nullable|string|max:255',
                'jumlah_peserta' => 'required|integer|min:1',
                'tanggal' => 'required|date',
                'waktu' => 'required',
                'foto_data' => 'nullable|string',
            ], [
                'nama.required' => 'Nama wajib diisi',
                'instansi_asal.required' => 'Instansi asal wajib diisi',
                'keperluan.required' => 'Keperluan wajib diisi',
                'jumlah_peserta.required' => 'Jumlah peserta wajib diisi',
                'tanggal.required' => 'Tanggal wajib diisi',
                'waktu.required' => 'Waktu wajib diisi',
            ]);

            Log::info('Validation passed');

            // Proses foto
            $fotoPath = $this->saveImage($request, 'instansi');
            Log::info('Photo saved:', ['path' => $fotoPath]);

            // Simpan ke database
            $data = [
                'nama' => $validated['nama'],
                'instansi_asal' => $validated['instansi_asal'],
                'keperluan' => $validated['keperluan'],
                'kontak' => $validated['kontak'] ?? null,
                'guru_dituju' => $validated['guru'] ?? null,
                'jumlah_peserta' => $validated['jumlah_peserta'],
                'tanggal_kunjungan' => $validated['tanggal'],
                'waktu_kunjungan' => $validated['waktu'],
                'foto' => $fotoPath,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('instansi')->insert($data);
            Log::info('Data inserted successfully');

            // Response untuk AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data kunjungan instansi berhasil disimpan!'
                ]);
            }

            return redirect()->back()->with('success', 'Data kunjungan instansi berhasil disimpan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', ['errors' => $e->errors()]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Error storing instansi:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * ==========================================
     * STORE TAMU UMUM
     * ==========================================
     */
    public function storeUmum(Request $request)
    {
        try {
            Log::info('=== TAMU UMUM REQUEST START ===');

            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'identitas' => 'required|string|max:255',
                'keperluan' => 'required|string',
                'kontak' => 'required|string|max:20',
                'guru_dituju' => 'nullable|string',
                'tanggal_kunjungan' => 'required|date',
                'waktu_kunjungan' => 'required',
                'foto_data' => 'nullable|string',
            ]);

            $fotoPath = $this->saveImage($request, 'tamu_umum');

            $data = [
                'nama' => $validated['nama'],
                'identitas' => $validated['identitas'],
                'keperluan' => $validated['keperluan'],
                'guru_dituju' => $validated['guru_dituju'] ?? null,
                'kontak' => $validated['kontak'],
                'waktu_kunjungan' => $validated['waktu_kunjungan'],
                'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
                'foto' => $fotoPath,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('tamu_umum')->insert($data);
            Log::info('Tamu Umum - Data saved successfully');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data tamu umum berhasil disimpan!'
                ]);
            }

            return redirect()->back()->with('success', 'Data tamu umum berhasil disimpan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', ['errors' => $e->errors()]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan!');
        }
    }

    /**
     * ==========================================
     * STORE ORANG TUA
     * ==========================================
     */
    public function storeOrtu(Request $request)
    {
        try {
            Log::info('=== ORANG TUA REQUEST START ===');

            $validated = $request->validate([
                'nama_orangtua' => 'required|string|max:255',
                'nama_siswa' => 'required|string|max:255',
                'kelas' => 'required|string|max:50',
                'alamat' => 'required|string',
                'keperluan' => 'required|string',
                'kontak' => 'required|string|max:20',
                'guru_dituju' => 'required|string',
                'tanggal' => 'required|date',
                'waktu_kunjungan' => 'required',
                'foto_data' => 'nullable|string',
            ]);

            $fotoPath = $this->saveImage($request, 'orang_tua');

            $data = [
                'nama_orangtua' => $validated['nama_orangtua'],
                'nama_siswa' => $validated['nama_siswa'],
                'kelas' => $validated['kelas'],
                'alamat' => $validated['alamat'],
                'keperluan' => $validated['keperluan'],
                'kontak' => $validated['kontak'],
                'guru_dituju' => $validated['guru_dituju'],
                'tanggal' => $validated['tanggal'],
                'waktu_kunjungan' => $validated['waktu_kunjungan'],
                'foto' => $fotoPath,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('orangtua')->insert($data);
            Log::info('Orang Tua - Data saved successfully');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data kunjungan orang tua berhasil disimpan!'
                ]);
            }

            return redirect()->back()->with('success', 'Data kunjungan orang tua berhasil disimpan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', ['errors' => $e->errors()]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan!');
        }
    }

    /**
     * ==========================================
     * HELPER: SAVE IMAGE
     * ==========================================
     */
    private function saveImage(Request $request, $folder)
    {
        try {
            // Jika dikirim via file upload
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store($folder, 'public');
                return Storage::url($path);
            }

            // Jika dikirim via kamera (base64)
            if ($request->filled('foto_data') && preg_match('/^data:image\/(\w+);base64,/', $request->foto_data, $matches)) {
                $data = substr($request->foto_data, strpos($request->foto_data, ',') + 1);
                $imageData = base64_decode($data);
                $ext = strtolower($matches[1]);
                $fileName = uniqid() . '.' . $ext;

                $directory = $folder;
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory, 0755, true);
                }

                $path = "$directory/$fileName";
                Storage::disk('public')->put($path, $imageData);

                Log::info('Image saved successfully:', ['path' => $path]);
                return Storage::url($path);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error saving image: ' . $e->getMessage());
            return null;
        }
    }
}