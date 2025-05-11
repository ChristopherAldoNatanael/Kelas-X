<?php
session_start();
include 'db/connection.php';

// Check if user is logged in
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

// Inisialisasi cart_count di awal
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

// Jika user sedang login, perbarui cart_count saat pertama kali masuk halaman
if ($user_id) {
    updateCartCount($conn, $user_id);
}

// Ambil data produk untuk ditampilkan di halaman
$products = $conn->query("SELECT * FROM products LIMIT 4");


// Proses penambahan produk ke keranjang
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$user_id) {
        header('Location: login.php'); // Redirect jika belum login
        exit();
    }

    $product_id = $_POST['product_id'];
    $quantity = 1; // Default quantity

    // Cek apakah produk sudah ada di keranjang
    $check_cart_stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check_cart_stmt->bind_param("ii", $user_id, $product_id);
    $check_cart_stmt->execute();
    $result = $check_cart_stmt->get_result();

    if ($result->num_rows > 0) {
        $current_quantity = $result->fetch_assoc()['quantity'];
        $new_quantity = $current_quantity + $quantity;

        // Update quantity di keranjang
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // Tambahkan produk baru ke keranjang
        $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    // Perbarui cart_count setelah penambahan produk
    updateCartCount($conn, $user_id);
    echo "<script>alert('Produk berhasil ditambahkan ke keranjang.');</script>";
    header("Refresh:0"); // Refresh halaman untuk menampilkan pembaruan
}

