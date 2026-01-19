<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sembodo Rent a Car Analytics</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .input-focus {
            @apply focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            
            <!-- Left Side: Branding -->
            <div class="hidden lg:flex flex-col justify-center space-y-8">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 4H3a1 1 0 00-1-1zm5 16a2 2 0 11-4 0 2 2 0 014 0zm12 0a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Sembodo</h2>
                    </div>
                    <h1 class="text-5xl font-bold text-gray-900 mb-4 leading-tight">
                        Analytics Rental Mobil
                    </h1>
                    <p class="text-xl text-gray-600 mb-8">
                        Sistem analisis data mining K-Means untuk optimalisasi performa armada kendaraan rental Anda.
                    </p>
                </div>

                <!-- Features List -->
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md gradient-bg">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Klasterisasi Otomatis</h3>
                            <p class="text-gray-600">Mengidentifikasi 3 segmen performa: Laris, Sedang, Kurang Laris</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md gradient-bg">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Dashboard Interaktif</h3>
                            <p class="text-gray-600">Visualisasi data dengan chart ApexCharts yang responsif</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md gradient-bg">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Laporan Komprehensif</h3>
                            <p class="text-gray-600">Export hasil analisis dalam format PDF untuk presentasi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div>
                <div class="bg-white rounded-2xl shadow-xl p-8 md:p-10">
                    <!-- Logo Mobile -->
                    <div class="lg:hidden mb-8 text-center">
                        <div class="flex items-center justify-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg gradient-bg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 4H3a1 1 0 00-1-1zm5 16a2 2 0 11-4 0 2 2 0 014 0zm12 0a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Sembodo</h2>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">Masuk Sistem</h1>
                        <p class="text-gray-600 text-sm mt-2">Akses dashboard analisis performa armada</p>
                    </div>

                    <!-- Desktop Heading -->
                    <div class="hidden lg:block mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                        <p class="text-gray-600">Masuk untuk mengakses sistem analisis Anda</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Username Field -->
                        <div>
                            <label for="username" class="block text-sm font-semibold text-gray-900 mb-2">
                                Nama Pengguna
                            </label>
                            <input 
                                type="text" 
                                name="username" 
                                id="username"
                                value="{{ old('username') }}"
                                placeholder="Masukkan nama pengguna" 
                                required 
                                autofocus
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg input-focus"
                            >
                            @error('username')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 17.586l-6.687-6.687a1 1 0 00-1.414 1.414l8 8a1 1 0 001.414 0l8-8z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">
                                Kata Sandi
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password"
                                    placeholder="Masukkan kata sandi" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg input-focus pr-10"
                                >
                                <button 
                                    type="button" 
                                    id="toggleBtn"
                                    onclick="togglePassword()"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition"
                                >
                                    <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 1.657-.672 3.157-1.757 4.243A6 6 0 0121 12a6 6 0 00-9.757-5.657M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 17.586l-6.687-6.687a1 1 0 00-1.414 1.414l8 8a1 1 0 001.414 0l8-8z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="w-full gradient-bg text-white font-semibold py-3 px-4 rounded-lg hover:shadow-lg transition duration-200 flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Masuk
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <p class="text-center text-sm text-gray-600">
                            © 2025 Sembodo Analytics • All Rights Reserved
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>`;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 1.657-.672 3.157-1.757 4.243A6 6 0 0121 12a6 6 0 00-9.757-5.657M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>`;
            }
        }
    </script>

</body>
</html>
