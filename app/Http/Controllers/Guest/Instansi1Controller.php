<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Instansi1Controller extends Controller
{
    public function create()
    {
        return view('guest.instansi');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'              => 'required|string|max:255',
            'instansi_asal'     => 'required|string|max:255',
            'keperluan'         => 'required|string',
            'kontak'            => 'required|string|max:20',
            'guru_dituju'       => 'required|string|max:255',
            'jumlah_peserta'    => 'required|integer|min:1',
            'waktu_kunjungan'   => 'required|regex:/^\d{2}:\d{2}$/',
            'tanggal_kunjungan' => 'required|date',
            'foto_data'         => 'nullable|string',
        ]);

        // Siapkan data untuk simpan
        $data = [
            'nama'              => $validated['nama'],
            'instansi_asal'     => $validated['instansi_asal'],
            'keperluan'         => $validated['keperluan'],
            'kontak'            => $validated['kontak'],
            'guru_dituju'       => $validated['guru_dituju'],
            'jumlah_peserta'    => $validated['jumlah_peserta'],
            'waktu_kunjungan'   => $validated['waktu_kunjungan'],
            'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
        ];

        // Simpan foto jika ada
        if (!empty($validated['foto_data'])) {
            if (preg_match('/^image\/(\w+);base64,/', $validated['foto_data'], $matches)) {
                $extension = strtolower($matches[1]);
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $imageData = base64_decode(substr($validated['foto_data'], strpos($validated['foto_data'], ',') + 1));
                    $filename = uniqid() . '.' . $extension;
                    Storage::disk('public')->put('instansi/' . $filename, $imageData);
                    $data['foto'] = 'instansi/' . $filename;
                }
            }
        }

        Instansi::create($data);

        return redirect()->back()->with('success', 'Data kunjungan instansi berhasil disimpan!');
    }
}