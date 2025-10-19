<?php

namespace App\Http\Controllers;

use App\Models\TamuUmum;
use Carbon\Carbon;
use App\Exports\TamuUmumExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TamuUmumController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $bulan = $request->get('bulan');
        $sort = $request->get('sort', 'newest');

        $query = TamuUmum::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('identitas', 'like', "%{$search}%")
                    ->orWhere('keperluan', 'like', "%{$search}%")
                    ->orWhere('guru_dituju', 'like', "%{$search}%");
            });
        }

        if (!empty($bulan)) {
            $query->whereMonth('tanggal_kunjungan', $bulan);
        }

        if ($sort === 'oldest') {
            $query->orderBy('tanggal_kunjungan', 'asc');
        } else {
            $query->orderBy('tanggal_kunjungan', 'desc');
        }

        $tamu_umum = $query->paginate(15);

        return view('tamu_umum.index', compact('tamu_umum', 'search', 'bulan', 'sort'));
    }

    public function export(Request $request)
    {
        $bulan = $request->get('bulan');
        $search = $request->get('search');
        $sort = $request->get('sort', 'newest');

        $filename = 'tamu_umum_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new TamuUmumExport($bulan, $search, $sort), $filename);
    }

    public function create()
    {
        $gurus = \App\Models\Guru::orderBy('guru_nama', 'asc')->pluck('guru_nama');
        return view('guest.umum', compact('gurus'));
    }

    public function store(Request $request)
    {
        try {
            // Log request size untuk debugging
            $requestSize = strlen(serialize($request->all()));
            \Log::info('Request size: ' . round($requestSize / 1024, 2) . ' KB');

            // Validasi input
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'identitas' => 'required|string|max:255',
                'keperluan' => 'required|string|max:1000',
                'guru_dituju' => 'nullable|string|max:255',
                'kontak' => 'nullable|string|max:20',
                'waktu_kunjungan' => 'required|date_format:H:i',
                'tanggal_kunjungan' => 'required|date',
                'foto_data' => 'nullable|string|max:700000', // Max ~700KB Base64
            ]);

            // Handle foto Base64 jika ada
            $fotoPath = null;
            if ($request->filled('foto_data')) {
                try {
                    $fotoData = $request->input('foto_data');
                    
                    // Validasi format Base64
                    if (preg_match('/^data:image\/(\w+);base64,/', $fotoData, $matches)) {
                        $imageType = $matches[1]; // jpeg, png, dll
                        $fotoData = substr($fotoData, strpos($fotoData, ',') + 1);
                        $fotoData = str_replace(' ', '+', $fotoData);
                        $fotoDecoded = base64_decode($fotoData);

                        // Validasi ukuran (max 2MB)
                        $fotoSize = strlen($fotoDecoded);
                        if ($fotoSize > 2 * 1024 * 1024) {
                            return back()->withErrors(['foto_data' => 'Ukuran foto terlalu besar (max 2MB)'])->withInput();
                        }

                        // Generate nama file unik
                        $fileName = 'umum_' . date('Ymd_His') . '_' . Str::random(6) . '.' . $imageType;
                        
                        // Simpan ke storage/app/public/photos/umum
                        $path = 'photos/umum/' . $fileName;
                        Storage::disk('public')->put($path, $fotoDecoded);
                        
                        $fotoPath = Storage::url($path); // URL lengkap
                        \Log::info('Photo saved: ' . $fotoPath);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error saving photo: ' . $e->getMessage());
                    // Lanjutkan tanpa foto jika error
                }
            }

            // Simpan data ke database
            TamuUmum::create([
                'nama' => $validated['nama'],
                'identitas' => $validated['identitas'],
                'keperluan' => $validated['keperluan'],
                'guru_dituju' => $validated['guru_dituju'] ?? null,
                'kontak' => $validated['kontak'] ?? null,
                'waktu_kunjungan' => $validated['waktu_kunjungan'],
                'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
                'foto' => $fotoPath,
            ]);

            return redirect()->route('guest.umum.create')
                ->with('success', '✅ Data tamu umum berhasil disimpan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error storing tamu umum: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(TamuUmum $tamu_umum)
    {
        $gurus = \App\Models\Guru::orderBy('guru_nama', 'asc')->pluck('guru_nama');
        return view('tamu_umum.edit', compact('tamu_umum', 'gurus'));
    }

    public function update(Request $request, TamuUmum $tamu_umum)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'identitas' => 'required|string',
            'identitas_lainnya' => 'nullable|string|max:255',
            'keperluan' => 'required|string|max:255',
            'guru_dituju' => 'required|string|max:255',
            'kontak' => [
                'required',
                'regex:/^(\+62|62|0)[0-9]{9,13}$/'
            ],
            'waktu_kunjungan' => 'required',
            'tanggal_kunjungan' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($data['identitas'] === 'Lainnya' && !empty($data['identitas_lainnya'])) {
            $data['identitas'] = $data['identitas_lainnya'];
        }
        unset($data['identitas_lainnya']);

        // Jika ada foto baru (file upload dari form edit)
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($tamu_umum->foto) {
                $oldPath = $tamu_umum->foto;
                if (str_starts_with($oldPath, '/storage/')) {
                    $oldPath = str_replace('/storage/', '', $oldPath);
                }
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Simpan foto baru
            $newPath = $request->file('foto')->store('photos/umum', 'public');
            $data['foto'] = Storage::url($newPath);
        }

        $tamu_umum->update($data);

        return redirect()->route('tamu_umum.index')->with('success', 'Data tamu umum berhasil diperbarui!');
    }

    public function destroy(TamuUmum $tamu_umum)
    {
        if ($tamu_umum->foto) {
            $path = $tamu_umum->foto;
            if (str_starts_with($path, '/storage/')) {
                $path = str_replace('/storage/', '', $path);
            }
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $tamu_umum->delete();

        return redirect()->route('tamu_umum.index')->with('success', 'Data tamu umum berhasil dihapus!');
    }
}