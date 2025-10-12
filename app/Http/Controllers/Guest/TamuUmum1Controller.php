<?php

namespace App\Http\Controllers;

use App\Models\TamuUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TamuUmum1Controller extends Controller
{
    public function create()
    {
        return view('guest.umum');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'              => 'required|string|max:255',
            'identitas'         => 'required|string|max:255',
            'keperluan'         => 'required|string',
            'guru_dituju'       => 'required|string|max:255',
            'kontak'            => 'required|string|max:20',
            'waktu_kunjungan'   => 'required|regex:/^\d{2}:\d{2}$/',
            'tanggal_kunjungan' => 'required|date',
            'foto_data'         => 'nullable|string',
        ]);

        $data = [
            'nama'              => $validated['nama'],
            'identitas'         => $validated['identitas'],
            'keperluan'         => $validated['keperluan'],
            'guru_dituju'       => $validated['guru_dituju'],
            'kontak'            => $validated['kontak'],
            'waktu_kunjungan'   => $validated['waktu_kunjungan'],
            'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
        ];

        if (!empty($validated['foto_data'])) {
            if (preg_match('/^image\/(\w+);base64,/', $validated['foto_data'], $matches)) {
                $extension = strtolower($matches[1]);
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $imageData = base64_decode(substr($validated['foto_data'], strpos($validated['foto_data'], ',') + 1));
                    $filename = uniqid() . '.' . $extension;
                    Storage::disk('public')->put('tamu_umum/' . $filename, $imageData);
                    $data['foto'] = 'tamu_umum/' . $filename;
                }
            }
        }

        TamuUmum::create($data);

        return redirect()->back()->with('success', 'Data tamu umum berhasil disimpan!');
    }
}