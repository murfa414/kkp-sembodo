<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sembodo Rent - @yield('title')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { background-color: #F3F4F6; font-family: sans-serif; }
        
        /* Sidebar Style */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #F8F9FC;
            position: fixed;
            top: 0; left: 0;
            border-right: 1px solid #ddd;
            padding-top: 20px;
            z-index: 1000; 
        }
        
        /* Content Style */
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        /* Menu Link Style */
        .nav-link { color: #5a5c69; font-weight: 500; padding: 12px 20px; margin-bottom: 5px; }
        .nav-link:hover { color: #4E73DF; background: #eaecf4; border-radius: 5px; }
        .nav-link.active { color: #4E73DF; font-weight: bold; background: #eaecf4; border-radius: 5px; }
        .nav-link i { width: 25px; text-align: center; margin-right: 10px; }

        /* --- [PERBAIKAN LOGO & GARIS PEMBATAS] --- */
        .sidebar-brand { 
            text-align: center; 
            margin-bottom: 20px;      /* Jarak dari garis ke menu di bawahnya */
            border-bottom: 1px solid #ddd; /* <--- INI GARIS PEMBATASNYA */
            padding-left: 15px;
            padding-right: 15px;
        }
        
        .sidebar-brand img { 
            width: 100%;
            max-width: 180px; 
            height: auto;
            object-fit: contain; 
        }
        
        .btn-logout { background-color: #b91c1c; color: white; margin: 20px; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="sidebar d-flex flex-column">
        
        {{-- LOGO --}}
        <div class="sidebar-brand">
            <div class="d-block">
                <img src="{{ asset('images/cars/logo/logo_sembodo.png') }}" alt="Logo Sembodo">
            </div>
        </div>

        <nav class="nav flex-column px-3">
            <span class="text-secondary small fw-bold mb-2 px-2">MENU UTAMA</span>
            
            <a href="{{ route('beranda.index') }}" class="nav-link {{ Request::is('/') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Beranda
            </a>
            
            <a href="{{ route('upload.index') }}" class="nav-link {{ Request::is('upload') ? 'active' : '' }}">
                <i class="fas fa-file-upload"></i> Impor Data
            </a>
            
            <a href="{{ route('kmeans.index') }}" class="nav-link {{ Request::routeIs('kmeans.*') ? 'active' : '' }}">
                <i class="fas fa-calculator"></i> Analisis K-Means
            </a>

            <a href="{{ route('laporan.index') }}" class="nav-link {{ Request::routeIs('laporan.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Hasil & Laporan
            </a>
        </nav>

        <div class="text-center mt-auto">
            {{-- Form Logout (Method POST) --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf {{-- Token Keamanan Wajib --}}
                <button type="submit" class="btn btn-logout px-5 py-2 fw-bold shadow-sm border-0">
                    LOGOUT
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm mb-4">
            <h5 class="m-0 fw-bold text-dark">@yield('page-title')</h5>
            <div class="user-profile fw-bold text-secondary">
                <i class="fas fa-user-circle fa-lg me-2"></i> Admin
            </div>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>