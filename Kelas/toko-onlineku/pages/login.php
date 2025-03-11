<?php
session_start();
include("../includes/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        $stmt = $koneksi->prepare("SELECT * FROM pelanggan WHERE email = ? AND (role IS NULL OR role = 'user')");
        $stmt->execute([$email]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Check if password in database is hashed
            if (strlen($row['password']) < 60) { // Not hashed
                if ($password === $row['password']) {
                    $_SESSION['pelanggan_id'] = $row['pelanggan_id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['role'] = 'user';
                    header("Location: ../index.php");
                    exit();
                } else {
                    $error = "Incorrect email or password.";
                }
            } else {
                // Password is hashed, use password_verify
                if (password_verify($password, $row['password'])) {
                    $_SESSION['pelanggan_id'] = $row['pelanggan_id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['role'] = 'user';
                    header("Location: ../index.php");
                    exit();
                } else {
                    $error = "Incorrect email or password.";
                }
            }
        } else {
            $error = "Incorrect email or password.";
        }
    } catch (PDOException $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}



// Function to get cart count
function getCartCount($koneksi, $pelanggan_id)
{
    try {
        $sql = "SELECT SUM(quantity) as total FROM cart WHERE pelanggan_id = :pelanggan_id";
        $stmt = $koneksi->prepare($sql);
        $stmt->execute([':pelanggan_id' => $pelanggan_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        error_log("Error getting cart count: " . $e->getMessage());
        return 0;
    }
}

// Get cart count if user is logged in
$cartCount = 0;
if (isset($_SESSION['pelanggan_id'])) {
    $cartCount = getCartCount($koneksi, $_SESSION['pelanggan_id']);
    // Add debugging
    error_log("Cart count for user {$_SESSION['pelanggan_id']}: $cartCount");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EliteWatch - Login</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #000000;
            --secondary-color: #1a1a1a;
            --accent-color: #D4AF37;
            --text-color: #ffffff;
        }

        body {
            background-color: var(--primary-color);
            color: var(--text-color);
            font-family: 'Helvetica Neue', sans-serif;
            overflow-x: hidden;
        }

        /* Enhanced Navigation Styles */
        /* Updated Navbar Styles */
        .navbar {
            background: rgba(10, 10, 10, 0.95) !important;
            backdrop-filter: blur(20px);
            padding: 1rem 0;
            transition: all 0.4s ease;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar>.container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: 2px;
            background: linear-gradient(135deg, var(--accent-color), #f8e7b3);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            padding: 0.5rem 0;
            margin-right: auto;
            /* Pushes everything else to the right */
        }

        .navbar-brand::after {
            content: 'LUXURY TIMEPIECES';
            position: absolute;
            bottom: 0;
            left: 5px;
            font-size: 0.8rem;
            letter-spacing: 3px;
            color: var(--accent-color);
            opacity: 0.8;
            transform: translateY(100%);
        }

        .navbar-collapse {
            flex-grow: 0;
            /* Prevents the collapse from growing and pushing elements */
        }

        .auth-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            margin-left: auto;
            /* Pushes auth buttons to the right */
        }

        .auth-btn {
            padding: 0.4rem 1rem;
            border: 1px solid var(--accent-color);
            border-radius: 20px;
            color: var(--text-light);
            background: transparent;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            text-decoration: none;
            white-space: nowrap;
        }

        .auth-btn.login-btn {
            background: transparent;
            border-color: var(--accent-color);
        }

        .auth-btn.register-btn {
            background: var(--accent-color);
            color: var(--primary-color);
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .navbar-toggler {
                order: 1;
            }

            .navbar-brand {
                margin-right: 0;
            }

            .navbar-collapse {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(10, 10, 10, 0.95);
                padding: 1rem;
            }

            .auth-buttons {
                margin: 0.5rem 0;
                justify-content: center;
            }
        }

        /* Enhanced Footer Styles */
        footer {
            background: linear-gradient(to bottom, #0a0a0a, #000000);
            padding: 5rem 0 2rem;
            margin-top: 6rem;
            position: relative;
            border-top: 1px solid rgba(212, 175, 55, 0.1);
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
            opacity: 0.3;
        }

        .footer-content {
            position: relative;
            z-index: 1;
        }

        .footer-title {
            color: var(--accent-color);
            font-size: 1.5rem;
            margin-bottom: 2rem;
            position: relative;
            display: inline-block;
            padding-bottom: 0.5rem;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--accent-color);
            transition: width 0.3s ease;
        }

        .footer-section:hover .footer-title::after {
            width: 100%;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .link-group {
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .link-group a {
            color: var(--text-light);
            text-decoration: none;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
            padding-left: 0;
        }

        .link-group:hover {
            transform: translateX(10px);
        }

        .link-group:hover a {
            color: var(--accent-color);
        }

        .link-group i {
            color: var(--accent-color);
            font-size: 1.2rem;
            transition: all 0.3s ease;
            opacity: 0.7;
        }

        .link-group:hover i {
            opacity: 1;
            transform: scale(1.2);
        }

        /* Enhanced Payment Methods Section */
        .payment-methods {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .payment-icon {
            width: 60px;
            height: 40px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 0.5rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }

        .payment-icon:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-color);
        }

        .payment-icon img {
            width: 100%;
            height: auto;
            transition: all 0.3s ease;
        }

        .payment-icon:hover img {
            filter: brightness(1.2);
        }

        /* Enhanced Social Icons */
        .social-icons {
            display: flex;
            gap: 1.2rem;
            margin-top: 2rem;
            justify-content: flex-start;
        }

        .social-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color);
            transition: all 0.4s ease;
            border: 1px solid rgba(212, 175, 55, 0.1);
            position: relative;
            overflow: hidden;
        }

        .social-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: var(--accent-color);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.4s ease;
            z-index: 0;
        }

        .social-icon i {
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .social-icon:hover::before {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        .social-icon:hover {
            transform: translateY(-5px) rotate(360deg);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2);
        }

        .social-icon:hover i {
            color: var(--primary-color);
        }

        /* Copyright Section */
        .footer-bottom {
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            position: relative;
        }

        .footer-bottom::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
        }

        .footer-bottom p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin: 0;
            transition: all 0.3s ease;
        }

        .footer-bottom:hover p {
            color: var(--text-light);
        }

        /* Newsletter Section (New Addition) */
        .newsletter-form {
            position: relative;
            margin-top: 2rem;
        }

        .newsletter-input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 8px;
            color: var(--text-light);
            transition: all 0.3s ease;
        }

        .newsletter-input:focus {
            outline: none;
            border-color: var(--accent-color);
            background: rgba(255, 255, 255, 0.1);
        }

        .newsletter-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--accent-color);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .newsletter-btn:hover {
            background: #e5c158;
            transform: translateY(-50%) scale(1.05);
        }

        .col-md-4 {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Login Page Styles */
        .login-container {
            margin-top: 130px;
            display: flex;
            width: 900px;
            height: 600px;
            background-color: var(--secondary-color);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
        }

        .image-section {
            flex: 1;
            background-image: url('path/to/your/luxury-watch-image.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 40px;
            position: relative;
        }

        .image-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.7));
        }

        .welcome-text {
            position: relative;
            z-index: 1;
        }

        .welcome-text h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--accent-color);
        }

        .welcome-text p {
            font-size: 1.2rem;
            margin-bottom: 0;
            color: var(--text-color);
        }

        .container-login {
            position: relative;
            width: 1000px;
            min-height: 600px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .background-luxury {
            position: absolute;
            width: 50%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1547996160-81dfa63595aa') center/cover;
            transition: transform 1.5s ease-in-out;
        }

        .background-luxury::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.3));
        }

        .container-login:hover .background-luxury {
            transform: scale(1.1);
        }

        .form-container {
            width: 50%;
            padding: 50px;
            margin-left: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .login-form {
            width: 100%;
        }

        .form-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .form-header h2 {
            color: var(--accent-color);
            font-size: 2.5em;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .form-group {
            position: relative;
            margin-bottom: 30px;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 5px;
            color: var(--text-color);
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            outline: none;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
        }

        .form-label {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .form-control:focus~.form-label,
        .form-control:not(:placeholder-shown)~.form-label {
            top: -10px;
            left: 5px;
            font-size: 12px;
            color: var(--accent-color);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: var(--accent-color);
            border: none;
            border-radius: 5px;
            color: var(--primary-color);
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login:hover {
            background: #e5c158;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }

        .error-message {
            background: rgba(255, 0, 0, 0.1);
            color: var(--error-color);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 0, 0, 0.2);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .role-select {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-color);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 5px;
            padding: 12px;
            width: 100%;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .role-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
        }

        .role-select option {
            background: var(--primary-color);
            color: var(--text-color);
        }

        .luxury-effects {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .luxury-effect {
            position: absolute;
            background: linear-gradient(45deg, transparent, rgba(212, 175, 55, 0.1), transparent);
            border-radius: 50%;
        }

        .effect1 {
            width: 200px;
            height: 200px;
            top: -100px;
            left: -100px;
            animation: float 8s infinite;
        }

        .effect2 {
            width: 300px;
            height: 300px;
            bottom: -150px;
            right: -150px;
            animation: float 12s infinite reverse;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(50px, 50px) rotate(90deg);
            }

            50% {
                transform: translate(0, 100px) rotate(180deg);
            }

            75% {
                transform: translate(-50px, 50px) rotate(270deg);
            }
        }

        @media (max-width: 1024px) {
            .container-login {
                width: 90%;
                margin: 2rem auto;
            }
        }

        @media (max-width: 768px) {
            .container-login {
                flex-direction: column;
                height: auto;
            }

            .background-luxury {
                width: 100%;
                height: 200px;
                position: relative;
            }

            .form-container {
                width: 100%;
                margin-left: 0;
                padding: 30px;
            }
        }

        /* Add smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Add custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--primary-color);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #b38f28;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">EliteWatch</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="auth-buttons">
                    <?php if (isset($_SESSION['username'])): ?>
                        <span class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <a href="../logout.php" class="auth-btn">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="auth-btn login-btn">Login</a>
                        <a href="register.php" class="auth-btn register-btn">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="login-container">
        <div class="container-login">
            <div class="background-luxury"></div>
            <div class="luxury-effects">
                <div class="luxury-effect effect1"></div>
                <div class="luxury-effect effect2"></div>
            </div>
            <div class="form-container">
                <form class="login-form" method="POST" action="">
                    <div class="form-header">
                        <h2>Login Access</h2>
                        <p style="color: var(--text-color);">Enter your credentials</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="error-message">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder=" " required>
                        <label class="form-label">Email Address</label>
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder=" " required>
                        <label class="form-label">Password</label>
                    </div>

                    <button type="submit" class="btn-login">
                        Sign In
                    </button>

                    <div style="text-align: center; margin-top: 20px; color: var(--text-color);">
                        <p>Don't have an account? <a href="register.php" style="color: var(--accent-color); text-decoration: none;">Register</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <div class="footer-links">
                        <div class="link-group">
                            <i class="fas fa-home"></i>
                            <a href="../index.php">Home</a>
                        </div>
                        <div class="link-group">
                            <i class="fas fa-store"></i>
                            <a href="products.php">Products</a>
                        </div>
                        <div class="link-group">
                            <i class="fas fa-shopping-cart"></i>
                            <a href="cart.php">Cart</a>
                        </div>
                        <div class="link-group">
                            <i class="fas fa-info-circle"></i>
                            <a href="about.php">About</a>
                        </div>
                        <div class="link-group">
                            <i class="fas fa-envelope"></i>
                            <a href="contact.php">Contact</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <h5 class="footer-title mb-4">Payment Methods</h5>
                    <div class="payment-methods">
                        <div class="payment-icon">
                            <img src="https://raw.githubusercontent.com/aaronfagan/svg-credit-card-payment-icons/main/flat-rounded/visa.svg"
                                alt="Visa">
                        </div>
                        <div class="payment-icon">
                            <img src="https://raw.githubusercontent.com/aaronfagan/svg-credit-card-payment-icons/main/flat-rounded/mastercard.svg"
                                alt="Mastercard">
                        </div>
                        <div class="payment-icon">
                            <img src="https://raw.githubusercontent.com/aaronfagan/svg-credit-card-payment-icons/main/flat-rounded/paypal.svg"
                                alt="PayPal">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5 class="footer-title">Connect With Us</h5>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p>&copy; <?php echo date("Y"); ?> EliteWatch. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/jquery-3.5.1.slim.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <script>
        // Add smooth animation for form inputs
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>

</body>

</html>