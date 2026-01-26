<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sembodo Rent a Car</title>
    
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    {{-- Google Fonts (Poppins) --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/pages/login.css') }}">
</head>
<body class="login-page">

    {{-- LOGIN CARD --}}
    <div class="card card-login">
        
        {{-- LOGO --}}
        <div class="text-center mb-4">
            <img src="{{ asset('images/cars/logo/logo_sembodo.png') }}" 
                alt="Sembodo Rent a Car" 
                style="width: 100%; max-width: 150px; height: auto;">  
        </div>

        {{-- LOGIN FORM --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- INPUT USERNAME --}}
            <div class="mb-3">
                <label for="username" class="form-label">Nama Pengguna</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" 
                       id="username" name="username" value="{{ old('username') }}" 
                       placeholder="masukkan nama pengguna" required autofocus>
                
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
                    
                    <i class="fas fa-eye-slash toggle-password" id="toggleIcon" onclick="togglePassword()"></i>
                </div>

                @error('password')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- LOGIN BUTTON --}}
            <button type="submit" class="btn btn-black w-100">
                Masuk
            </button>

        </form>
    </div>

    {{-- FOOTER --}}
    <div class="footer-text">
        &copy; 2025 www.sembodoklastering.com - All Rights Reserved.
    </div>

    {{-- TOGGLE PASSWORD SCRIPT --}}
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