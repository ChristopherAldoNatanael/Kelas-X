<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #6a11cb;
            --gradient-angle: 135deg;
        }

        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(var(--gradient-angle), #f4f7fc, #e6f2ff);
        }

        .page-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .login-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            transform: scale(0.9);
            opacity: 0;
            animation: fadeInScale 0.6s forwards;
            position: relative;
            overflow: hidden;
        }

        @keyframes fadeInScale {
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(var(--gradient-angle), rgba(74, 144, 226, 0.1), rgba(106, 17, 203, 0.1));
            transform: rotate(-45deg);
            z-index: 1;
            pointer-events: none;
        }

        .form-control {
            transition: all 0.3s ease;
            border-radius: 10px;
        }

        .form-control:focus {
            box-shadow: 0 0 15px rgba(74, 144, 226, 0.3);
            border-color: var(--primary-color);
        }

        .btn-primary {
            background: linear-gradient(var(--gradient-angle), var(--primary-color), var(--secondary-color));
            border: none;
            transition: all 0.4s ease;
        }

        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(74, 144, 226, 0.3);
        }

        .login-footer {
            position: relative;
            z-index: 2;
        }

        .social-login {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .social-login a {
            margin: 0 10px;
            color: var(--primary-color);
            font-size: 24px;
            transition: transform 0.3s ease;
        }

        .social-login a:hover {
            transform: scale(1.2);
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-10px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(10px);
            }
        }

        .form-control.is-invalid {
            animation: shake 0.5s;
            border-color: #dc3545;
        }

        .footer {
            background-color: #4a90e2;
            /* Primary color */
            padding: 40px 0;
        }

        .footer h5 {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .footer a {
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: #6a11cb;
            /* Secondary color */
        }
    </style>
</head>

<body>
    <div class="page-container">
        <!-- Navbar (unchanged from previous version) -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <strong>Toko Online</strong>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                        <li class="nav-item"><a href="produk.php#" class="nav-link">Produk</a></li>
                        <li class="nav-item"><a href="cart.php" class="nav-link">Cart</a></li>
                        <li class="nav-item"><a href="about.php#" class="nav-link">About</a></li>
                        <li class="nav-item"><a href="kontak.php" class="nav-link">Kontak</a></li>
                    </ul>
                    <div class="d-flex">
                        <div class="input-group me-2">
                            <input type="text" class="form-control" placeholder="Search products">
                            <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </div>
                        <a href="register.php" class="btn btn-outline-primary me-2"><i class="bi bi-person-plus"></i> Register</a>
                        <a href="login.php" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Login Wrapper -->
        <div class="login-wrapper">
            <div class="login-container">
                <h2 class="text-center mb-4">Login to Your Account</h2>
                <form id="loginForm" action="process_login.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
                <div class="login-footer text-center mt-3">
                    <p>Don't have an account? <a href="register.php">Register</a></p>
                    <div class="social-login">
                        <a href="#" class="text-primary"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-danger"><i class="fab fa-google"></i></a>
                        <a href="#" class="text-info"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer (unchanged from previous version) -->
        <footer class="footer">
            <div class="container">
                <div class="row text-white">
                    <div class="col-md-3">
                        <h5>Menu</h5>
                        <ul class="list-unstyled">
                            <li><a href="index.php" class="text-white">Home</a></li>
                            <li><a href="produk.php" class="text-white">Produk</a></li>
                            <li><a href="about.php" class="text-white">About</a></li>
                            <li><a href="kontak.php" class="text-white">Kontak</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h5>Pembayaran</h5>
                        <p>Visa, MasterCard, PayPal</p>
                    </div>
                    <div class="col-md-3">
                        <h5>Media Sosial</h5>
                        <p>
                            <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i> Facebook</a>
                            <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i> Instagram</a>
                            <a href="#" class="text-white"><i class="fab fa-twitter"></i> Twitter</a>
                        </p>
                    </div>
                    <div class="col-md-3">
                        <h5>Kontak</h5>
                        <p>Email: <a href="mailto:contact@tokoonline.com" class="text-white">contact@tokoonline.com</a></p>
                        <p>Telepon: <a href="tel:08123456789" class="text-white">08123456789</a></p>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <p>&copy; 2025 Toko Online. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('email');
            const password = document.getElementById('password');

            // Simple validation example
            if (email.value === '' || password.value === '') {
                if (email.value === '') email.classList.add('is-invalid');
                if (password.value === '') password.classList.add('is-invalid');

                setTimeout(() => {
                    email.classList.remove('is-invalid');
                    password.classList.remove('is-invalid');
                }, 1000);
            } else {
                alert('Login successful!');
                // You would typically send this to your backend for processing
            }
        });
    </script>
</body>

</html>