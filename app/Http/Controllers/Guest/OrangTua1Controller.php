<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrangTua1Controller extends Controller
{
    public function create()
    {
        return view('guest.ortu');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_orangtua'     => 'required|string|max:255',
            'nama_siswa'        => 'required|string|max:255',
            'kelas'             => 'nullable|string|max:100',
            'alamat'            => 'required|string',
            'keperluan'         => 'nullable|string',
            'kontak'            => 'required|string|max:20',
            'guru_dituju'       => 'required|string|max:255',
            'waktu_kunjungan'   => 'required|regex:/^\d{2}:\d{2}$/',
            'tanggal'           => 'required|date',
            'foto_data'         => 'nullable|string',
        ]);

        $data = [
            'nama_orangtua'     => $validated['nama_orangtua'],
            'nama_siswa'        => $validated['nama_siswa'],
            'kelas'             => $validated['kelas'] ?? null,
            'alamat'            => $validated['alamat'],
            'keperluan'         => $validated['keperluan'] ?? null,
            'kontak'            => $validated['kontak'],
            'guru_dituju'       => $validated['guru_dituju'],
            'waktu_kunjungan'   => $validated['waktu_kunjungan'],
            'tanggal'           => $validated['tanggal'],
        ];

        if (!empty($validated['foto_data'])) {
            if (preg_match('/^image\/(\w+);base64,/', $validated['foto_data'], $matches)) {
                $extension = strtolower($matches[1]);
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $imageData = base64_decode(substr($validated['foto_data'], strpos($validated['foto_data'], ',') + 1));
                    $filename = uniqid() . '.' . $extension;
                    Storage::disk('public')->put('orangtua/' . $filename, $imageData);
                    $data['foto'] = 'orangtua/' . $filename;
                }
            }
        }

        OrangTua::create($data);

        return redirect()->back()->with('success', 'Data orang tua berhasil disimpan!');
    }
}