<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sembodo Rent a Car</title>
    
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome (Untuk Ikon Mata) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    {{-- Google Fonts (Poppins) --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #9e999988; /* Warna latar gelap sesuai mockup */
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .login-title {
            color: #000000;
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
        }

        .card-login {
            background: #ffffff;
            border-radius: 15px; /* Sudut membulat */
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .form-label {
            font-weight: 700; /* Tebal judul input */
            color: #000;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 10px; /* Sudut input membulat */
            padding: 12px 15px;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #000;
        }

        .btn-black {
            background-color: #000000;
            color: #ffffff;
            font-weight: 700;
            padding: 12px;
            border-radius: 10px; /* Samakan dengan input */
            transition: all 0.3s;
        }

        .btn-black:hover {
            background-color: #333;
            color: #fff;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }

        .footer-text {
            margin-top: 3rem;
            color: #000000;
            font-size: 0.85rem;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- JUDUL DI ATAS KARTU --}}
    {{-- <div class="login-title">Masuk</div> --}}

    {{-- KARTU LOGIN --}}
    <div class="card card-login">
        
        {{-- LOGO --}}
        <div class="text-center mb-4">
            {{-- Pastikan kamu punya file logo ini di public/images/ --}}
            <img src="{{ asset('images/cars/logo/logo_sembodo.png') }}" 
                alt="Sembodo Rent a Car" 
                style="width: 100%; max-width: 150px; height: auto;">  
        </div>

        {{-- FORM LOGIN --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- INPUT username --}}
            <div class="mb-3">
                <label for="username" class="form-label">Nama Pengguna</label>
                <input type="username" class="form-control @error('username') is-invalid @enderror" 
                       id="username" name="username" value="{{ old('username') }}" 
                       placeholder="masukkan nama pengguna" required autofocus>
                
                {{-- Pesan Error Validasi --}}
                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- INPUT PASSWORD --}}
            <div class="mb-4">
                <label for="password" class="form-label">Kata Sandi</label>
                <div class="password-container">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" 
                           placeholder="masukkan kata sandi" required>
                    
                    {{-- Ikon Mata (Toggle Show/Hide) --}}
                    <i class="fas fa-eye-slash toggle-password" id="toggleIcon" onclick="togglePassword()"></i>
                </div>

                @error('password')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- TOMBOL MASUK --}}
            <button type="submit" class="btn btn-black w-100">
                Masuk
            </button>

        </form>
    </div>

    {{-- FOOTER --}}
    <div class="footer-text">
        &copy; 2025 www.sembodoklastering.com - All Rights Reserved.
    </div>

    {{-- SCRIPT TOGGLE PASSWORD --}}
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var icon = document.getElementById("toggleIcon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }
    </script>

</body>
</html>