// Fungsi untuk memperbarui jumlah cart
function updateCartCount($conn, $user_id)
{
    $cart_count_query = $conn->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
    $cart_count_query->bind_param("i", $user_id);
    $cart_count_query->execute();
    $result = $cart_count_query->get_result();
    $_SESSION['cart_count'] = $result->fetch_assoc()['total'] ?? 0;
    $cart_count_query->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KaJaTu Store - Fashion Terkini</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --background-color: #f6f8ff;
            --text-color: #2c3e50;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: var(--background-color);
            color: var(--text-color);
        }

        /* Enhanced Navigation */
        nav {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }

        .logo a {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            font-size: 1.8rem;
        }

        .search-container {
            flex: 1;
            max-width: 500px;
            position: relative;
        }

        .search-container input {
            width: 100%;
            padding: 0.8rem 2.5rem 0.8rem 1rem;
            border: 2px solid transparent;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .search-container input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .search-button {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .login-button {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            /* Optional border for better contrast */
        }

        .register-button,
        .logout-button {
            background: var(--accent-color);
            color: white;
            border: 2px solid var(--accent-color);
            /* Optional border for better contrast */
        }

        /* Hover effect */
        .btn:hover {
            transform: translateY(-4px);
            /* Make the button appear to rise */
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            /* Add subtle shadow */
        }

        /* Hover effect khusus untuk login-button */
        .login-button:hover {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        /* Hover effect khusus untuk register-button dan logout-button */
        .register-button:hover,
        .logout-button:hover {
            background: var(--primary-color);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .cart-icon {
            position: relative;
            color: white;
            font-size: 1.2rem;
            text-decoration: none;
            margin-right: 1rem;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--accent-color);
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }

        /* Enhanced Slider */
        .slider {
            max-width: 1200px;
            height: 400px;
            margin: 2rem auto;
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .slide.active {
            opacity: 1;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .slider-nav button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            color: var(--primary-color);
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Categories Section */
        .categories-section {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 1rem;
        }

        .title {
            font-size: 1.8rem;
            color: var(--text-color);
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 1rem;
        }

        .title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 3px;
        }

        .categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        .category {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .category:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .category a {
            text-decoration: none;
            color: var(--text-color);
        }

        .category img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .category:hover img {
            transform: scale(1.05);
        }

        .category p {
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 500;
            text-align: center;
        }

        /* Products Section */
        .product-section {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 1rem;
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        .product {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .product:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .product img {
            width: 100%;
            height: auto;
            /* Ubah dari height: 200px; menjadi height: auto; */
            object-fit: cover;
            /* Pastikan gambar tetap terjaga proporsinya */
            transition: transform 0.3s ease;
        }

        .product:hover img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-name {
            font-size: 1.1rem;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .product-price {
            color: var(--accent-color);
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-content {
                flex-direction: column;
                padding: 1rem;
            }

            .search-container {
                width: 100%;
            }

            .slider {
                height: 300px;
                margin: 1rem;
            }

            .categories,
            .product-list {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }
        }

        /* Footer */
        .footer {
            background-color: #f8f9fa;
            color: #333;
            font-family: 'Arial', sans-serif;
            margin-top: auto;
            /* Push footer to bottom */
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            padding: 60px 20px;
        }

        .column {
            flex: 1;
            margin: 0 15px;
        }

        .column h3 {
            color: #2c3e50;
            font-size: 1.2rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }

        .column ul {
            list-style: none;
            padding: 0;
        }

        .column ul li {
            margin-bottom: 12px;
        }

        .column ul li a {
            color: #666;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .column ul li a:hover {
            color: #3498db;
        }

        .payment-methods,
        .shipping-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .payment-methods img,
        .shipping-methods img {
            max-width: 100%;
            height: auto;
            transition: transform 0.3s ease;
            filter: grayscale(30%);
        }

        .payment-methods img:hover,
        .shipping-methods img:hover {
            transform: translateY(-5px);
            filter: grayscale(0%);
        }

        /* Updated copyright styles */
        .copyright {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
            bottom: 0;
            /* Memastikan tidak ada space di bawah */
            width: 100%;
            margin-bottom: 0;
            /* Menghilangkan margin bawah */
        }

        .copyright::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .copyright p {
            margin: 5px 0;
            font-size: 0.9rem;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
        }

        .copyright p:first-child {
            font-weight: bold;
            font-size: 1rem;
        }

        .copyright p:last-child {
            opacity: 0.9;
            font-style: italic;
            margin-bottom: 0;
            /* Menghilangkan margin bawah dari paragraf terakhir */
        }

        @media (max-width: 768px) {
            .footer-container {
                flex-direction: column;
            }

            .column {
                margin: 20px 0;
            }
        }
    </style>
</head>

<body>
    <!-- The rest of the HTML structure remains the same, but with updated class names and structure -->
    <!-- Note: Make sure to add the store icon to the logo -->
    <nav>
        <div class="nav-content">
            <div class="logo">
                <a href="index.php">
                    <i class="fas fa-store"></i>
                    KaJaTu Store
                </a>
            </div>
            <div class="search-container">
                <form method="GET" action="user/search.php">
                    <input type="text" name="query" placeholder="Cari produk fashion..." required>
                    <button class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            <div class="nav-links">
                <?php if ($user_id): ?>
                    <a href="user/cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?= $_SESSION['cart_count'] ?? 0 ?></span>
                    </a>
                    <span class="welcome-text">Hi, <?php echo htmlspecialchars($username); ?></span>
                    <a href="user/logout.php" class="btn logout-button">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn login-button">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="register.php" class="btn register-button">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </nav>

    <div class="slider">
        <div class="slide active">
            <img src="images/gambar1.png" alt="Fashion Collection 1">
        </div>
        <div class="slide">
            <img src="images/gambar2.jpeg" alt="Fashion Collection 2">
        </div>
        <div class="slider-nav">
            <button class="prev" onclick="prevSlide()">❮</button>
            <button class="next" onclick="nextSlide()">❯</button>
        </div>
    </div>

    <section class="categories-section animate-fade-in">
        <h2 class="title">KATEGORI PILIHAN</h2>
        <div class="categories">
            <div class="category">
                <a href="user/kaospria.php">
                    <img src="images/kaospria.png" alt="Kaos Pria">
                    <p>Kaos Pria</p>
                </a>
            </div>
            <div class="category">
                <a href="user/kaoswanita.php">
                    <img src="images/kaoswanita.jpg" alt="Kaos Wanita">
                    <p>Kaos Wanita</p>
                </a>
            </div>
            <div class="category">
                <a href="user/jamtangan.php">
                    <img src="images/jam6.png" alt="Jam Tangan">
                    <p>Jam Tangan</p>
                </a>
            </div>
            <div class="category">
                <a href="user/sepatu.php">
                    <img src="images/sepatu.jpg" alt="Sepatu">
                    <p>Sepatu</p>
                </a>
            </div>
        </div>
    </section>

    <section class="product-section animate-fade-in">
        <h2 class="title">PRODUK UNGGULAN</h2>
        <div class="product-list">
            <?php while ($row = $products->fetch_assoc()) { ?>
                <div class="product">
                    <a href="user/detail.php?id=<?php echo htmlspecialchars($row['id']); ?>">
                        <img src="images/<?php echo htmlspecialchars($row['image']); ?>"
                            alt="<?php echo htmlspecialchars($row['name']); ?>">
                    </a>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="product-price">Rp<?php echo number_format($row['price'], 0, ',', '.'); ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>


    <footer class="footer">
        <div class="footer-container">
            <div class="column">
                <h3>Jelajahi KaJaTu</h3>
                <ul>
                    <li><a href="tentangkami.html">Tentang Kami</a></li>
                </ul>
            </div>

            <div class="column">
                <h3>Pembayaran</h3>
                <div class="payment-methods">
                    <img alt="COD logo" src="images/cash-on-delivery.jpg" />
                </div>

                <h3>Pengiriman</h3>
                <div class="shipping-methods">
                    <img alt="Gosend logo" src="https://storage.googleapis.com/a1aa/image/SvwSg9LgHaqfaiVrkIsqkXhUuNnmTHW8mgS935RhKA1gHTDKA.jpg" />
                    <img alt="GrabExpress logo" src="https://storage.googleapis.com/a1aa/image/R7qUPOicG1r0I9yRZMOkvRfvUSeBvuhe5Iyq90CfhHL46YaQB.jpg" />
                    <img alt="J&T Cargo logo" src="https://storage.googleapis.com/a1aa/image/hOyZXigy7qI3MVTNJUHM1l9lfgoBv4N9J4Eidl19fSezdMNoA.jpg" />
                    <img alt="J&T Express logo" src="https://storage.googleapis.com/a1aa/image/MfhqsS5keVrrq0uiaFxuE8Wefp1boKiBGlixJZPRdxRr6YaQB.jpg" />
                    <img alt="Ninja Express logo" src="https://storage.googleapis.com/a1aa/image/QD3bX67VJf3dZ6141dGPqUrewhnoLq0C10xiRe4tBsefyx0gC.jpg" />
                    <img alt="Pos Aja logo" src="https://storage.googleapis.com/a1aa/image/7BdO67AnuRIDDlf8surXpBZZhsiUmJv0ZHYihSuInfNfdMNoA.jpg" />
                    <img alt="Sicepat logo" src="https://storage.googleapis.com/a1aa/image/UBKwX56AEApfIqX3x0zu8Xe8kdnB5gltSyyblLhP7ehadMNoA.jpg" />
                    <img alt="SPX logo" src="https://storage.googleapis.com/a1aa/image/fHYbXQ6jYfjcOEfM6pBfeddAMZWu1YKSE3sRzsUOl1uw3x0gC.jpg" />
                </div>
            </div>

            <div class="column">
                <h3>Kontak Kami</h3>
                <div class="social-media">
                    <ul>
                        <li>
                            <a href="https://www.instagram.com/christopher.aldoo/">
                                <i class="fab fa-instagram"></i>
                                Instagram
                            </a>
                        </li>
                        <li>
                            <a href="mailto:christophernatanael123@gmail.com">
                                <i class="far fa-envelope"></i>
                                Email
                            </a>
                        </li>
                        <li>
                            <a href="https://wa.me/6285731279959">
                                <i class="fas fa-phone"></i>
                                Telepon
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 KaJaTu Store - Fashion Terkini</p>
            <p>Temukan gaya fashion terbaikmu bersama kami</p>
        </div>
    </footer>






    <script>
        // Enhanced Slider Functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            currentSlide = (index + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        // Auto-advance slides
        setInterval(nextSlide, 5000);

        // Animate elements on scroll
        function animateOnScroll() {
            const elements = document.querySelectorAll('.animate-fade-in');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                if (elementTop < windowHeight - 100) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
        }

        window.addEventListener('scroll', animateOnScroll);
        animateOnScroll(); // Initial check
    </script>
</body>

</html>