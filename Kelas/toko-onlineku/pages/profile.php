<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['pelanggan_id'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database melalui config
require_once '../includes/config.php';

// Ambil data user
$pelanggan_id = $_SESSION['pelanggan_id'];
$query = "SELECT * FROM pelanggan WHERE pelanggan_id = :id";
$stmt = $koneksi->prepare($query);
$stmt->bindParam(':id', $pelanggan_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Proses update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    try {
        $update_query = "UPDATE pelanggan SET username = :username, email = :email, password = :password WHERE pelanggan_id = :id";
        $stmt = $koneksi->prepare($update_query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':id', $pelanggan_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $success = "Profile berhasil diperbarui!";
            // Refresh data user
            $user['username'] = $username;
            $user['email'] = $email;
        } else {
            $error = "Gagal memperbarui profile!";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #2ecc71;
            --error-color: #e74c3c;
            --border-radius: 12px;
            --shadow-sm: 0 4px 6px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #8BC6EC 0%, #9599E2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .profile-container {
            width: 100%;
            max-width: 1000px;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            animation: fadeIn 0.8s ease-out;
        }

        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }

        .profile-sidebar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            height: fit-content;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 40px 20px;
            text-align: center;
            position: relative;
        }

        .profile-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            background: inherit;
            filter: blur(20px);
            opacity: 0.7;
            z-index: -1;
        }

        .avatar-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
        }

        .avatar-circle {
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: white;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .avatar-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .avatar-overlay {
            position: absolute;
            bottom: -50px;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            text-align: center;
            padding: 5px;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .avatar-container:hover .avatar-overlay {
            bottom: 0;
        }

        .username {
            color: white;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .role {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-top: 5px;
        }

        .user-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 20px;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.8);
            padding: 15px;
            border-radius: var(--border-radius);
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }

        .profile-main {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .profile-header-main {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            padding: 20px 30px;
            color: white;
        }

        .profile-header-main h2 {
            font-size: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-content {
            padding: 30px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            animation: slideIn 0.5s ease;
        }

        .alert i {
            font-size: 24px;
            margin-right: 15px;
        }

        .success {
            background: rgba(46, 204, 113, 0.15);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .error {
            background: rgba(231, 76, 60, 0.15);
            color: var(--error-color);
            border-left: 4px solid var(--error-color);
        }

        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 25px;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .profile-form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        @media (max-width: 600px) {
            .profile-form {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            position: relative;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #555;
            transition: all 0.3s ease;
        }

        .input-container {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            transition: all 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #eee;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        .form-control:focus+.input-icon {
            color: var(--primary-color);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .action-buttons {
            grid-column: 1 / -1;
            display: flex !important;
            gap: 15px;
            margin-top: 20px;
            visibility: visible !important;
        }

        .btn {
            padding: 15px 25px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex !important;
            align-items: center;
            justify-content: center;
            gap: 10px;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
            flex: 1;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        }

        .btn-primary:active {
            transform: translateY(1px);
        }

        .btn-secondary {
            background: white;
            color: #666;
            border: 2px solid #eee;
            flex: 1;
        }

        .btn-secondary:hover {
            background: #f8f9fa;
            color: var(--primary-color);
            border-color: #ddd;
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animasi untuk elemen */
        .animate-slide-up {
            animation: slideUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animasi pulse untuk hint */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="profile-container">
        <!-- Profile Sidebar -->
        <div class="profile-sidebar">
            <div class="profile-header">
                <div class="avatar-container">
                    <div class="avatar-circle pulse">
                        <i class="fas fa-user"></i>
                    </div>
                    <!-- <div class="avatar-overlay">Update Photo</div> -->
                </div>
                <h1 class="username"><?php echo htmlspecialchars($user['username']); ?></h1>
                <span class="role"><?php echo htmlspecialchars($user['role'] ?? 'Member'); ?></span>
            </div>

            <div class="user-stats">
                <div class="stat-item">
                    <div class="stat-value">14</div>
                    <div class="stat-label">Orders</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">3</div>
                    <div class="stat-label">Reviews</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">5⭐</div>
                    <div class="stat-label">Rating</div>
                </div>
            </div>
        </div>

        <!-- Profile Main Content -->
        <div class="profile-main">
            <div class="profile-header-main">
                <h2><i class="fas fa-user-edit"></i> Edit Your Profile</h2>
            </div>

            <div class="profile-content">
                <?php if (isset($success)): ?>
                    <div class="alert success">
                        <i class="fas fa-check-circle"></i>
                        <div><?php echo $success; ?></div>
                    </div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div><?php echo $error; ?></div>
                    </div>
                <?php endif; ?>

                <h2 class="section-title">Personal Information</h2>

                <form method="POST" class="profile-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-container">
                            <input type="text"
                                id="username"
                                name="username"
                                class="form-control"
                                value="<?php echo htmlspecialchars($user['username']); ?>"
                                required>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-container">
                            <input type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                value="<?php echo htmlspecialchars($user['email']); ?>"
                                required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="password">New Password (Kosongkan jika tidak ingin mengubah)</label>
                        <div class="input-container">
                            <input type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="Enter new password">
                            <i class="fas fa-lock input-icon"></i>
                            <i class="fas fa-eye-slash password-toggle" id="passwordToggle"></i>
                        </div>
                    </div>

                    <div class="action-buttons" style="display: flex; margin-top: 20px; gap: 15px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1; padding: 15px; border-radius: 12px; background: linear-gradient(135deg, #4361ee, #3a0ca3); color: white;">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="../index.php" class="btn btn-secondary" style="flex: 1; padding: 15px; border-radius: 12px; background: white; color: #666; border: 2px solid #eee;">
                            <i class="fas fa-home"></i> Kembali ke Halaman Awal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Password visibility toggle
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');

            passwordToggle.addEventListener('click', () => {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordToggle.classList.remove('fa-eye-slash');
                    passwordToggle.classList.add('fa-eye');
                } else {
                    passwordInput.type = 'password';
                    passwordToggle.classList.remove('fa-eye');
                    passwordToggle.classList.add('fa-eye-slash');
                }
            });

            // GSAP animations
            const tl = gsap.timeline();

            // Sidebar animation
            tl.from('.profile-sidebar', {
                duration: 0.8,
                x: -50,
                opacity: 0,
                ease: 'power3.out'
            });

            // Main content animation
            tl.from('.profile-main', {
                duration: 0.8,
                x: 50,
                opacity: 0,
                ease: 'power3.out'
            }, '-=0.5');

            // Header elements
            tl.from('.profile-header > *', {
                duration: 0.6,
                y: 30,
                opacity: 0,
                stagger: 0.1,
                ease: 'back.out(1.7)'
            }, '-=0.3');

            // Stats
            tl.from('.stat-item', {
                duration: 0.6,
                scale: 0.8,
                opacity: 0,
                stagger: 0.1,
                ease: 'back.out(1.2)'
            }, '-=0.2');

            // Form groups with staggered animation
            tl.from('.form-group', {
                duration: 0.5,
                y: 20,
                opacity: 0,
                stagger: 0.1,
                ease: 'power2.out'
            }, '-=0.1');

            // Buttons
            // tl.from('.btn', {
            //     duration: 0.5,
            //     y: 20,
            //     opacity: 0,
            //     stagger: 0.1,
            //     ease: 'back.out(1.5)'
            // }, '-=0.2');

            // Hover effects for buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(btn => {
                btn.addEventListener('mouseenter', () => {
                    gsap.to(btn, {
                        duration: 0.3,
                        scale: 1.05,
                        ease: 'power2.out'
                    });
                });

                btn.addEventListener('mouseleave', () => {
                    gsap.to(btn, {
                        duration: 0.3,
                        scale: 1,
                        ease: 'power2.out'
                    });
                });
            });
        });
    </script>
</body>

</html>