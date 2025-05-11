<?php
// Pastikan session sudah dimulai di halaman yang memuat widget ini
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan koneksi database sudah tersedia
require_once '../includes/config.php';
// Ambil data pengguna dari database jika sudah login
$displayName = "Guest";
$userEmail = "";
$avatarInitials = "G";

if (isset($_SESSION['pelanggan_id'])) {
    try {
        // Query langsung tanpa menggunakan fungsi dari model
        $sql = "SELECT * FROM pelanggan WHERE pelanggan_id = :id";
        $stmt = $koneksi->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['pelanggan_id'], PDO::PARAM_INT);
        $stmt->execute();

        $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pelanggan) {
            $displayName = $pelanggan['username'];
            $userEmail = $pelanggan['email'];

            // Membuat inisial untuk avatar dari nama pengguna
            $nameParts = explode(' ', $displayName);
            if (count($nameParts) > 1) {
                // Jika ada nama depan dan belakang, ambil inisial dari keduanya
                $avatarInitials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
            } else {
                // Jika hanya ada satu kata, ambil 2 huruf pertama atau 1 huruf jika nama sangat pendek
                $avatarInitials = strtoupper(substr($displayName, 0, min(2, strlen($displayName))));
            }
        }
    } catch (PDOException $e) {
        // Tangani error jika terjadi
        // Untuk produksi, sebaiknya log error, bukan tampilkan
        // echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings Page with GSAP Animation</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #000;
            color: #fff;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding: 20px 0;
            border-bottom: 1px solid #333;
            opacity: 0;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            background: linear-gradient(90deg, #ff00cc, #3333ff);
            background-clip: text;
            /* Properti standar */
            -webkit-background-clip: text;
            /* Properti dengan prefix vendor untuk kompatibilitas */
            -webkit-text-fill-color: transparent;
            color: transparent;
            /* Fallback untuk browser yang tidak mendukung -webkit-text-fill-color */
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(45deg, #ff00cc, #3333ff);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .username {
            font-weight: bold;
        }

        .email {
            font-size: 14px;
            color: #aaa;
        }

        .settings-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 40px;
        }

        .sidebar {
            opacity: 0;
            transform: translateX(-50px);
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin-bottom: 15px;
            padding: 12px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar li.active {
            background: linear-gradient(90deg, rgba(255, 0, 204, 0.2), rgba(51, 51, 255, 0.2));
            border-left: 3px solid #ff00cc;
        }

        .sidebar li:hover:not(.active) {
            background-color: #111;
        }

        .sidebar-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .content {
            opacity: 0;
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 25px;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #ff00cc, #3333ff);
            border-radius: 3px;
        }

        .card {
            background-color: #111;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            transform: translateY(50px);
            opacity: 0;
        }

        .card-title {
            font-size: 18px;
            margin-bottom: 20px;
            color: #ddd;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #aaa;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            background-color: #1a1a1a;
            border: 1px solid #333;
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: #ff00cc;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 0, 204, 0.2);
        }

        .switch-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .switch-label {
            font-size: 16px;
            color: #ddd;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #333;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background: linear-gradient(90deg, #ff00cc, #3333ff);
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #ff00cc;
        }

        input:checked+.slider:before {
            transform: translateX(30px);
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn-primary {
            background: linear-gradient(90deg, #ff00cc, #3333ff);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 0, 204, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #555;
            color: #fff;
            margin-right: 10px;
        }

        .btn-outline:hover {
            background-color: #111;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: linear-gradient(90deg, #ff00cc, #3333ff);
            color: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 0;
            transform: translateY(-20px);
            z-index: 1000;
        }

        .notification-icon {
            font-size: 20px;
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: radial-gradient(circle, rgba(255, 0, 204, 0.8), rgba(51, 51, 255, 0));
            border-radius: 50%;
            opacity: 0.5;
        }

        /* Floating elements */
        .floating-element {
            position: absolute;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle at center, rgba(255, 0, 204, 0.05), rgba(51, 51, 255, 0.05));
            border-radius: 50%;
            z-index: -1;
            opacity: 0.5;
            filter: blur(20px);
        }

        /* .cursor-follower {
            position: fixed;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(90deg, #ff00cc, #3333ff);
            opacity: 0.7;
            pointer-events: none;
            mix-blend-mode: screen;
            z-index: 9999;
            transform: translate(-50%, -50%);
            filter: blur(5px);
        } */

        /* Responsive styles */
        @media (max-width: 768px) {
            .settings-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                margin-bottom: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="cursor-follower"></div>

    <div class="particles" id="particles"></div>

    <div class="floating-element" style="top: 15%; left: 10%;"></div>
    <div class="floating-element" style="top: 50%; right: 5%;"></div>
    <div class="floating-element" style="bottom: 20%; left: 20%;"></div>

    <div class="container">
        <header id="header">
            <div class="logo"><a href="../index.php">ELiteWatch</a></div>
            <div class="user-profile">
                <div class="avatar"><?php echo $avatarInitials; ?></div>
                <div class="user-info">
                    <div class="username"><?php echo htmlspecialchars($displayName); ?></div>
                    <div class="email"><?php echo htmlspecialchars($userEmail); ?></div>
                </div>
            </div>
        </header>

        <div class="settings-container">
            <div class="sidebar" id="sidebar">
                <ul>
                    <li class="active">
                        <div class="sidebar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                        </div>
                        General
                    </li>
                    <li>
                        <div class="sidebar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        Account
                    </li>
                    <li>
                        <div class="sidebar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        Privacy
                    </li>
                    <li>
                        <div class="sidebar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                        </div>
                        Notifications
                    </li>
                    <li>
                        <div class="sidebar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                        </div>
                        Appearance
                    </li>
                    <li>
                        <div class="sidebar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                        </div>
                        Activity
                    </li>
                </ul>
            </div>

            <div class="content" id="content">
                <h2 class="section-title">General Settings</h2>

                <div class="card" id="card1">
                    <h3 class="card-title">User Preferences</h3>

                    <div class="switch-container">
                        <span class="switch-label">Dark Mode</span>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="switch-container">
                        <span class="switch-label">Notifications</span>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="switch-container">
                        <span class="switch-label">Sound Effects</span>
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="switch-container">
                        <span class="switch-label">Animations</span>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <div class="card" id="card2">
                    <h3 class="card-title">Profile Information</h3>

                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" class="form-control" value="John Smith">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" value="john@example.com">
                    </div>

                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" class="form-control" rows="3">UI/UX Designer & Frontend Developer</textarea>
                    </div>
                </div>

                <div class="card" id="card3">
                    <h3 class="card-title">System Settings</h3>

                    <div class="form-group">
                        <label for="language">Language</label>
                        <select id="language" class="form-control">
                            <option value="en" selected>English</option>
                            <option value="es">Spanish</option>
                            <option value="fr">French</option>
                            <option value="de">German</option>
                            <option value="ja">Japanese</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="timezone">Timezone</label>
                        <select id="timezone" class="form-control">
                            <option value="utc" selected>UTC (Coordinated Universal Time)</option>
                            <option value="est">EST (Eastern Standard Time)</option>
                            <option value="pst">PST (Pacific Standard Time)</option>
                            <option value="gmt">GMT (Greenwich Mean Time)</option>
                        </select>
                    </div>

                    <div class="actions">
                        <button class="btn btn-outline">Cancel</button>
                        <button class="btn btn-primary" id="saveBtn">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="notification" id="notification">
        <div class="notification-icon">✓</div>
        <span>Settings saved successfully!</span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize GSAP and ScrollTrigger
            gsap.registerPlugin(ScrollTrigger);

            // Create cursor follower
            const cursor = document.querySelector('.cursor-follower');

            document.addEventListener('mousemove', function(e) {
                gsap.to(cursor, {
                    x: e.clientX,
                    y: e.clientY,
                    duration: 0.3,
                    ease: "power2.out"
                });
            });

            // Header animation
            gsap.to('#header', {
                opacity: 1,
                duration: 1,
                ease: "power2.out"
            });

            // Sidebar animation
            gsap.to('#sidebar', {
                opacity: 1,
                x: 0,
                duration: 1,
                delay: 0.3,
                ease: "power2.out"
            });

            // Content animation
            gsap.to('#content', {
                opacity: 1,
                duration: 1,
                delay: 0.5,
                ease: "power2.out"
            });

            // Card animations
            gsap.to('#card1', {
                opacity: 1,
                y: 0,
                duration: 0.8,
                delay: 0.7,
                ease: "back.out(1.7)"
            });

            gsap.to('#card2', {
                opacity: 1,
                y: 0,
                duration: 0.8,
                delay: 0.9,
                ease: "back.out(1.7)"
            });

            gsap.to('#card3', {
                opacity: 1,
                y: 0,
                duration: 0.8,
                delay: 1.1,
                ease: "back.out(1.7)"
            });

            // Floating elements animation
            const floatingElements = document.querySelectorAll('.floating-element');

            floatingElements.forEach((element, index) => {
                gsap.to(element, {
                    y: "random(-20, 20)",
                    x: "random(-20, 20)",
                    duration: "random(3, 6)",
                    repeat: -1,
                    yoyo: true,
                    ease: "sine.inOut",
                    delay: index * 0.2
                });
            });

            // Create particles
            const particlesContainer = document.getElementById('particles');

            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');

                const size = Math.random() * 5 + 2;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;

                const posX = Math.random() * window.innerWidth;
                const posY = Math.random() * window.innerHeight;
                particle.style.left = `${posX}px`;
                particle.style.top = `${posY}px`;

                particlesContainer.appendChild(particle);

                // Animate particle
                gsap.to(particle, {
                    y: `+=${Math.random() * 200 - 100}`,
                    x: `+=${Math.random() * 200 - 100}`,
                    opacity: Math.random() * 0.5 + 0.1,
                    duration: Math.random() * 15 + 5,
                    repeat: -1,
                    yoyo: true,
                    ease: "sine.inOut",
                    delay: Math.random() * 5
                });
            }

            // Add functionality to menu items
            const menuItems = document.querySelectorAll('.sidebar li');

            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    // Animate text
                    gsap.from(this, {
                        color: "#ff00cc",
                        duration: 0.5,
                        ease: "power2.out"
                    });

                    // Create ripple effect
                    const ripple = document.createElement('div');
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.transform = 'translate(-50%, -50%)';
                    ripple.style.pointerEvents = 'none';
                    ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
                    ripple.style.width = '100px';
                    ripple.style.height = '100px';

                    this.appendChild(ripple);

                    gsap.to(ripple, {
                        scale: 3,
                        opacity: 0,
                        duration: 1,
                        ease: "power2.out",
                        onComplete: () => {
                            ripple.remove();
                        }
                    });
                });
            });

            // Save button click animation and notification
            const saveBtn = document.getElementById('saveBtn');
            const notification = document.getElementById('notification');

            saveBtn.addEventListener('click', function() {
                // Button animation
                gsap.to(this, {
                    scale: 0.95,
                    duration: 0.1,
                    yoyo: true,
                    repeat: 1,
                    ease: "power2.out"
                });

                // Show notification
                gsap.to(notification, {
                    opacity: 1,
                    y: 0,
                    duration: 0.5,
                    ease: "back.out(1.7)"
                });

                // Hide notification after 3 seconds
                gsap.to(notification, {
                    opacity: 0,
                    y: -20,
                    duration: 0.5,
                    delay: 3,
                    ease: "power2.in"
                });
            });

            // Form input animation
            const formInputs = document.querySelectorAll('.form-control');

            formInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    gsap.to(this, {
                        borderColor: "#ff00cc",
                        boxShadow: "0 0 0 3px rgba(255, 0, 204, 0.2)",
                        duration: 0.3,
                        ease: "power2.out"
                    });
                });

                input.addEventListener('blur', function() {
                    if (!this.value) {
                        gsap.to(this, {
                            borderColor: "#333",
                            boxShadow: "none",
                            duration: 0.3,
                            ease: "power2.out"
                        });
                    }
                });
            });

            // Switch animation
            const switches = document.querySelectorAll('.switch input');

            switches.forEach(switchEl => {
                switchEl.addEventListener('change', function() {
                    const slider = this.nextElementSibling;

                    if (this.checked) {
                        gsap.fromTo(slider, {
                            backgroundColor: "#333"
                        }, {
                            background: "linear-gradient(90deg, #ff00cc, #3333ff)",
                            duration: 0.3,
                            ease: "power2.out"
                        });
                    } else {
                        gsap.to(slider, {
                            backgroundColor: "#333",
                            clearProps: "background",
                            duration: 0.3,
                            ease: "power2.out"
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>