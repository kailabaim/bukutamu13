<!--@extends('layouts.app')

@section('title', 'Form Instansi - Buku Tamu')

@section('content')
<div class="min-h-screen bg-[#d6a29d] flex flex-col items-center justify-center">
    <h2 class="text-3xl font-bold text-center mb-6 text-[#7d2d2d]">INSTANSI</h2>
    
    <form action="{{ route('tamu.storeInstansi') }}" method="POST" class="bg-white rounded-xl shadow-lg p-8 w-full max-w-2xl">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-2">Nama</label>
            <input type="text" name="nama" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-2">Instansi/Perusahaan</label>
            <input type="text" name="instansi" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-2">Keperluan</label>
            <input type="text" name="keperluan" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-2">Bertemu Dengan</label>
            <select name="guru" class="w-full border rounded-lg px-3 py-2">
                <option>Pilih Guru</option>
                <option>Pak Jaya</option>
                <option>Bu Putri</option>
                <option>Lainnya</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-2">Tanggal</label>
            <input type="date" name="tanggal" class="w-full border rounded-lg px-3 py-2">
        </div>
        <button type="submit" class="bg-[#7d2d2d] text-white px-6 py-2 rounded-lg">Submit</button>
    </form>
</div>
@endsection-->
