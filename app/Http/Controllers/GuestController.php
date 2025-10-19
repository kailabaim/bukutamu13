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
            Log::info('Request Data:', $request->except(['foto_data', '_token']));
            Log::info('Has foto_data:', ['has' => $request->filled('foto_data')]);

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
                'jumlah_peserta.min' => 'Jumlah peserta minimal 1 orang',
                'tanggal.required' => 'Tanggal wajib diisi',
                'waktu.required' => 'Waktu wajib diisi',
            ]);

            Log::info('✅ Validation passed');

            // Proses foto
            $fotoPath = $this->saveImage($request, 'instansi');
            Log::info('Photo processing result:', ['path' => $fotoPath]);

            // Simpan ke database
            $data = [
                'nama' => trim($validated['nama']),
                'instansi_asal' => trim($validated['instansi_asal']),
                'keperluan' => trim($validated['keperluan']),
                'kontak' => $validated['kontak'] ? trim($validated['kontak']) : null,
                'guru_dituju' => $validated['guru'] ?? null,
                'jumlah_peserta' => (int) $validated['jumlah_peserta'],
                'tanggal_kunjungan' => $validated['tanggal'],
                'waktu_kunjungan' => $validated['waktu'],
                'foto' => $fotoPath,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $id = DB::table('instansi')->insertGetId($data);
            Log::info('✅ Data inserted successfully', ['id' => $id]);

            // Response untuk AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '✅ Data kunjungan instansi berhasil disimpan!',
                    'data' => ['id' => $id]
                ]);
            }

            // Response normal (redirect)
            return redirect()->route('landing')
                ->with('success', '✅ Terima kasih! Data kunjungan Anda berhasil disimpan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation failed:', ['errors' => $e->errors()]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Mohon periksa kembali form Anda.');

        } catch (\Exception $e) {
            Log::error('❌ Error storing instansi:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', '❌ Terjadi kesalahan sistem. Silakan coba lagi.')
                ->withInput();
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
                'guru' => 'nullable|string', // ✅ Ubah dari guru_dituju
                'tanggal' => 'required|date',
                'waktu' => 'required',
                'foto_data' => 'nullable|string',
            ]);

            $fotoPath = $this->saveImage($request, 'tamu_umum');

            $data = [
                'nama' => trim($validated['nama']),
                'identitas' => trim($validated['identitas']),
                'keperluan' => trim($validated['keperluan']),
                'guru_dituju' => $validated['guru'] ?? null, // ✅ Mapping ke guru_dituju
                'kontak' => trim($validated['kontak']),
                'waktu_kunjungan' => $validated['waktu'],
                'tanggal_kunjungan' => $validated['tanggal'],
                'foto' => $fotoPath,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $id = DB::table('tamu_umum')->insertGetId($data);
            Log::info('✅ Tamu Umum - Data saved', ['id' => $id]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '✅ Data tamu umum berhasil disimpan!'
                ]);
            }

            return redirect()->route('landing')
                ->with('success', '✅ Terima kasih! Data kunjungan Anda berhasil disimpan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation failed:', ['errors' => $e->errors()]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('❌ Error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', '❌ Terjadi kesalahan sistem.')
                ->withInput();
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
                'guru' => 'required|string', // ✅ Ubah dari guru_dituju
                'tanggal' => 'required|date',
                'waktu' => 'required',
                'foto_data' => 'nullable|string',
            ]);

            $fotoPath = $this->saveImage($request, 'orang_tua');

            $data = [
                'nama_orangtua' => trim($validated['nama_orangtua']),
                'nama_siswa' => trim($validated['nama_siswa']),
                'kelas' => trim($validated['kelas']),
                'alamat' => trim($validated['alamat']),
                'keperluan' => trim($validated['keperluan']),
                'kontak' => trim($validated['kontak']),
                'guru_dituju' => $validated['guru'], // ✅ Mapping
                'tanggal' => $validated['tanggal'],
                'waktu_kunjungan' => $validated['waktu'],
                'foto' => $fotoPath,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $id = DB::table('orangtua')->insertGetId($data);
            Log::info('✅ Orang Tua - Data saved', ['id' => $id]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '✅ Data kunjungan orang tua berhasil disimpan!'
                ]);
            }

            return redirect()->route('landing')
                ->with('success', '✅ Terima kasih! Data kunjungan Anda berhasil disimpan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation failed:', ['errors' => $e->errors()]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('❌ Error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', '❌ Terjadi kesalahan sistem.')
                ->withInput();
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
            // ✅ Prioritas 1: File upload langsung
            if ($request->hasFile('foto')) {
                Log::info('📁 Saving uploaded file...');
                $path = $request->file('foto')->store($folder, 'public');
                $url = Storage::url($path);
                Log::info('✅ File uploaded', ['url' => $url]);
                return $url;
            }

            // ✅ Prioritas 2: Base64 dari kamera
            if ($request->filled('foto_data')) {
                $fotoData = $request->foto_data;
                
                // Cek format base64
                if (preg_match('/^data:image\/(\w+);base64,/', $fotoData, $matches)) {
                    $imageType = strtolower($matches[1]);
                    
                    // Extract base64 content
                    $base64Data = substr($fotoData, strpos($fotoData, ',') + 1);
                    $imageDecoded = base64_decode($base64Data, true);
                    
                    if ($imageDecoded === false || strlen($imageDecoded) === 0) {
                        Log::warning('⚠️ Base64 decode failed or empty result');
                        return null;
                    }
                    
                    // Generate filename
                    $fileName = uniqid('img_') . '_' . time() . '.' . $imageType;
                    $path = "$folder/$fileName";
                    
                    // Buat folder jika belum ada
                    if (!Storage::disk('public')->exists($folder)) {
                        Storage::disk('public')->makeDirectory($folder, 0755, true);
                        Log::info('📁 Directory created:', ['folder' => $folder]);
                    }
                    
                    // Simpan file
                    $saved = Storage::disk('public')->put($path, $imageDecoded);
                    
                    if ($saved) {
                        $url = Storage::url($path);
                        $sizeKB = strlen($imageDecoded) / 1024;
                        Log::info('✅ Base64 image saved', [
                            'path' => $path,
                            'url' => $url,
                            'size' => round($sizeKB, 2) . ' KB'
                        ]);
                        return $url;
                    } else {
                        Log::error('❌ Failed to save image to storage');
                        return null;
                    }
                    
                } else {
                    Log::warning('⚠️ Invalid base64 image format');
                    return null;
                }
            }

            Log::info('ℹ️ No image provided');
            return null;

        } catch (\Exception $e) {
            Log::error('❌ Error saving image:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            return null;
        }
    }
}