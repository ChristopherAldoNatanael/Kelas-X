<?php 
    session_start();
    require_once "../dbcontroller.php";
    $db = new DB;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Warung Aldo</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #2e294e;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, rgba(46, 41, 78, 0.9), rgba(78, 115, 223, 0.9)), url('../upload/restaurant-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: fadeIn 0.8s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            padding: 30px 20px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .login-logo i {
            font-size: 40px;
            color: var(--primary);
        }
        
        .login-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .login-subtitle {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--secondary);
            font-size: 14px;
        }
        
        .form-control {
            height: 50px;
            padding: 10px 15px 10px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 42px;
            color: #aaa;
            transition: all 0.3s ease;
        }
        
        .form-control:focus + .input-icon {
            color: var(--primary);
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .custom-control-label {
            font-size: 14px;
            color: #666;
        }
        
        .forgot-link {
            font-size: 14px;
            color: var(--primary);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .forgot-link:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        .btn-login {
            display: block;
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(78, 115, 223, 0.6);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            padding: 20px 30px;
            border-top: 1px solid #eee;
            background-color: #f9f9f9;
        }
        
        .login-footer p {
            margin: 0;
            font-size: 13px;
            color: #777;
        }
        
        .error-message {
            background-color: rgba(231, 74, 59, 0.1);
            color: var(--danger);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            border-left: 4px solid var(--danger);
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .success-message {
            background-color: rgba(28, 200, 138, 0.1);
            color: var(--success);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            border-left: 4px solid var(--success);
        }
        
        .admin-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: var(--danger);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 42px;
            color: #aaa;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .password-toggle:hover {
            color: var(--primary);
        }
        
        @media (max-width: 576px) {
            .login-container {
                max-width: 100%;
                border-radius: 10px;
            }
            
            .login-header {
                padding: 20px;
            }
            
            .login-logo {
                width: 60px;
                height: 60px;
            }
            
            .login-logo i {
                font-size: 30px;
            }
            
            .login-title {
                font-size: 20px;
            }
            
            .login-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-utensils"></i>
            </div>
            <h1 class="login-title">Warung Aldo</h1>
            <p class="login-subtitle">Admin Management System</p>
            <div class="admin-badge">Admin Only</div>
        </div>
        
        <div class="login-body">
            <?php 
                if (isset($_POST['login'])) {
                    $email = $_POST['email'];
                    $password = hash('sha256', $_POST['password']);

                    $sql = "SELECT * FROM tbluser WHERE email = '$email' AND PASSWORD='$password'";
                    $count = $db->rowCOUNT($sql);

                    if ($count == 0) {
                        echo '<div class="error-message">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                Email atau password salah. Silakan coba lagi.
                              </div>';
                    } else {
                        $sql = "SELECT * FROM tbluser WHERE email = '$email' AND PASSWORD='$password'";
                        $row = $db->getITEM($sql);

                        $_SESSION['user'] = $row['email'];
                        $_SESSION['level'] = $row['level'];
                        $_SESSION['iduser'] = $row['iduser'];
                        
                        echo '<div class="success-message">
                                <i class="fas fa-check-circle mr-2"></i>
                                Login berhasil! Mengalihkan ke dashboard...
                              </div>';
                        
                        echo '<script>
                                setTimeout(function() {
                                    window.location.href = "index.php";
                                }, 1500);
                              </script>';
                    }
                }
            ?>
            
            <form action="" method="post" id="loginForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required class="form-control" placeholder="Masukkan email Anda">
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required class="form-control" placeholder="Masukkan password Anda">
                    <i class="fas fa-lock input-icon"></i>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>
                
                <div class="form-options">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="rememberMe">
                        <label class="custom-control-label" for="rememberMe">Ingat saya</label>
                    </div>
                    <a href="#" class="forgot-link">Lupa password?</a>
                </div>
                
                <button type="submit" name="login" class="btn-login">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            <p>&copy; 2024 Warung Aldo - All Rights Reserved</p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
            
            // Form validation
            const loginForm = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            
            loginForm.addEventListener('submit', function(e) {
                let isValid = true;
                
                if (!emailInput.value.includes('@')) {
                    isValid = false;
                    emailInput.classList.add('is-invalid');
                } else {
                    emailInput.classList.remove('is-invalid');
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
            
            // Remove validation styling on input
            emailInput.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    </script>
</body>
</html>
