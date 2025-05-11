<?php
session_start();
include '../db/connection.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Query untuk mendapatkan detail produk berdasarkan ID
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<p>Produk tidak ditemukan.</p>";
    exit;
}

// Ambil username dan user_id dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

// Jika user login, ambil alamat pengguna
if ($user_id) {
    $address_query = $conn->prepare("SELECT alamat FROM users WHERE user_id = ?");
    $address_query->bind_param("i", $user_id);
    $address_query->execute();
    $address_result = $address_query->get_result();

    if ($address_result->num_rows > 0) {
        $user_address = $address_result->fetch_assoc()['alamat'];
    } else {
        $user_address = "Alamat tidak ditemukan";
    }

    updateCartCount($conn, $user_id);
}

// Inisialisasi jumlah keranjang jika belum ada di session
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

// Fungsi untuk memperbarui jumlah barang di keranjang
function updateCartCount($conn, $user_id)
{
    $cart_count_query = $conn->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
    $cart_count_query->bind_param("i", $user_id);
    $cart_count_query->execute();
    $result = $cart_count_query->get_result();
    $_SESSION['cart_count'] = (int)($result->fetch_assoc()['total'] ?? 0);
    $cart_count_query->close();
}

// Proses form jika ada permintaan POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Cek apakah user sudah login
    if (!$user_id) {
        // Jika belum login, arahkan ke halaman login
        header('Location: ../login.php');
        exit();
    }

    // Ambil data dari form
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;
    $size = $_POST['sizes']; // Ambil ukuran yang dipilih dari input form

    if (empty($size)) {
        echo "<script>alert('Harap pilih ukuran produk');</script>";
        exit();
    }

    // Cek apakah produk dengan ukuran yang dipilih sudah ada di keranjang
    $check_cart_stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ? AND size = ?");
    $check_cart_stmt->bind_param("iis", $user_id, $product_id, $size);
    if ($check_cart_stmt->execute()) {
        $result = $check_cart_stmt->get_result();

        if ($result->num_rows > 0) {
            // Update quantity di keranjang jika sudah ada
            $current_quantity = $result->fetch_assoc()['quantity'];
            $new_quantity = $current_quantity + $quantity;

            // Update entry yang ada
            $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ? AND size = ?");
            $update_stmt->bind_param("iiis", $new_quantity, $user_id, $product_id, $size);
            if ($update_stmt->execute()) {
                echo "<script>alert('Produk berhasil diperbarui di keranjang.');</script>";
            }
            $update_stmt->close();
            
        } else {
            // Insert produk baru ke keranjang
            $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, size) VALUES (?, ?, ?, ?)");
            if ($insert_stmt) {
                $insert_stmt->bind_param("iiis", $user_id, $product_id, $quantity, $size);
                if ($insert_stmt->execute()) {
                    echo "<script>alert('Produk berhasil ditambahkan ke keranjang.');</script>";
                }
                $insert_stmt->close();
            }
        }

        updateCartCount($conn, $user_id);
        
    } else {
        echo "<script>alert('Terjadi kesalahan saat memeriksa keranjang.');</script>";
        exit();
    }
}

// Fetch sizes from database (hanya ukuran tertentu)
$query_sizes = "SELECT size_name FROM sizes WHERE size_name IN ('36', '37', '38', '39', '40', '41', '42', '43', '44', '45')";
$result_sizes = @$conn->query($query_sizes); // Menambahkan @ untuk menghindari warning

