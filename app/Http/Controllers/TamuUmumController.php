<?php

namespace App\Http\Controllers;

use App\Models\TamuUmum;
use Carbon\Carbon;
use App\Exports\TamuUmumExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        // ✅ perbaikan: pake $tamu_umum
        $tamu_umum = $query->paginate(15);

        return view('tamu_umum.index', compact('tamu_umum', 'search', 'bulan', 'sort'));
    }

    public function export(Request $request)
    {
        $bulan = $request->get('bulan');
        $search = $request->get('search');
        $sort = $request->get('sort', 'newest');

        $filename = 'tamu_umum' . '.xlsx';

        return Excel::download(new TamuUmumExport($bulan, $search, $sort), $filename);
    }

    public function create()
    {
        $gurus = \App\Models\Guru::orderBy('guru_nama', 'asc')->pluck('guru_nama');
        return view('tamu_umum.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'identitas' => 'required|string',
            'keperluan' => 'required|string|max:255',
            'guru_dituju' => 'required|string|max:255',
            'kontak' => [
                'required',
                'regex:/^(\+62|62|0)[0-9]{9,13}$/'
            ],
            'waktu_kunjungan' => 'required',
            'tanggal_kunjungan' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'kontak.regex' => 'Nomor telepon harus berupa angka dan sesuai format Indonesia (contoh: 081234567890).'
        ]);

        $identitas = $request->identitas == 'Lainnya'
            ? $request->identitas_lainnya
            : $request->identitas;

        $fotoUrl = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('tamu_umum', 'public');
            $fotoUrl = Storage::url($path); // ✅ Simpan URL lengkap: /storage/tamu_umum/...
        }

        TamuUmum::create([
            'nama' => $request->nama,
            'identitas' => $identitas,
            'keperluan' => $request->keperluan,
            'guru_dituju' => $request->guru_dituju,
            'kontak' => $request->kontak,
            'waktu_kunjungan' => $request->waktu_kunjungan,
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'foto' => $fotoUrl, // ✅ Simpan URL, bukan path
        ]);

        return redirect()->route('tamu_umum.index')->with('success', 'Data tamu umum berhasil disimpan!');
    }

    public function edit(TamuUmum $tamu_umum)
    {
        return view('tamu_umum.edit', compact('tamu_umum'));
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

        // Jika ada foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama (jika berupa path relatif atau URL)
            if ($tamu_umum->foto) {
                // Ambil path relatif dari URL
                $oldPath = $tamu_umum->foto;
                if (str_starts_with($oldPath, '/storage/')) {
                    $oldPath = substr($oldPath, strlen('/storage/'));
                }
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Simpan foto baru sebagai URL
            $newPath = $request->file('foto')->store('tamu_umum', 'public');
            $data['foto'] = Storage::url($newPath); // ✅ URL lengkap
        }

        $tamu_umum->update($data);

        return redirect()->route('tamu_umum.index')->with('success', 'Data tamu umum berhasil diperbarui!');
    }

    public function destroy(TamuUmum $tamu_umum)
    {
        if ($tamu_umum->foto) {
            $path = $tamu_umum->foto;
            if (str_starts_with($path, '/storage/')) {
                $path = substr($path, strlen('/storage/'));
            }
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $tamu_umum->delete();

        return redirect()->route('tamu_umum.index')->with('success', 'Data tamu umum berhasil dihapus!');
    }
}
