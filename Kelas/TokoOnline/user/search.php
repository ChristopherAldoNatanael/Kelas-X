<?php
session_start();
include '../db/connection.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';

// Check if user is logged in
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

// Prepare and execute search query
$search_stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
$search_query = "%" . $query . "%";
$search_stmt->bind_param("s", $search_query);
$search_stmt->execute();
$result = $search_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - KaJaTu Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
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

        .btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            /* Tiga kolom dalam satu baris */
            gap: 2rem;
            margin-top: 3rem;
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
            object-fit: cover;
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

        h1 {
            text-align: center;
            margin: 2rem 0;
            font-size: 2rem;
        }

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

        @media (max-width: 1024px) {
            .product-list {
                grid-template-columns: repeat(2, 1fr);
                /* Dua kolom pada layar menengah */
            }
        }

        @media (max-width: 768px) {
            .product-list {
                grid-template-columns: 1fr;
                /* Satu kolom pada layar kecil */
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


    <h1>Hasil Pencarian untuk: <?= htmlspecialchars($query) ?></h1>

    <div class="product-list">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="product">
                <a href="detail.php?id=<?php echo htmlspecialchars($row['id']); ?>">
                    <img src="../images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
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

<?php
$search_stmt->close();
$conn->close();
?>