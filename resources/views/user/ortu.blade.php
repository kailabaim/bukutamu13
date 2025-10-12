<!--@extends('layouts.app')

@section('title', 'Form Orang Tua - Buku Tamu')

@section('content')
<div class="min-h-screen bg-[#d6a29d] flex flex-col items-center justify-center">
    <h2 class="text-3xl font-bold text-center mb-6 text-[#7d2d2d]">ORANG TUA SISWA/I</h2>
    
    <form action="{{ route('tamu.storeOrtu') }}" method="POST" class="bg-white rounded-xl shadow-lg p-8 w-full max-w-2xl">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-2">Nama Orang Tua</label>
            <input type="text" name="nama_ortu" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-2">Nama Siswa</label>
            <input type="text" name="nama_siswa" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-2">Kelas</label>
            <input type="text" name="kelas" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-2">Keperluan</label>
            <input type="text" name="keperluan" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-2">Tanggal</label>
            <input type="date" name="tanggal" class="w-full border rounded-lg px-3 py-2">
        </div>
        <button type="submit" class="bg-[#7d2d2d] text-white px-6 py-2 rounded-lg">Submit</button>
    </form>
</div>
@endsection-->
