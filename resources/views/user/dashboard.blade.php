<!--@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    
    <div class="relative">
        <img src="{{ asset('images/gedung.jpg') }}" class="w-full h-72 object-cover">
        <div class="absolute inset-0 bg-black/40 flex flex-col justify-center items-center text-white">
            <h1 class="text-3xl font-bold">BUKU TAMU SMKN 13 BANDUNG</h1>
            <p class="max-w-2xl text-center mt-2">
                Selamat datang di Buku Tamu SMKN 13 Bandung. Silakan pilih jenis tamu Anda
                untuk melanjutkan ke form pengisian data.
            </p>
        </div>
    </div>

   
    <div class="bg-rose-200 py-16 flex justify-center gap-8">
        <a href="{{ route('tamu.instansi') }}" 
           class="bg-white shadow-lg p-8 rounded-lg w-48 flex flex-col items-center hover:scale-105 transition">
            <i class="fas fa-university text-5xl mb-4"></i>
            <span class="font-bold">INSTANSI</span>
        </a>

        <a href="{{ route('tamu.orangtua') }}" 
           class="bg-white shadow-lg p-8 rounded-lg w-48 flex flex-col items-center hover:scale-105 transition">
            <i class="fas fa-user text-5xl mb-4"></i>
            <span class="font-bold">ORANG TUA SISWA/I</span>
        </a>
    </div>

    
    <div class="bg-red-900 text-white text-center py-6">
        <p>SMKN 13 BANDUNG</p>
        <p class="text-sm">Dikelola oleh Curif | Menggunakan Tailwind CSS dan Laravel</p>
    </div>
</div>
@endsection
-->