$sizes = [];
if ($result_sizes && $result_sizes->num_rows > 0) {
    while ($row = $result_sizes->fetch_assoc()) {
        // Store sizes for selection in front-end
        $sizes[] = htmlspecialchars($row['size_name']); // Ambil nilai size_name dan simpan di array
    }
} else {
    echo "<p>Tidak ada ukuran tersedia.</p>";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - <?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --text-color: #2c3e50;
            --gray-light: #f6f8ff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--gray-light);
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
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .search-form {
            flex: 1;
            max-width: 500px;
            margin: 0 1rem;
            position: relative;
        }

        .search-form input {
            width: 100%;
            padding: 0.8rem 3rem 0.8rem 1rem;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
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
        }

        .register-button,
        .logout-button {
            background: var(--accent-color);
            color: white;
            border: 2px solid var(--accent-color);
        }

        /* Hover effect */
        .btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
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

        /* Product Container */
        .product-container {
            max-width: 1200px;
            margin: 2rem auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        /* Image Section */
        .image-section {
            position: relative;
        }

        .main-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            transition: var(--transition);
        }

        .main-image:hover {
            transform: scale(1.02);
        }

        .favorite-container {
            margin-top: 1rem;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .favorite-container:hover {
            color: var(--accent-color);
        }

        /* Product Info */
        .product-info {
            padding: 1rem;
        }

        .product-title {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .rating-container {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stars {
            color: #ffd700;
        }

        .price-section {
            background: var(--gray-light);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .price {
            font-size: 2rem;
            color: var(--accent-color);
            font-weight: 600;
        }

        /* Size Selection */
        .size-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            max-width: 600px;
            padding: 16px;
        }

        .size-option {
            position: relative;
            cursor: pointer;
        }

        .size-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .size-label {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .size-label:hover {
            border-color: #90caf9;
        }

        .radio-custom {
            width: 18px;
            height: 18px;
            border: 2px solid #e0e0e0;
            border-radius: 50%;
            margin-right: 10px;
            position: relative;
            transition: all 0.3s ease;
        }

        .radio-custom:after {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            background: #2196f3;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.2s ease;
        }

        .size-option input[type="radio"]:checked+.size-label {
            border-color: #2196f3;
        }

        .size-option input[type="radio"]:checked+.size-label .radio-custom {
            border-color: #2196f3;
        }

        .size-option input[type="radio"]:checked+.size-label .radio-custom:after {
            transform: translate(-50%, -50%) scale(1);
        }

        .size-text {
            font-family: Arial, sans-serif;
            color: #333;
        }

        /* Quantity Section */
        .quantity-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .quantity-input {
            display: flex;
            align-items: center;
            border: 2px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
        }

        .quantity-btn {
            padding: 0.8rem 1.2rem;
            background: none;
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .quantity-btn:hover {
            background: var(--gray-light);
        }

        #jumlah {
            width: 60px;
            text-align: center;
            border: none;
            padding: 0.8rem 0;
        }

        /* Add to Cart Button */
        .add-to-cart-btn {
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .add-to-cart-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        /* Reviews Section */
        .review-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .review {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            transition: var(--transition);
        }

        .review:hover {
            background: var(--gray-light);
        }

        .review-header {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .review-header img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .product-container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            nav {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .search-form {
                width: 100%;
                margin: 0;
            }

            .nav-links {
                width: 100%;
                display: flex;
                justify-content: space-around;
            }

            .nav-links a {
                margin: 0;
            }
        }

        /* Shipping and Guarantee Section Styles */
        .shipping-section {
            margin: 1.5rem 0;
            padding: 1rem 0;
            border-bottom: 1px solid #efefef;
        }

        .shipping-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 0;
            color: #212121;
        }

        .shipping-label {
            min-width: 110px;
            color: #757575;
            font-size: 0.9rem;
        }

        .shipping-icon {
            color: #212121;
            font-size: 1.2rem;
            width: 24px;
        }

        .shipping-value {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .shipping-value .dropdown {
            color: #757575;
            font-size: 0.8rem;
            margin-left: 0.25rem;
        }

        .guarantee-section {
            display: flex;
            align-items: center;
            gap: 2rem;
            padding: 1rem 0;
            border-bottom: 1px solid #efefef;
        }

        .guarantee-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #212121;
            font-size: 0.9rem;
        }

        .guarantee-icon {
            color: rgb(238, 77, 45);
            font-size: 1rem;
        }

        /* Pagination Container */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin: 2rem auto;
            padding: 1rem 0;
        }

        /* Pagination Links */
        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0.5rem;
            border: 1px solid #dbdbdb;
            border-radius: 4px;
            color: #555555;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        /* Hover Effect */
        .page-link:hover {
            background-color: #f5f5f5;
            border-color: #999999;
            color: #333333;
        }

        /* Active Page */
        .page-link.active {
            background-color: #ee4d2d;
            border-color: #ee4d2d;
            color: white;
            cursor: default;
        }

        .page-link.active:hover {
            background-color: #ee4d2d;
            border-color: #ee4d2d;
            color: white;
        }

        /* Previous/Next Buttons (if needed) */
        .page-link.prev,
        .page-link.next {
            padding: 0.5rem 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .pagination {
                gap: 0.25rem;
            }

            .page-link {
                min-width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>

    <body>
        <nav>
            <div class="nav-content">
                <div class="logo">
                    <a href="../index.php">
                        <i class="fas fa-store"></i>
                        KaJaTu Store
                    </a>
                </div>
                <div class="search-container">
                    <form method="GET" action="search.php">
                        <input type="text" name="query" placeholder="Cari produk fashion..." required>
                        <button class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="nav-links">
                    <?php if ($user_id): ?>
                        <a href="cart.php" class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count"><?= $_SESSION['cart_count'] ?? 0 ?></span>
                        </a>
                        <span class="welcome-text">Hi, <?php echo htmlspecialchars($username); ?></span>
                        <a href="user/logout.php" class="btn logout-button">Logout</a>
                    <?php else: ?>
                        <a href="../login.php" class="btn login-button">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="../register.php" class="btn register-button">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
        <div class="product-container">
            <div class="image-section">
                <img alt="<?= htmlspecialchars($product['name']) ?>" class="main-image" src="../images/<?= htmlspecialchars($product['image']) ?>" />
                <div class="favorite-container" onclick="toggleFavorite()">
                    <i class="fas fa-heart" id="favorite-icon">
                    </i>
                    <span>
                        Favorit (6,1RB)
                    </span>
                </div>
            </div>
            <div class="product-info">
                <h1 class="product-title">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>
                <div class="rating-container">
                    <div class="rating">
                        <span>4.3</span>
                        <span class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </span>
                    </div>
                    <div class="reviews">
                        <span class="underline">26,7RB</span>
                        <span>Penilaian</span>
                    </div>
                    <div class="sales">
                        <span class="underline">10RB+</span>
                        <span>Sold</span>
                    </div>
                </div>
                <div class="price-section">
                    <span class="price">
                        <span class="currency">
                            Rp
                        </span>
                        <?= number_format($product['price'], 0, ',', '.') ?>
                    </span>
                </div>
                <div class="shipping-section">
                    <!-- Shipping Info -->
                    <div class="shipping-row">
                        <i class="fas fa-truck shipping-icon"></i>
                        <div class="shipping-label">Pengiriman</div>
                        <div class="shipping-value">
                            <span>Pengiriman Ke</span>
                            <div><strong><?php
                                            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                                                echo htmlspecialchars($user_address) . ' <i class="fas fa-chevron-down"></i>';
                                            } else {
                                                echo '';  // Kosong jika user belum login
                                            }
                                            ?></strong></div>
                            <i class="fas fa-chevron-down dropdown"></i>
                        </div>
                    </div>

                    <!-- Shipping Cost -->
                    <div class="shipping-row">
                        <div class="shipping-label" style="margin-left: 34px;">Ongkos Kirim</div>
                        <div class="shipping-value">
                            <strong>Rp0</strong>
                            <i class="fas fa-chevron-down dropdown"></i>
                        </div>
                    </div>
                </div>

                <!-- Guarantee Section -->
                <div class="guarantee-section">
                    <div class="guarantee-item">
                        <i class="fas fa-shield-alt guarantee-icon"></i>
                        <span>Bebas Pengembalian</span>
                    </div>
                    <div class="guarantee-item">
                        <i class="fas fa-money-bill-wave guarantee-icon"></i>
                        <span>COD-Cek Dulu</span>
                    </div>
                    <div class="guarantee-item">
                        <i class="fas fa-shield-alt guarantee-icon"></i>
                        <span>Proteksi Kerusakan</span>
                        <i class="fas fa-chevron-down dropdown"></i>
                    </div>
                </div>
                <form method="POST">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

                    <div class="size-grid">
                        <?php foreach ($sizes as $size): ?>
                            <label class="size-option">
                                <input type="radio" name="sizes" value="<?php echo htmlspecialchars($size); ?>" required>
                                <div class="size-label">
                                    <span class="radio-custom"></span>
                                    <span class="size-text"><?php echo htmlspecialchars($size); ?></span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>


                    <div class="quantity-section">
                        <button type="button" class="quantity-btn">-</button>
                        <input type="number" name="jumlah" id="jumlah" value="1" min="1">
                        <button type="button" class="quantity-btn">+</button>
                    </div>

                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                </form>

            </div>
        </div>

        <div class="product-description">
            <h2 class="description-title">Deskripsi Produk</h2>
            <div class="description-content">
                <?= nl2br(htmlspecialchars($product['detail'])) ?>
            </div>
        </div>

        <div class="review-container">
            <h3>Penilaian Produk</h3>
            <div class="page active" id="page1">
                <div class="review">
                    <div class="review-header">
                        <img alt="User profile picture" src="../images/ulasan1.jpeg" />
                        <span class="name">a******o</span>
                        <span class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </span>
                        <span class="date">2024-01-10 21:00 | Variasi: Hitam,L</span>
                    </div>
                    <div class="review-content">
                        <div class="label">Tampilan:</div>
                        <div class="value">baik</div>
                        <div class="label">Warna:</div>
                        <div class="value">Hitam L, Mustard L</div>
                        <div class="description">Lumayan dengan harga segitu... Mksh seller dan kurir Shopee...</div>
                    </div>
                    <div class="review-footer">
                        <i class="fas fa-thumbs-up"></i>
                        <span class="likes">231</span>
                    </div>
                </div>

                <div class="review">
                    <div class="review-header">
                        <img alt="User profile picture" src="../images/ulasan2.png" />
                        <span class="name">
                            Pengguna Shopee
                        </span>
                        <span class="stars">
                            <i class="fas fa-star">
                            </i>
                            <i class="fas fa-star">
                            </i>
                            <i class="fas fa-star">
                            </i>
                            <i class="fas fa-star">
                            </i>
                        </span>
                        <span class="date">
                            2023-04-11 13:21 | Variasi: Putih,L
                        </span>
                    </div>
                    <div class="review-content">
                        <div class="label">
                            Tampilan:
                        </div>
                        <div class="value">
                            bagus
                        </div>
                        <div class="label">
                            Warna:
                        </div>
                        <div class="value">
                            putih xl, putih l, biru XL, abu XL...
                        </div>
                        <div class="description">
                            Sangat puas dengan pelayanannya... Akan belanja lagi di sini...
                        </div>
                    </div>
                    <div class="review-footer">
                        <i class="fas fa-thumbs-up">
                        </i>
                        <span class="likes">
                            120
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add more reviews for page1 if needed -->
        <div class="review-container">
            <div class="page active " id="page2" style="display: none;">
                <div class="review">
                    <div class="review-header">
                        <img alt="User profile picture" src="../images/ulasan3.jpeg" />
                        <span class="name">b******o</span>
                        <span class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </span>
                        <span class="date">
                            2023-04-11 13:21 | Variasi: Putih,L
                        </span>
                    </div>
                    <div class="review-content">
                        <div class="label">
                            Tampilan:
                        </div>
                        <div class="value">
                            bagus
                        </div>
                        <div class="label">
                            Warna:
                        </div>
                        <div class="value">
                            putih xl, putih l, biru XL, abu XL...
                        </div>
                        <div class="description">
                            Barang sesuai ekspektasi. Dengan harga segini, udah oke banget! Terima kasih seller dan kurirnya!
                        </div>
                    </div>
                    <div class="review-footer">
                        <i class="fas fa-thumbs-up">
                        </i>
                        <span class="likes">
                            120
                        </span>
                    </div>
                </div>

                <div class="review">
                    <div class="review-header">
                        <img alt="User profile picture" src="../images/ulasan4.jpeg" />
                        <span class="name">a******n</span>
                        <span class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </span>
                        <span class="date">
                            2023-05-08 10:45 | Variasi: Hitam, M
                        </span>
                    </div>
                    <div class="review-content">
                        <div class="label">
                            Tampilan:
                        </div>
                        <div class="value">
                            elegan
                        </div>
                        <div class="label">
                            Warna:
                        </div>
                        <div class="value">
                            hitam M, hitam L, putih M, abu M...
                        </div>
                        <div class="description">
                            Produk luar biasa! Sangat puas dengan kualitasnya. Pengiriman juga cepat, mantap seller!
                        </div>
                    </div>
                    <div class="review-footer">
                        <i class="fas fa-thumbs-up">
                        </i>
                        <span class="likes">
                            145
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <div class="review-container">
            <div class="page active" id="page3" style="display: none;">
                <div class="review">
                    <div class="review-header">
                        <img alt="User profile picture" src="../images/ulasan5.jpg" />
                        <span class="name">k******i</span>
                        <span class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </span>
                        <span class="date">
                            2023-06-15 18:32 | Variasi: Biru, XL
                        </span>
                    </div>
                    <div class="review-content">
                        <div class="label">
                            Tampilan:
                        </div>
                        <div class="value">
                            sangat menarik
                        </div>
                        <div class="label">
                            Warna:
                        </div>
                        <div class="value">
                            biru XL, biru L, merah XL, hitam L...
                        </div>
                        <div class="description">
                            Produk melebihi harapan saya. Detailnya rapi dan bahannya nyaman dipakai. Terima kasih banyak!
                        </div>
                    </div>
                    <div class="review-footer">
                        <i class="fas fa-thumbs-up"></i>
                        <span class="likes">165</span>
                    </div>
                </div>

                <div class="review">
                    <div class="review-header">
                        <img alt="User profile picture" src="../images/ulasan6.jpg" />
                        <span class="name">z******r</span>
                        <span class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </span>
                        <span class="date">
                            2023-07-02 14:19 | Variasi: Merah, M
                        </span>
                    </div>
                    <div class="review-content">
                        <div class="label">
                            Tampilan:
                        </div>
                        <div class="value">
                            simpel tapi elegan
                        </div>
                        <div class="label">
                            Warna:
                        </div>
                        <div class="value">
                            merah M, merah L, abu M, biru M...
                        </div>
                        <div class="description">
                            Barang sesuai dengan gambar, kualitasnya bagus. Cocok untuk penggunaan sehari-hari. Sukses selalu seller!
                        </div>
                    </div>
                    <div class="review-footer">
                        <i class="fas fa-thumbs-up"></i>
                        <span class="likes">98</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Add more pages as needed -->

        <div class="pagination">
            <a class="page-link prev" href="#" onclick="showPage(event, 'prev')">
                <i class="fas fa-chevron-left"></i>
            </a>
            <a class="page-link active" href="#" data-page="page1" onclick="showPage(event, 'page1')">1</a>
            <a class="page-link" href="#" data-page="page2" onclick="showPage(event, 'page2')">2</a>
            <a class="page-link" href="#" data-page="page3" onclick="showPage(event, 'page3')">3</a>
            <a class="page-link next" href="#" onclick="showPage(event, 'next')">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <script>
            // Enhanced JavaScript functionality
            document.addEventListener('DOMContentLoaded', function() {
                // Size selection
                const sizeItems = document.querySelectorAll('.size-item');
                sizeItems.forEach(item => {
                    item.addEventListener('click', function() {
                        sizeItems.forEach(i => i.classList.remove('selected'));
                        this.classList.add('selected');
                    });
                });

                // Quantity controls
                const quantityInput = document.getElementById('jumlah');
                const incrementBtn = document.querySelector('.quantity-btn:last-child');
                const decrementBtn = document.querySelector('.quantity-btn:first-child');

                incrementBtn.addEventListener('click', () => {
                    quantityInput.value = parseInt(quantityInput.value) + 1;
                });

                decrementBtn.addEventListener('click', () => {
                    if (parseInt(quantityInput.value) > 1) {
                        quantityInput.value = parseInt(quantityInput.value) - 1;
                    }
                });

                // Favorite toggle with animation
                const favoriteBtn = document.querySelector('.favorite-container');
                const favoriteIcon = document.getElementById('favorite-icon');

                favoriteBtn.addEventListener('click', () => {
                    favoriteIcon.classList.toggle('active');
                    if (favoriteIcon.classList.contains('active')) {
                        favoriteIcon.style.color = '#e74c3c';
                        favoriteIcon.style.transform = 'scale(1.2)';
                        setTimeout(() => {
                            favoriteIcon.style.transform = 'scale(1)';
                        }, 200);
                    } else {
                        favoriteIcon.style.color = 'grey';
                    }
                });
            });

            function showPage(event, pageId) {
                event.preventDefault();

                // Hide all pages
                document.querySelectorAll('.page').forEach(page => {
                    page.style.display = 'none';
                });

                // Remove active class from all page links
                document.querySelectorAll('.page-link').forEach(link => {
                    link.classList.remove('active');
                });

                // Show the selected page
                document.getElementById(pageId).style.display = 'block';

                // Add active class to the clicked link
                event.target.classList.add('active');
            }

            function selectSize(size) {
                // Menandai ukuran yang dipilih
                document.querySelectorAll('.size-item').forEach(item => {
                    item.classList.remove('selected'); // Menghapus kelas selected pada semua ukuran
                });

                const selectedItem = document.querySelector(`[data-size="${size}"]`);
                selectedItem.classList.add('selected'); // Menambahkan kelas selected pada ukuran yang dipilih

                // Menyimpan ukuran yang dipilih di input tersembunyi
                document.getElementById('size-input').value = size;
            }

            // Optional: Add functions to increment/decrement quantity
            function incrementQuantity() {
                var quantity = document.getElementById('jumlah').value;
                document.getElementById('jumlah').value = parseInt(quantity) + 1;
            }

            function decrementQuantity() {
                var quantity = document.getElementById('jumlah').value;
                if (quantity > 1) {
                    document.getElementById('jumlah').value = parseInt(quantity) - 1;
                }
            }
        </script>
    </body>

</html>