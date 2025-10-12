<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Instansi;
use App\Models\TamuUmum;
use App\Models\OrangTua;
use App\Models\Guru;

class GuestController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

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

    // ====== STORE INSTANSI ======
    public function storeInstansi(Request $request)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'instansi_asal' => 'required|string|max:255',
            'keperluan' => 'required|string',
            'tanggal' => 'required|date',
            'waktu' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()->all(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $fotoUrl = $this->saveImage($request, 'instansi');

        Instansi::create([
            'nama' => $request->nama,
            'instansi_asal' => $request->instansi_asal,
            'keperluan' => $request->keperluan,
            'guru_dituju' => $request->guru,
            'jumlah_peserta' => $request->jumlah_peserta,
            'kontak' => $request->kontak,
            'waktu_kunjungan' => $request->waktu,
            'tanggal_kunjungan' => $request->tanggal,
            'foto' => $fotoUrl,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data kunjungan instansi berhasil disimpan!',
            ]);
        }

        return back()->with('success', 'Data kunjungan instansi berhasil disimpan!');
    }

    // ====== STORE TAMU UMUM ======
    public function storeUmum(Request $request)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'identitas' => 'required|string|max:255',
            'keperluan' => 'required|string',
            'kontak' => 'required|string|max:20',
            'guru_dituju' => 'nullable|string',
            'tanggal_kunjungan' => 'required|date',
            'waktu_kunjungan' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()->all(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $fotoUrl = $this->saveImage($request, 'tamu_umum');

        TamuUmum::create([
            'nama' => $request->nama,
            'identitas' => $request->identitas,
            'keperluan' => $request->keperluan,
            'guru_dituju' => $request->guru_dituju,
            'kontak' => $request->kontak,
            'waktu_kunjungan' => $request->waktu_kunjungan,
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'foto' => $fotoUrl,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data tamu umum berhasil disimpan!',
            ]);
        }

        return back()->with('success', 'Data tamu umum berhasil disimpan!');
    }

    // ====== STORE ORANG TUA ======
    public function storeOrtu(Request $request)
    {
        $rules = [
            'nama_orangtua' => 'required|string|max:255',
            'nama_siswa' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'alamat' => 'required|string',
            'keperluan' => 'required|string',
            'kontak' => 'required|string|max:20',
            'guru_dituju' => 'required|string',
            'tanggal' => 'required|date',
            'waktu_kunjungan' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()->all(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $fotoUrl = $this->saveImage($request, 'orang_tua');

        OrangTua::create([
            'nama_orangtua' => $request->nama_orangtua,
            'nama_siswa' => $request->nama_siswa,
            'kelas' => $request->kelas,
            'alamat' => $request->alamat,
            'keperluan' => $request->keperluan,
            'kontak' => $request->kontak,
            'guru_dituju' => $request->guru_dituju,
            'tanggal' => $request->tanggal,
            'waktu_kunjungan' => $request->waktu_kunjungan,
            'foto' => $fotoUrl,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data kunjungan orang tua berhasil disimpan!',
            ]);
        }

        return back()->with('success', 'Data kunjungan orang tua berhasil disimpan!');
    }

    // ====== SIMPAN GAMBAR (base64 atau file) ======
    private function saveImage(Request $request, $folder)
    {
        // Jika dikirim via file upload (input type="file")
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

            return Storage::url($path);
        }

        return null;
    }
}