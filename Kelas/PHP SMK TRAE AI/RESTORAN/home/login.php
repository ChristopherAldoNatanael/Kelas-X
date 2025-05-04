<div class="login-container">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card login-card">
                <div class="card-header login-card-header">
                    <h3 class="text-center mb-0">
                        <i class="fas fa-user-circle mr-2"></i> LOGIN PELANGGAN
                    </h3>
                </div>
                <div class="card-body">
                    <div class="login-icon">
                        <i class="fas fa-utensils"></i>
                        <h4>WARUNG ALDO</h4>
                    </div>
                    
                    <?php 
                    if (isset($_POST['login'])) {
                        $email = $_POST['email'];
                        $password = $_POST['password'];

                        $sql = "SELECT * FROM tblpelanggan WHERE email = '$email' AND PASSWORD='$password' AND aktif=1";
                        $count = $db->rowCOUNT($sql);

                        if ($count == 0) {
                            echo '<div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-circle mr-2"></i> Email atau password salah. Silakan coba lagi.
                                  </div>';
                        } else {
                            $sql = "SELECT * FROM tblpelanggan WHERE email = '$email' AND PASSWORD='$password' AND aktif=1";
                            $row = $db->getITEM($sql);

                            $_SESSION['pelanggan'] = $row['email'];
                            $_SESSION['idpelanggan'] = $row['idpelanggan'];
                            
                            echo '<div class="alert alert-success" role="alert">
                                    <i class="fas fa-check-circle mr-2"></i> Login berhasil! Mengalihkan...
                                  </div>';
                            echo '<script>
                                    setTimeout(function() {
                                        window.location.href = "index.php";
                                    }, 1500);
                                  </script>';
                        }
                    }
                    ?>
                    
                    <form action="" method="post" class="login-form">
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope mr-1"></i> Email
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" name="email" id="email" required class="form-control" placeholder="Masukkan email Anda">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock mr-1"></i> Password
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" name="password" id="password" required class="form-control" placeholder="Masukkan password Anda">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" title="Tampilkan password">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group remember-me">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="rememberMe">
                                <label class="custom-control-label" for="rememberMe">Ingat saya</label>
                            </div>
                            <a href="#" class="forgot-password">Lupa password?</a>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="login" class="btn btn-primary btn-block login-btn">
                                <i class="fas fa-sign-in-alt mr-2"></i> Login
                            </button>
                        </div>
                    </form>
                    
                    <div class="register-link text-center">
                        <p>Belum punya akun? <a href="?f=home&m=daftar">Daftar sekarang</a></p>
                    </div>
                </div>
                <div class="card-footer login-card-footer text-center">
                    <div class="social-login">
                        <p class="mb-2">Atau login dengan</p>
                        <div class="social-buttons">
                            <button class="btn btn-outline-secondary social-btn" disabled>
                                <i class="fab fa-google"></i>
                            </button>
                            <button class="btn btn-outline-secondary social-btn" disabled>
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button class="btn btn-outline-secondary social-btn" disabled>
                                <i class="fab fa-twitter"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .login-container {
        padding: 40px 15px;
    }
    
    .login-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        background-color: white;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .login-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .login-card-header {
        background-color: var(--secondary);
        color: white;
        border-radius: 10px 10px 0 0;
        padding: 20px;
    }
    
    .login-card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
        padding: 20px;
    }
    
    .login-icon {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .login-icon i {
        font-size: 3rem;
        color: var(--primary);
        margin-bottom: 10px;
    }
    
    .login-icon h4 {
        color: var(--secondary);
        font-weight: 700;
    }
    
    .form-label {
        font-weight: 500;
        color: var(--secondary);
        margin-bottom: 8px;
    }
    
    .form-control {
        border-radius: 0 6px 6px 0;
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    }
    
    .input-group-text {
        background-color: var(--secondary);
        color: white;
        border: none;
        border-radius: 6px 0 0 6px;
    }
    
    .toggle-password {
        cursor: pointer;
        background-color: #f0f0f0;
        color: #555;
    }
    
    .remember-me {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 20px 0;
    }
    
    .forgot-password {
        color: var(--primary);
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .forgot-password:hover {
        color: var(--secondary);
        text-decoration: underline;
    }
    
    .login-btn {
        background-color: var(--primary);
        border-color: var(--primary);
        padding: 12px;
        font-weight: 500;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        border-radius: 6px;
    }
    
    .login-btn:hover {
        background-color: #e55a2a;
        border-color: #e55a2a;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
    }
    
    .register-link {
        margin-top: 20px;
    }
    
    .register-link a {
        color: var(--primary);
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .register-link a:hover {
        color: var(--secondary);
        text-decoration: underline;
    }
    
    .social-login {
        margin-top: 10px;
    }
    
    .social-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    
    .social-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .social-btn:hover:not([disabled]) {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .alert {
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
    
    @media (max-width: 768px) {
        .login-container {
            padding: 20px 10px;
        }
        
        .remember-me {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .forgot-password {
            margin-top: 10px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.querySelector('.toggle-password');
        const passwordInput = document.querySelector('#password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle eye icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });
</script>
