<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use Carbon\Carbon;
use App\Exports\OrangTuaExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class OrangTuaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $bulan = $request->get('bulan');
        $sort = $request->get('sort', 'newest');

        $query = OrangTua::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_orangtua', 'like', "%{$search}%")
                  ->orWhere('nama_siswa', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('keperluan', 'like', "%{$search}%")
                  ->orWhere('guru_dituju', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

       if ($bulan) {
    $query->whereMonth('tanggal', $bulan); // Ganti created_at jadi tanggal
}

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $orangTua = $query->paginate(15);

        return view('ortu.index', compact('orangTua', 'search', 'bulan', 'sort'));
    }

    public function export(Request $request)
    {
        $bulan = $request->get('bulan');
        $search = $request->get('search');
        $sort = $request->get('sort', 'newest');

        $filename = 'orang_tua_siswa'.'.xlsx';

        return Excel::download(new OrangTuaExport($bulan, $search, $sort), $filename);
    }

    public function create()
    {
        $gurus = \App\Models\Guru::orderBy('guru_nama', 'asc')->pluck('guru_nama');
        return view('ortu.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_orangtua' => 'required|string|max:255',
            'nama_siswa' => 'required|string|max:255',
            'alamat' => 'required|string',
            'keperluan' => 'required|string',
            'kontak' => 'nullable|string|max:20',
            'guru_dituju' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'waktu_kunjungan' => 'required|date_format:H:i',
            'tanggal' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('orangtua', 'public');
            $data['foto'] = Storage::url($fotoPath);
        }
        OrangTua::create($data);

        // Redirect ke index setelah tambah
        return redirect()->route('ortu.index')->with('success', 'Data orang tua siswa berhasil ditambahkan!');
    }

    public function edit(OrangTua $ortu)
    {
        return view('ortu.edit', compact('ortu'));
    }

    public function update(Request $request, OrangTua $ortu)
    {
        $request->validate([
            'nama_orangtua' => 'required|string|max:255',
            'nama_siswa' => 'required|string|max:255',
            'alamat' => 'required|string',
            'keperluan' => 'required|string',
            'kontak' => 'nullable|string|max:20',
            'guru_dituju' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'waktu_kunjungan' => 'required|date_format:H:i:s',
            'tanggal' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($ortu->foto) {
                Storage::disk('public')->delete($ortu->foto);
            }
            $fotoPath = $request->file('foto')->store('orangtua', 'public');
            $data['foto'] = Storage::url($fotoPath);
        }

        $ortu->update($data);

        // Redirect ke index setelah edit
        return redirect()->route('ortu.index')->with('success', 'Data orang tua siswa berhasil diperbarui!');
    }

    public function destroy(OrangTua $ortu)
    {
        if ($ortu->foto) {
            Storage::disk('public')->delete($ortu->foto);
        }

        $ortu->delete();

        return redirect()->route('ortu.index')->with('success', 'Data orang tua siswa berhasil dihapus!');
    }
}