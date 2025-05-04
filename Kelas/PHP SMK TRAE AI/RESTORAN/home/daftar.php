<div class="registration-container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card registration-card">
                <div class="card-header registration-card-header">
                    <h3 class="text-center mb-0">
                        <i class="fas fa-user-plus mr-2"></i> Registrasi Pelanggan
                    </h3>
                </div>
                <div class="card-body">
                    <div class="registration-icon">
                        <i class="fas fa-utensils"></i>
                        <h4>WARUNG ALDO</h4>
                        <p class="registration-subtitle">Daftar untuk menikmati pengalaman kuliner terbaik</p>
                    </div>
                    
                    <?php 
                    if (isset($_POST['simpan'])) {
                        $pelanggan = $_POST['pelanggan'];
                        $alamat = $_POST['alamat'];
                        $telp = $_POST['telp'];
                        $email = $_POST['email'];
                        $password = $_POST['password'];
                        $konfirmasi = $_POST['konfirmasi'];

                        if ($password === $konfirmasi) {
                            $sql = "INSERT INTO tblpelanggan VALUES ('','$pelanggan','$alamat','$telp','$email','$password',1)";
                            $db->runSQL($sql);
                            
                            echo '<div class="alert alert-success" role="alert">
                                    <i class="fas fa-check-circle mr-2"></i> Registrasi berhasil! Mengalihkan ke halaman informasi...
                                  </div>';
                            echo '<script>
                                    setTimeout(function() {
                                        window.location.href = "?f=home&m=info";
                                    }, 2000);
                                  </script>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-circle mr-2"></i> Password tidak sama dengan konfirmasi. Silakan coba lagi.
                                  </div>';
                        }
                    }
                    ?>
                    
                    <form action="" method="post" class="registration-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pelanggan" class="form-label">
                                        <i class="fas fa-user mr-1"></i> Nama Lengkap
                                    </label>
                                    <input type="text" name="pelanggan" id="pelanggan" required placeholder="Masukkan nama lengkap Anda" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label for="alamat" class="form-label">
                                        <i class="fas fa-map-marker-alt mr-1"></i> Alamat
                                    </label>
                                    <textarea name="alamat" id="alamat" required placeholder="Masukkan alamat lengkap Anda" class="form-control" rows="3"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="telp" class="form-label">
                                        <i class="fas fa-phone mr-1"></i> Nomor Telepon
                                    </label>
                                    <input type="text" name="telp" id="telp" required placeholder="Masukkan nomor telepon Anda" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope mr-1"></i> Email
                                    </label>
                                    <input type="email" name="email" id="email" required placeholder="Masukkan email Anda" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock mr-1"></i> Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" required placeholder="Masukkan password Anda" class="form-control">
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" title="Tampilkan password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Password minimal 6 karakter</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="konfirmasi" class="form-label">
                                        <i class="fas fa-lock mr-1"></i> Konfirmasi Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="konfirmasi" id="konfirmasi" required placeholder="Masukkan ulang password Anda" class="form-control">
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-konfirmasi" title="Tampilkan password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group terms-checkbox mt-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="terms" required>
                                <label class="custom-control-label" for="terms">
                                    Saya menyetujui <a href="#">Syarat dan Ketentuan</a> serta <a href="#">Kebijakan Privasi</a>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="simpan" class="btn btn-primary btn-block register-btn">
                                <i class="fas fa-user-plus mr-2"></i> Daftar Sekarang
                            </button>
                        </div>
                    </form>
                    
                    <div class="login-link text-center mt-4">
                        <p>Sudah punya akun? <a href="?f=home&m=login">Login di sini</a></p>
                    </div>
                </div>
                <div class="card-footer registration-card-footer text-center">
                    <div class="social-registration">
                        <p class="mb-2">Atau daftar dengan</p>
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
    .registration-container {
        padding: 40px 15px;
    }
    
    .registration-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        background-color: white;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .registration-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .registration-card-header {
        background-color: var(--secondary);
        color: white;
        border-radius: 10px 10px 0 0;
        padding: 20px;
    }
    
    .registration-card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
        padding: 20px;
    }
    
    .registration-icon {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .registration-icon i {
        font-size: 3rem;
        color: var(--primary);
        margin-bottom: 10px;
    }
    
    .registration-icon h4 {
        color: var(--secondary);
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .registration-subtitle {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .form-label {
        font-weight: 500;
        color: var(--secondary);
        margin-bottom: 8px;
    }
    
    .form-control {
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    }
    
    .input-group-text {
        background-color: #f0f0f0;
        color: #555;
        border: 1px solid #e0e0e0;
        border-radius: 0 6px 6px 0;
        cursor: pointer;
    }
    
    .toggle-password, .toggle-konfirmasi {
        cursor: pointer;
    }
    
    .terms-checkbox a {
        color: var(--primary);
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .terms-checkbox a:hover {
        color: var(--secondary);
        text-decoration: underline;
    }
    
    .form-actions {
        margin-top: 30px;
    }
    
    .register-btn {
        background-color: var(--primary);
        border-color: var(--primary);
        padding: 12px;
        font-weight: 500;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        border-radius: 6px;
    }
    
    .register-btn:hover {
        background-color: #e55a2a;
        border-color: #e55a2a;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
    }
    
    .login-link a {
        color: var(--primary);
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .login-link a:hover {
        color: var(--secondary);
        text-decoration: underline;
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
        .registration-container {
            padding: 20px 10px;
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
        
        // Toggle confirmation password visibility
        const toggleKonfirmasi = document.querySelector('.toggle-konfirmasi');
        const konfirmasiInput = document.querySelector('#konfirmasi');
        
        toggleKonfirmasi.addEventListener('click', function() {
            const type = konfirmasiInput.getAttribute('type') === 'password' ? 'text' : 'password';
            konfirmasiInput.setAttribute('type', type);
            
            // Toggle eye icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
        
        // Check password match
        const password = document.querySelector('#password');
        const konfirmasi = document.querySelector('#konfirmasi');
        
        function checkPasswordMatch() {
            if (password.value !== '' && konfirmasi.value !== '') {
                if (password.value !== konfirmasi.value) {
                    konfirmasi.setCustomValidity('Password tidak sama');
                } else {
                    konfirmasi.setCustomValidity('');
                }
            }
        }
        
        password.addEventListener('change', checkPasswordMatch);
        konfirmasi.addEventListener('keyup', checkPasswordMatch);
    });
</script>
