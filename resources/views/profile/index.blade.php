@extends('layouts.app')

@section('title', 'Admin Profile - Tamuin')
@section('page-title', 'Admin Profil')

@section('content')
<div class="space-y-6">
    <!-- Success/Error Alerts -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-green-700 hover:text-green-900" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" class="absolute top-0 right-0 mt-2 mr-2 text-red-700 hover:text-red-900" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Debug Info -->
    @if($user)
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
            User found: {{ $user->name }} ({{ $user->email }})
        </div>
    @else
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            No user found! Auth: {{ Auth::check() ? 'Yes' : 'No' }}
        </div>
    @endif

    <!-- Profile Section -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Banner -->
        <div class="relative h-32 bg-gradient-to-r from-purple-600 via-orange-500 to-yellow-400">
            <!-- Profile Picture -->
            <div class="absolute left-1/2 transform -translate-x-1/2 -bottom-16">
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-300 to-purple-500 flex items-center justify-center border-4 border-white shadow-lg">
                    <i class="fas fa-user text-white text-4xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Profile Info -->
        <div class="pt-20 pb-6 px-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $user->nama ?? $user->name ?? 'Nabila A' }}</h2>
                <p class="text-gray-600 mb-3">Admin</p>
                <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                    <span>username</span>
                    <div class="w-4 h-4 bg-orange-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-xs"></i>
                    </div>
                    <span>{{ $user->username ?? 'Nabila-Admin1' }}</span>
                </div>
            </div>

            <!-- Profile Form -->
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                        <input type="text" name="nama" value="{{ $user->nama ?? $user->name ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ $user->email ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" value="{{ $user->username ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg flex items-center space-x-2">
                        <i class="fas fa-file-alt"></i>
                        <span>Update Profile</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Section -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">CHANGE PASSWORD</h3>
        
        <form action="{{ route('profile.change-password') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input type="password" name="current_password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                @error('current_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" name="new_password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg flex items-center space-x-2">
                    <i class="fas fa-key"></i>
                    <span>Change Password</span>
                </button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
    {{ session('success') }}
</div>
@endif
@endsection 