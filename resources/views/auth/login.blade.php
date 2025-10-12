<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SMK Negeri 13 Bandung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Left Column - School Building Image -->
        <div class="hidden lg:flex lg:w-1/2 relative">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
                 style="background-image: url('{{ asset('assets/sekolah.jpg') }}');">
            </div>
            
            <!-- Overlay for better text readability -->
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            
            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-center px-12 text-white">
                <!-- Title -->
                <div class="relative mb-8">
                    <h1 class="text-5xl font-bold leading-tight drop-shadow-2xl">
                        <span class="block text-orange-200">SMK NEGERI 13</span>
                        <span class="block text-white">BANDUNG</span>
                    </h1>
                </div>
                
                <!-- Description text -->
                <div class="text-lg leading-relaxed max-w-lg drop-shadow-lg">
                    <p class="mb-4 text-orange-100">
                        SMK Negeri 13 Bandung adalah sekolah menengah kejuruan di Bandung yang mendidik siswa dalam bidang Analisis Kimia, Teknik Komputer dan Jaringan, serta Rekayasa Perangkat Lunak.
                    </p>
                    <p class="text-orange-50">
                        Sekolah ini berawal dari pendidikan analis kimia di bawah pengelolaan Jurusan Kimia Institut Teknologi Bandung (ITB), dimulai pada tahun 1938, dan dipelopori oleh Prof. C.O. SCHAEFFER.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Login Form Card -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <!-- Login Title -->
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Login</h2>
                        <p class="text-gray-600">Welcome onboard with us!</p>
                    </div>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Admin Email ID Field -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Admin Email ID
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                placeholder="Enter your username" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-colors"
                                required
                            >
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="Enter your password" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-colors"
                                required
                            >
                        </div>

                        <!-- Login Button -->
                        <button 
                            type="submit" 
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2"
                        >
                            <span>Login as Admin</span>
                            <i class="fas fa-arrow-right text-white"></i>
                        </button>

                        <!-- Register Link -->
                        <div class="text-center mt-4">
                            <p class="text-gray-600">
                                Belum punya akun? 
                                <a href="{{ route('register') }}" class="text-orange-500 hover:text-orange-600 font-medium">
                                    Daftar di sini
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 