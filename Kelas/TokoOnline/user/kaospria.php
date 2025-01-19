<?php
session_start();
include '../db/connection.php';

// Check if user is logged in
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

// Initialize cart_count
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

// Update cart count if user is logged in
if ($user_id) {
    updateCartCount($conn, $user_id);
}

// Get products from kaos_pria category
$products = $conn->query("SELECT * FROM products WHERE category = 'kaos_pria'");

// Process add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$user_id) {
        header('Location: ../login.php');
        exit();
    }

    $product_id = $_POST['product_id'];
    $quantity = 1;

    $check_cart_stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check_cart_stmt->bind_param("ii", $user_id, $product_id);
    $check_cart_stmt->execute();
    $result = $check_cart_stmt->get_result();

    if ($result->num_rows > 0) {
        $current_quantity = $result->fetch_assoc()['quantity'];
        $new_quantity = $current_quantity + $quantity;

        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    updateCartCount($conn, $user_id);
    echo "<script>alert('Produk berhasil ditambahkan ke keranjang.');</script>";
    header("Refresh:0");
}

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
    <title>Kaos Pria - KaJaTu Store</title>
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

        /* Enhanced Product Section */
        .product-section {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 1rem;
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
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

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 3rem 1rem;
            margin-top: 4rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .footer-content p {
            margin: 0.5rem 0;
            opacity: 0.9;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .product-list {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .product-list {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        /* Animations */
        @keyframes fadeIn {
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
            animation: fadeIn 0.8s ease-out;
        }

        h1 {
            text-align: center;
            color: var(--text-color);
            margin: 20px 0;
            animation: fadeIn 0.5s;
        }

        .banner {
            padding: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Center the content */
            max-width: 1200px;
            margin: 0 auto;
            /* Center the banner itself */
        }

        .images {
            display: flex;
            gap: 10px;
        }

        .images img {
            width: 1203px;
            height: 354px;
            border: 5px solid white;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .banner {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .images img {
                width: 100px;
                height: 150px;
            }
        }
    </style>
</head>

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
                    <a href="logout.php" class="btn logout-button">Logout</a>
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

    <div class="banner">
        <div class="images">
            <img alt="Deskripsi gambar pertama" src="../images/banner1.png" />
        </div>
    </div>

    <h1>Koleksi Kaos Pria</h1>

    <div class="product-list animate-fade-in">
        <?php while ($row = $products->fetch_assoc()) { ?>
            <div class="product">
                <a href="detail.php?id=<?php echo htmlspecialchars($row['id']); ?>">
                    <img src="../images/<?php echo htmlspecialchars($row['image']); ?>"
                        alt="<?php echo htmlspecialchars($row['name']); ?>">
                </a>
                <div class="product-info">
                    <h3 class="product-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p class="product-price">Rp<?php echo number_format($row['price'], 0, ',', '.'); ?></p>
                </div>
            </div>
        <?php } ?>
    </div>

    <footer>
        <div class="footer-content">
            <p>&copy; 2025 KaJaTu Store - Semua Hak Cipta Dilindungi</p>
            <p>Temukan gaya fashion terbaikmu bersama kami</p>
        </div>
    </footer>
</body>

</html>