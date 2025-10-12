<?php

namespace App\Http\Controllers;

use App\Models\Tamu;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // tampilkan form
    public function create()
    {
        return view('user.form');
    }

    // simpan data tamu
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:100',
            'tanggal'   => 'required|date',
            'identitas' => 'required|string|max:100',
            'keperluan' => 'required|string|max:100',
            'dituju'    => 'required|string|max:100',
            'kontak'    => 'required|string|max:20',
            'waktu_kunjungan'=> 'required',
        ]);

        Tamu::create([
            'nama'       => $request->nama,
            'tanggal'    => $request->tanggal,
            'identitas'  => $request->identitas,
            'keperluan'  => $request->keperluan,
            'dituju'     => $request->dituju,
            'kontak'     => $request->kontak,
            'waktu_kunjungan' => $request->waktu_kunjungan,
        ]);

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }
}
