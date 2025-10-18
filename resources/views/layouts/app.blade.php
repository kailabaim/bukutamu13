<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tamuin - Dashboard')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @stack('styles')
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-red-700 via-red-800 to-red-900 text-white shadow-xl">
            <!-- Logo -->
            <div class="p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-lg">T</span>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-white to-red-200 bg-clip-text text-transparent">Tamuin</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="{{ route('dashboard.index') }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard.*') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg' : 'hover:bg-gradient-to-r hover:from-red-600/50 hover:to-red-700/50 text-white/80 hover:text-white' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('tamu_umum.index') }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('tamu*') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg' : 'hover:bg-gradient-to-r hover:from-red-600/50 hover:to-red-700/50 text-white/80 hover:text-white' }}">
                        <i class="fas fa-user"></i>
                        <span>Umum</span>
                    </a>
                    <a href="{{ route('ortu.index') }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('ortu*') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg' : 'hover:bg-gradient-to-r hover:from-red-600/50 hover:to-red-700/50 text-white/80 hover:text-white' }}">
                        <i class="fas fa-users"></i>
                        <span>Orang Tua Siswa</span>
                    </a>
                    <a href="{{ route('instansi.index') }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('instansi*') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg' : 'hover:bg-gradient-to-r hover:from-red-600/50 hover:to-red-700/50 text-white/80 hover:text-white' }}">
                        <i class="fas fa-building"></i>
                        <span>Instansi</span>
                    </a>
                    <a href="{{ route('kunjungan') }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('kunjungan') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg' : 'hover:bg-gradient-to-r hover:from-red-600/50 hover:to-red-700/50 text-white/80 hover:text-white' }}">
                        <i class="fas fa-calendar"></i>
                        <span>Kunjungan</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-gradient-to-r from-white to-gray-50 shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <h1 class="text-2xl font-semibold bg-gradient-to-r from-red-700 via-red-800 to-red-900 bg-clip-text text-transparent">
                        @yield('page-title', 'Dashboard')
                    </h1>

                    <!-- User Info and Actions -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ Auth::user()->nama ?? Auth::user()->name ?? 'Admin' }}
                                </div>
                                <div class="text-xs text-gray-500">Admin</div>
                            </div>
                            <div class="w-8 h-8 bg-gradient-to-br from-red-700 via-red-800 to-red-900 rounded-full flex items-center justify-center shadow-md">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                        </div>
                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-red-600 transition-colors duration-200" title="Logout">
                                <i class="fas fa-sign-out-alt text-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 via-white to-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>