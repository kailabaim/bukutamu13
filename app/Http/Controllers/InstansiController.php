<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use App\Exports\InstansiExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            // ✅ PERBAIKAN: Tambahkan 'foto' dan 'kontak' ke select
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
                'foto' // <-- INI YANG KURANG!
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
     * Form tambah instansi.
     */
    public function create()
    {
        $gurus = \App\Models\Guru::orderBy('guru_nama', 'asc')->pluck('guru_nama');
        return view('instansi.create', compact('gurus'));
    }

    /**
     * Simpan data baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'           => 'required|string|max:255',
            'instansi_asal'  => 'required|string|max:255',
            'keperluan'      => 'required|string',
            'kontak'         => 'nullable|string|max:20',
            'guru_dituju'    => 'required|string|max:255',
            'jumlah_peserta' => 'required|integer|min:1',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Set tanggal & waktu otomatis
        $validated['tanggal_kunjungan'] = now()->toDateString();
        $validated['waktu_kunjungan']   = now()->toTimeString();

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $validated['foto'] = Storage::url($request->file('foto')->store('instansi', 'public'));
        }

        Instansi::create($validated);

        return redirect()->route('instansi.index')->with('success', 'Data instansi berhasil ditambahkan!');
    }

    /**
     * Form edit data instansi.
     */
    public function edit(Instansi $instansi)
    {
        $gurus = \App\Models\Guru::orderBy('guru_nama', 'asc')->pluck('guru_nama');
        return view('instansi.edit', compact('instansi','gurus'));
    }

    /**
     * Update data instansi.
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

        if ($request->hasFile('foto')) {
            if ($instansi->foto) {
                Storage::disk('public')->delete($instansi->foto);
            }
            $validated['foto'] = Storage::url($request->file('foto')->store('instansi', 'public'));
        }

        $instansi->update($validated);

        return redirect()->route('instansi.index')->with('success', 'Data instansi berhasil diperbarui!');
    }

    /**
     * Hapus data instansi.
     */
    public function destroy(Instansi $instansi)
    {
        if ($instansi->foto) {
            Storage::disk('public')->delete($instansi->foto);
        }

        $instansi->delete();

        return redirect()->route('instansi.index')->with('success', 'Data instansi berhasil dihapus!');
    }
}
