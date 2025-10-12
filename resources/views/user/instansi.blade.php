@extends('layouts.app')

@section('title', 'Tambah Tamu - Akademi')
@section('page-title', 'Tambah Tamu')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6 max-w-3xl">
	<form method="POST" action="{{ route('admin.tamu.store') }}" class="space-y-4">
		@csrf
		<div>
			<label class="block text-sm text-gray-700">Nama</label>
			<input name="nama" value="{{ old('nama') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required />
		</div>
		<div>
			<label class="block text-sm text-gray-700">Tanggal</label>
			<input type="date" name="tanggal" value="{{ old('tanggal') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required />
		</div>
		<div>
			<label class="block text-sm text-gray-700">Identitas</label>
			<input name="identitas" value="{{ old('identitas') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required />
		</div>
		<div>
			<label class="block text-sm text-gray-700">Keperluan</label>
			<input name="keperluan" value="{{ old('keperluan') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required />
		</div>
		<div>
			<label class="block text-sm text-gray-700">Dituju</label>
			<input name="dituju" value="{{ old('dituju') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required />
		</div>
		<div>
			<label class="block text-sm text-gray-700">Kontak</label>
			<input name="kontak" value="{{ old('kontak') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" />
		</div>
		<div class="grid grid-cols-2 gap-4">
			<div>
				<label class="block text-sm text-gray-700">Jam Datang</label>
				<input type="time" name="jam_datang" value="{{ old('jam_datang') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" />
			</div>
		</div>
		<div class="flex items-center space-x-3">
			<button class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">Simpan</button>
			<a href="{{ route('tamu') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">Batal</a>
		</div>
	</form>
</div>
@endsection 