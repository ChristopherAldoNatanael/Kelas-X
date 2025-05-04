<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 class="success-title">Registrasi Berhasil!</h2>
        <p class="success-message">Akun Anda telah berhasil dibuat. Silahkan login untuk melanjutkan.</p>
        <div class="success-action">
            <a class="btn btn-primary login-btn" href="?f=home&m=login" role="button">
                <i class="fas fa-sign-in-alt mr-2"></i>Login Sekarang
            </a>
        </div>
    </div>
</div>

<style>
    .success-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 20px;
    }
    
    .success-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 40px;
        text-align: center;
        max-width: 500px;
        width: 100%;
        animation: fadeIn 0.8s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .success-icon {
        font-size: 80px;
        color: #28a745;
        margin-bottom: 20px;
        animation: scaleIn 0.5s ease-out 0.3s both;
    }
    
    @keyframes scaleIn {
        from { transform: scale(0); }
        to { transform: scale(1); }
    }
    
    .success-title {
        color: #2E294E;
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 28px;
    }
    
    .success-message {
        color: #6c757d;
        font-size: 18px;
        margin-bottom: 30px;
    }
    
    .success-action {
        margin-top: 10px;
    }
    
    .login-btn {
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 50px;
        background: linear-gradient(135deg, #4A90E2, #2E294E);
        border: none;
        box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
        transition: all 0.3s ease;
    }
    
    .login-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(74, 144, 226, 0.4);
        background: linear-gradient(135deg, #5A9FF2, #3E395E);
    }
    
    .login-btn:active {
        transform: translateY(1px);
    }
</style>