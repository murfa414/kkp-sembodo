<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sembodo Rent - @yield('title')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Primary) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        /* Sidebar Style */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #ffffff;
            position: fixed;
            top: 0;
            left: 0;
            border-right: 1px solid #e5e7eb;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        /* Content Style */
        .main-content {
            margin-left: 260px;
            padding: 24px;
            min-height: 100vh;
            background: #f3f4f6;
        }

        /* Menu Link Style */
        .nav-link {
            color: #6b7280;
            font-weight: 500;
            padding: 12px 16px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .nav-link:hover {
            color: #3b82f6;
            background: #eff6ff;
        }

        .nav-link.active {
            color: #3b82f6;
            font-weight: 600;
            background: #eff6ff;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .sidebar-brand {
            text-align: center;
            padding: 20px 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .sidebar-brand img {
            width: 100%;
            max-width: 160px;
            height: auto;
            object-fit: contain;
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Logo -->
        <div class="sidebar-brand">
            <img src="{{ asset('images/cars/logo/logo_sembodo.png') }}" alt="Logo Sembodo">
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-3 block">Menu Utama</span>

            <a href="{{ route('beranda.index') }}" class="nav-link {{ Request::is('/') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Beranda
            </a>

            <a href="{{ route('upload.index') }}" class="nav-link {{ Request::is('upload') ? 'active' : '' }}">
                <i class="fas fa-file-upload"></i> Impor Data
            </a>

            <a href="{{ route('kmeans.index') }}" class="nav-link {{ Request::routeIs('kmeans.*') ? 'active' : '' }}">
                <i class="fas fa-calculator"></i> Analisis
            </a>

            <a href="{{ route('laporan.index') }}" class="nav-link {{ Request::routeIs('laporan.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Hasil Laporan
            </a>
        </nav>

        <!-- Logout Button -->
        <div class="p-4 border-t border-gray-200">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="bg-white p-4 rounded-xl shadow-sm mb-6 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-900">@yield('page-title')</h1>
            <div class="flex items-center gap-2 text-gray-600 font-medium">
                <i class="fas fa-user-circle text-lg"></i> Admin
            </div>
        </div>

        <!-- Page Content -->
        @yield('content')
    </div>

    <!-- ApexCharts (for charts) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @stack('scripts')

</body>

</html>