<?php 
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $sql = "SELECT * FROM tbluser WHERE iduser = $id";
        $row = $db->getITEM($sql);
    }
?>

<div class="update-user-container">
            <div class="card-front">
                <div class="card-header ultra-gradient">
                    <div class="animated-bg"></div>
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-edit mr-2"></i>Update Profil User
                    </h3>
                    <div class="header-actions">
                        <button type="button" class="theme-toggle" id="themeToggle">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="user-avatar-section text-center mb-4">
                        <div class="avatar-wrapper">
                            <div class="avatar-circle glow-effect">
                                <div class="avatar-overlay"></div>
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="avatar-status online"></div>
                        </div>
                        <div class="user-info">
                            <h5 class="mt-3 mb-0 user-name"><?php echo $row['user']; ?></h5>
                            <span class="badge badge-pill badge-role">
                                <span class="badge-icon"><i class="fas fa-<?php echo ($row['level'] === 'admin') ? 'crown' : (($row['level'] === 'koki') ? 'utensils' : 'cash-register'); ?>"></i></span>
                                <?php echo ucfirst($row['level']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <form action="" method="post" class="user-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group material-input">
                                    <input type="text" name="user" id="user" required value="<?php echo $row['user']?>" class="form-control custom-input">
                                    <label for="user">
                                        <i class="fas fa-user text-primary mr-2"></i>Nama User
                                    </label>
                                    <span class="focus-border">
                                        <i></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group material-input">
                                    <input type="email" name="email" id="email" required value="<?php echo $row['email']?>" class="form-control custom-input">
                                    <label for="email">
                                        <i class="fas fa-envelope text-primary mr-2"></i>Email
                                    </label>
                                    <span class="focus-border">
                                        <i></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group material-input">
                                    <div class="password-field">
                                        <input type="password" name="password" id="password" required value="<?php echo $row['password']?>" class="form-control custom-input">
                                        <button type="button" class="password-toggle" data-target="password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <label for="password">
                                        <i class="fas fa-lock text-primary mr-2"></i>Password
                                    </label>
                                    <span class="focus-border">
                                        <i></i>
                                    </span>
                                    <div class="password-strength-meter">
                                        <div class="strength-bar"></div>
                                        <span class="strength-text"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group material-input">
                                    <div class="password-field">
                                        <input type="password" name="konfirmasi" id="konfirmasi" required value="<?php echo $row['password']?>" class="form-control custom-input">
                                        <button type="button" class="password-toggle" data-target="konfirmasi">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <label for="konfirmasi">
                                        <i class="fas fa-check-circle text-primary mr-2"></i>Konfirmasi Password
                                    </label>
                                    <span class="focus-border">
                                        <i></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="level" class="select-label">
                                <i class="fas fa-user-tag text-primary mr-2"></i>Level
                            </label>
                            <div class="custom-select-wrapper">
                                <select name="level" id="level" class="form-control custom-select">
                                    <option value="admin" <?php if ($row['level']==="admin") echo "selected" ?>>Admin</option>
                                    <option value="koki" <?php if ($row['level']==="koki") echo "selected" ?>>Koki</option>
                                    <option value="kasir" <?php if ($row['level']==="kasir") echo "selected" ?>>Kasir</option>
                                </select>
                                <div class="select-icon">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions mt-4">
                            <button type="submit" name="simpan" class="btn btn-primary btn-save">
                                <span class="btn-content">
                                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                                </span>
                                <span class="btn-glowing-effect"></span>
                            </button>
                            <a href="?f=user&m=select" class="btn btn-secondary btn-cancel">
                                <i class="fas fa-times mr-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <div class="last-updated">
                        <i class="fas fa-clock text-muted mr-2"></i>
                        <span>Terakhir diperbarui: <?php echo date('d M Y H:i'); ?></span>
                    </div>
                    <div class="footer-actions">
                        <button type="button" class="action-btn" title="Lihat Riwayat">
                            <i class="fas fa-history"></i>
                        </button>
                        <button type="button" class="action-btn" title="Bantuan">
                            <i class="fas fa-question-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    if (isset($_POST['simpan'])) {
        $user = $_POST['user'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $konfirmasi = $_POST['konfirmasi'];
        $level = $_POST['level'];

        if ($password === $konfirmasi) {
            $sql = "UPDATE tbluser SET user='$user',email='$email',password='$password',level='$level' WHERE iduser=$id ";
            $db->runSQL($sql);
            echo '<div class="notification-toast success show">
                    <div class="toast-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="toast-content">
                        <h4>Sukses!</h4>
                        <p>Data berhasil diperbarui.</p>
                    </div>
                    <div class="toast-progress">
                        <div class="progress-bar"></div>
                    </div>
                  </div>';
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "?f=user&m=select";
                    }, 3000);
                  </script>';
        } else {
            echo '<div class="notification-toast error show">
                    <div class="toast-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="toast-content">
                        <h4>Error!</h4>
                        <p>Password tidak sama dengan konfirmasi.</p>
                    </div>
                    <div class="toast-progress">
                        <div class="progress-bar"></div>
                    </div>
                  </div>';
        }
    }
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --primary-light: #818cf8;
        --secondary: #2e294e;
        --success: #10b981;
        --info: #06b6d4;
        --warning: #f59e0b;
        --danger: #ef4444;
        --light: #f8fafc;
        --dark: #1e293b;
        --gradient-1: #6366f1;
        --gradient-2: #8b5cf6;
        --gradient-3: #d946ef;
        --transition-speed: 0.3s;
        --border-radius: 16px;
        --card-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }
    
    .dark-theme {
        --primary: #818cf8;
        --primary-dark: #6366f1;
        --primary-light: #a5b4fc;
        --secondary: #475569;
        --light: #1e293b;
        --dark: #f8fafc;
        --card-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }
    
    /* Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f0f4f8;
        color: var(--dark);
        transition: background-color var(--transition-speed), color var(--transition-speed);
    }
    
    .dark-theme body {
        background-color: #0f172a;
    }
    
    .update-user-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
        perspective: 1000px;
    }
    
    /* 3D Card Effect */
    .card-3d-wrapper {
        width: 100%;
        height: 100%;
        position: relative;
        transform-style: preserve-3d;
        transition: all 0.8s ease;
    }
    
    .card-3d {
        position: relative;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
        transition: all 0.8s ease;
        transform: rotateX(5deg) rotateY(5deg);
        will-change: transform;
    }
    
    .update-user-container:hover .card-3d {
        transform: rotateX(0deg) rotateY(0deg);
    }
    
    .card-front {
        width: 100%;
        position: relative;
        background-color: white;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        backface-visibility: hidden;
        transform-style: preserve-3d;
        transition: all var(--transition-speed);
    }
    
    .dark-theme .card-front {
        background-color: #1e293b;
    }
    
    /* Ultra Gradient Header */
    .ultra-gradient {
        background: linear-gradient(135deg, var(--gradient-1), var(--gradient-2), var(--gradient-3));
        background-size: 200% 200%;
        animation: gradientAnimation 15s ease infinite;
        color: white;
        padding: 30px;
        position: relative;
        overflow: hidden;
    }
    
    .animated-bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB2aWV3Qm94PSIwIDAgMTI4MCAxNDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGcgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjEpIj48cGF0aCBkPSJNMTI4MCAwTDY0MCA3MCAwIDB2MTQwbDY0MC03MCAxMjgwIDcwVjB6Ii8+PC9nPjwvc3ZnPg==');
        background-size: 100% 100%;
        animation: waveAnimation 10s linear infinite;
        opacity: 0.3;
    }
    
    .card-title {
        font-weight: 700;
        letter-spacing: 1px;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        font-size: 1.5rem;
    }
    
    .header-actions {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 2;
    }
    
    .theme-toggle {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all var(--transition-speed);
        backdrop-filter: blur(5px);
    }
    
    .theme-toggle:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-3px);
    }
    
    /* Avatar Section */
    .user-avatar-section {
        padding: 40px 0 30px;
        position: relative;
    }
    
    .avatar-wrapper {
        position: relative;
        display: inline-block;
    }
    
    .avatar-circle {
        width: 140px;
        height: 140px;
        background: linear-gradient(135deg, #f0f4ff, #e4eaff);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 4rem;
        color: var(--primary);
        box-shadow: 0 10px 25px rgba(99,102,241,0.3);
        border: 6px solid white;
        transition: all var(--transition-speed);
        position: relative;
        overflow: hidden;
    }
    
    .dark-theme .avatar-circle {
        background: linear-gradient(135deg, #2d3748, #1a202c);
        border-color: #2d3748;
    }
    
    .avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(139,92,246,0.2));
        opacity: 0;
        transition: opacity var(--transition-speed);
    }
    
    .avatar-circle:hover .avatar-overlay {
        opacity: 1;
    }
    
    .glow-effect {
        position: relative;
    }
    
    .glow-effect::after {
        content: '';
        position: absolute;
        top: -20px;
        left: -20px;
        right: -20px;
        bottom: -20px;
        background: radial-gradient(circle, rgba(99,102,241,0.4) 0%, rgba(99,102,241,0) 70%);
        z-index: -1;
        opacity: 0;
        transition: opacity var(--transition-speed);
    }
    
    .avatar-wrapper:hover .glow-effect::after {
        opacity: 1;
    }
    
    .avatar-status {
        position: absolute;
        bottom: 10px;
        right: 10px;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        border: 4px solid white;
        z-index: 2;
    }
    
    .dark-theme .avatar-status {
        border-color: #2d3748;
    }
    
    .avatar-status.online {
        background-color: var(--success);
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.6);
    }
    
    .user-info {
        margin-top: 20px;
    }
    
    .user-name {
        font-weight: 700;
        font-size: 1.5rem;
        margin-top: 15px;
        color: var(--dark);
        letter-spacing: 0.5px;
        position: relative;
        display: inline-block;
    }
    
    .dark-theme .user-name {
        color: var(--light);
    }
    
    .user-name::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: linear-gradient(to right, var(--gradient-1), var(--gradient-3));
        transition: width var(--transition-speed);
    }
    
    .user-name:hover::after {
        width: 100%;
    }
    
    .badge-role {
        background: linear-gradient(135deg, var(--gradient-1), var(--gradient-2));
        color: white;
        padding: 8px 20px;
        font-size: 0.9rem;
        margin-top: 15px;
        border-radius: 30px;
        font-weight: 500;
        box-shadow: 0 8px 15px rgba(99,102,241,0.3);
        transition: all var(--transition-speed);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .badge-role:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 12px 20px rgba(99,102,241,0.4);
    }
    
    .badge-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
    }
    
    /* Form Styles */
    .user-form {
        padding: 20px 40px 40px;
    }
    
    .form-group {
        margin-bottom: 30px;
        position: relative;
    }
    
    /* Material Input */
    .material-input {
        position: relative;
        margin-top: 20px;
    }
    
    .material-input label {
        position: absolute;
        top: 12px;
        left: 15px;
        font-size: 0.9rem;
        color: #6c757d;
        transition: all 0.25s ease;
        pointer-events: none;
        z-index: 1;
    }
    
    .material-input input:focus ~ label,
    .material-input input:valid ~ label {
        top: -12px;
        left: 10px;
        font-size: 0.75rem;
        background: linear-gradient(135deg, var(--gradient-1), var(--gradient-2));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        padding: 0 5px;
        font-weight: 600;
    }
    
    .dark-theme .material-input input:focus ~ label,
    .dark-theme .material-input input:valid ~ label {
        background: white;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .focus-border {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(to right, var(--gradient-1), var(--gradient-3));
        transition: all 0.4s ease;
    }
    
    .material-input input:focus ~ .focus-border,
    .material-input input:valid ~ .focus-border {
        width: 100%;
    }
    
    .custom-input {
        border-radius: 12px;
        padding: 15px;
        border: 2px solid #e0e0e0;
        transition: all var(--transition-speed);
        font-size: 0.95rem;
        background-color: #f9fafc;
        width: 100%;
        color: var(--dark);
    }
    
    .dark-theme .custom-input {
        background-color: #2d3748;
        border-color: #4a5568;
        color: var(--light);
    }
    
    .custom-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        background-color: white;
        outline: none;
    }
    
    .dark-theme .custom-input:focus {
        background-color: #1e293b;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.15);
    }
    
    /* Password Field */
    .password-field {
        position: relative;
    }
    
    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        transition: color var(--transition-speed);
        z-index: 2;
    }
    
    .password-toggle:hover {
        color: var(--primary);
    }
    
    .password-strength-meter {
        height: 5px;
        background-color: #e0e0e0;
        border-radius: 3px;
        margin-top: 10px;
        overflow: hidden;
        display: none;
    }
    
    .dark-theme .password-strength-meter {
        background-color: #4a5568;
    }
    
    .strength-bar {
        height: 100%;
        width: 0;
        transition: width 0.3s, background-color 0.3s;
    }
    
    .strength-text {
        font-size: 0.75rem;
        margin-top: 5px;
        display: block;
    }
    
    /* Custom Select */
    .custom-select-wrapper {
        position: relative;
    }
    
    .custom-select {
        border-radius: 12px;
        padding: 15px;
        border: 2px solid #e0e0e0;
        transition: all var(--transition-speed);
        font-size: 0.95rem;
        background-color: #f9fafc;
        width: 100%;
        appearance: none;
        cursor: pointer;
        color: var(--dark);
    }
    
    .dark-theme .custom-select {
        background-color: #2d3748;
        border-color: #4a5568;
        color: var(--light);
    }
    
    .custom-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        background-color: white;
        outline: none;
    }
    
    .dark-theme .custom-select:focus {
        background-color: #1e293b;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.15);
    }
    
    .select-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        pointer-events: none;
        transition: transform var(--transition-speed);
    }
    
    .custom-select:focus + .select-icon {
        transform: translateY(-50%) rotate(180deg);
        color: var(--primary);
    }
    
    .select-label {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 10px;
        font-size: 0.9rem;
        display: block;
    }
    
    .dark-theme .select-label {
        color: var(--light);
    }
    
    /* Buttons */
    .form-actions {
        display: flex;
        gap: 15px;
    }
    
    .btn {
        border: none;
        border-radius: 12px;
        padding: 16px 32px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all var(--transition-speed);
        position: relative;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    
    .btn-save {
        background: linear-gradient(135deg, var(--gradient-1), var(--gradient-2));
        color: white;
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        position: relative;
        z-index: 1;
    }
    
    .btn-save:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 25px rgba(99, 102, 241, 0.4);
    }
    
    .btn-content {
        position: relative;
        z-index: 2;
    }
    
    .btn-glowing-effect {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(45deg);
        animation: glowingEffect 3s infinite;
    }
    
    .btn-cancel {
        background-color: #f0f0f0;
        color: var(--dark);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .dark-theme .btn-cancel {
        background-color: #4a5568;
        color: var(--light);
    }
    
    .btn-cancel:hover {
        background-color: #e0e0e0;
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    .dark-theme .btn-cancel:hover {
        background-color: #2d3748;
    }
    
    /* Card Footer */
    .card-footer {
        background-color: #f8f9fc;
        border-top: 1px solid #e3e6f0;
        padding: 20px 40px;
        color: #6c757d;
        font-size: 0.85rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .dark-theme .card-footer {
        background-color: #2d3748;
        border-color: #4a5568;
        color: #a0aec0;
    }
    
    .footer-actions {
        display: flex;
        gap: 10px;
    }
    
    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: white;
        border: 1px solid #e3e6f0;
        color: #6c757d;
        cursor: pointer;
        transition: all var(--transition-speed);
    }
    
    .dark-theme .action-btn {
        background-color: #4a5568;
        border-color: #2d3748;
        color: #a0aec0;
    }
    
    .action-btn:hover {
        background-color: var(--primary);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
    }
    
    /* Toast Notifications */
    .notification-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        width: 350px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        display: flex;
        overflow: hidden;
        transform: translateX(400px);
        opacity: 0;
        transition: all 0.5s ease;
        z-index: 9999;
    }
    
    .notification-toast.show {
        transform: translateX(0);
        opacity: 1;
    }
    
    .toast-icon {
        width: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .notification-toast.success .toast-icon {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .notification-toast.error .toast-icon {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    
    .toast-content {
        flex: 1;
        padding: 15px 10px;
    }
    
    .toast-content h4 {
        margin: 0 0 5px;
        font-weight: 600;
        font-size: 1rem;
    }
    
    .toast-content p {
        margin: 0;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: rgba(0,0,0,0.1);
    }
    
    .toast-progress .progress-bar {
        height: 100%;
        width: 100%;
        background: linear-gradient(to right, var(--gradient-1), var(--gradient-3));
        animation: progressAnimation 3s linear forwards;
    }
    
    .dark-theme .notification-toast {
        background: #2d3748;
    }
    
    .dark-theme .toast-content p {
        color: #a0aec0;
    }
    
    /* Animations */
    @keyframes gradientAnimation {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }
    
    @keyframes waveAnimation {
        0% {
            transform: translateX(0) translateZ(0) scaleY(1);
        }
        50% {
            transform: translateX(-25%) translateZ(0) scaleY(0.8);
        }
        100% {
            transform: translateX(-50%) translateZ(0) scaleY(1);
        }
    }
    
    @keyframes glowingEffect {
        0% {
            transform: rotate(0deg);
            opacity: 0.3;
        }
        50% {
            transform: rotate(180deg);
            opacity: 0.1;
        }
        100% {
            transform: rotate(360deg);
            opacity: 0.3;
        }
    }
    
    @keyframes progressAnimation {
        0% {
            width: 100%;
        }
        100% {
            width: 0%;
        }
    }
    
    @keyframes floatingAnimation {
        0% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
        100% {
            transform: translateY(0px);
        }
    }
    
    /* Responsive Styles */
    @media (max-width: 768px) {
        .update-user-container {
            padding: 10px;
            margin: 20px auto;
        }
        
        .user-form {
            padding: 15px 20px 30px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .card-footer {
            padding: 15px 20px;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }
        
        .notification-toast {
            width: calc(100% - 40px);
        }
    }
    
    /* Floating Elements Animation */
    .floating {
        animation: floatingAnimation 3s ease-in-out infinite;
    }
    
    /* Glassmorphism Effect */
    .glass-effect {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .dark-theme .glass-effect {
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const toggleButtons = document.querySelectorAll('.password-toggle');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
        
        // Password strength meter
        const passwordInput = document.getElementById('password');
        const strengthBar = document.querySelector('.strength-bar');
        const strengthText = document.querySelector('.strength-text');
        const strengthMeter = document.querySelector('.password-strength-meter');
        
        if (passwordInput) {
            passwordInput.addEventListener('focus', function() {
                strengthMeter.style.display = 'block';
            });
            
            passwordInput.addEventListener('input', function() {
                const value = passwordInput.value;
                let strength = 0;
                
                if (value.length >= 8) strength += 20;
                if (value.match(/[a-z]+/)) strength += 20;
                if (value.match(/[A-Z]+/)) strength += 20;
                if (value.match(/[0-9]+/)) strength += 20;
                if (value.match(/[^a-zA-Z0-9]+/)) strength += 20;
                
                strengthBar.style.width = strength + '%';
                
                if (strength < 40) {
                    strengthBar.style.backgroundColor = '#ef4444';
                    strengthText.textContent = 'Lemah';
                    strengthText.style.color = '#ef4444';
                } else if (strength < 70) {
                    strengthBar.style.backgroundColor = '#f59e0b';
                    strengthText.textContent = 'Sedang';
                    strengthText.style.color = '#f59e0b';
                } else {
                    strengthBar.style.backgroundColor = '#10b981';
                    strengthText.textContent = 'Kuat';
                    strengthText.style.color = '#10b981';
                }
            });
        }
        
        // Theme toggle
        const themeToggle = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;
        
        themeToggle.addEventListener('click', function() {
            htmlElement.classList.toggle('dark-theme');
            
            const icon = this.querySelector('i');
            if (htmlElement.classList.contains('dark-theme')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });
        
        // 3D Card Effect
        const card = document.querySelector('.card-3d');
        const container = document.querySelector('.update-user-container');
        
        container.addEventListener('mousemove', function(e) {
            const xAxis = (window.innerWidth / 2 - e.pageX) / 25;
            const yAxis = (window.innerHeight / 2 - e.pageY) / 25;
            card.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
        });
        
        container.addEventListener('mouseenter', function() {
            card.style.transition = 'none';
        });
        
        container.addEventListener('mouseleave', function() {
            card.style.transition = 'all 0.5s ease';
            card.style.transform = 'rotateY(0deg) rotateX(0deg)';
        });
        
        // Auto-hide toast notifications
        const toasts = document.querySelectorAll('.notification-toast');
        
        toasts.forEach(toast => {
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        });
        
        // Form validation with visual feedback
        const form = document.querySelector('.user-form');
        const konfirmasiInput = document.getElementById('konfirmasi');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                if (passwordInput.value !== konfirmasiInput.value) {
                    e.preventDefault();
                    
                    // Shake animation for password fields
                    passwordInput.parentElement.classList.add('shake');
                    konfirmasiInput.parentElement.classList.add('shake');
                    
                    setTimeout(() => {
                        passwordInput.parentElement.classList.remove('shake');
                        konfirmasiInput.parentElement.classList.remove('shake');
                    }, 500);
                }
            });
        }
    });
    
    // Add shake animation
    document.head.insertAdjacentHTML('beforeend', `
        <style>
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }
            
            .shake {
                animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
            }
        </style>
    `);
</script>