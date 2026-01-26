<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sembodo Rent - @yield('title')</title>
    
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    
    @stack('styles')
</head>
<body>

    {{-- Mobile Sidebar Toggle Button --}}
    <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle navigation">
        <i class="fas fa-bars"></i>
    </button>

    {{-- Sidebar Overlay (Mobile) --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    {{-- SIDEBAR --}}
    <div class="sidebar d-flex flex-column" id="sidebar">
        
        {{-- LOGO --}}
        <div class="sidebar-brand">
            <div class="d-block">
                <img src="{{ asset('images/cars/logo/logo_sembodo.png') }}" alt="Logo Sembodo">
            </div>
        </div>

        {{-- NAVIGATION MENU --}}
        <nav class="nav flex-column px-3">
            <span class="text-secondary small fw-bold mb-2 px-2">MENU UTAMA</span>
            
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
                <i class="fas fa-chart-bar"></i> Hasil dan Laporan
            </a>
        </nav>

        {{-- LOGOUT BUTTON --}}
        <div class="text-center mt-auto">
            <button type="button" class="btn btn-logout px-5 py-2 fw-bold shadow-sm border-0" 
                data-bs-toggle="modal" data-bs-target="#modalLogout">
                Keluar
            </button>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        {{-- Header Bar --}}
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm mb-4">
            <h5 class="m-0 fw-bold text-dark">@yield('page-title')</h5>
            <div class="user-profile fw-bold text-secondary">
                <i class="fas fa-user-circle fa-lg me-2"></i> Admin
            </div>
        </div>

        {{-- Page Content --}}
        @yield('content')
    </div>

    {{-- LOGOUT MODAL --}}
    <x-modal id="modalLogout" icon="sign-out-alt" color="secondary" title="Ingin Keluar?">
        Apakah Anda yakin ingin mengakhiri sesi ini?
        <x-slot:actions>
            <button type="button" class="btn btn-light border fw-bold px-4" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-danger fw-bold px-4" 
                onclick="document.getElementById('logout-form').submit()">Ya, Keluar</button>
        </x-slot:actions>
    </x-modal>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Sidebar Toggle Script --}}
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.sidebar-toggle');
            
            if (window.innerWidth < 768 && 
                !sidebar.contains(e.target) && 
                !toggle.contains(e.target) &&
                sidebar.classList.contains('show')) {
                toggleSidebar();
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>