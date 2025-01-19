
// session_start();
// include '../db/connection.php';

// Check if user is logged in
// $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
// $user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null; // Ensure user ID is set

// $products = $conn->query("SELECT * FROM products");

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $product_id = $_POST['product_id'];
//     $quantity = 1; // Default quantity

    // Prepare query to insert data into cart table
    // $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    // $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    // if ($stmt->execute()) {
    //     echo "<script>alert('Produk berhasil ditambahkan ke keranjang.');</script>";
        // header("location: cart.php");
//     } else {
//         echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "');</script>";
//     }
//     $stmt->close();
// }

// Cek apakah pengguna sudah login sebelum menambahkan ke keranjang
// if (!isset($_SESSION['username'])) {
//     header('Location: ../index.php'); // Arahkan ke halaman login
//     exit();
// }

// Logika untuk menambahkan produk ke keranjang
// if (isset($_POST['add_to_cart'])) {
//     $product_id = $_POST['product_id']; // Ambil ID produk dari form
    // Tambahkan logika untuk menyimpan produk ke keranjang di sini
// }


// $products = $conn->query("SELECT * FROM products");
// ?>

<!-- <!DOCTYPE html>
<html>

<head>
    <title>KaosKita - Home</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <style>
        /* Your existing styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #f3f3f3;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ff7e5f;
            padding: 1em;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
            font-family: poppins;
        }

        h1 {
            text-align: center;
        }

        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .product {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            margin: 10px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s;
        }

        .product:hover {
            transform: scale(1.05);
        }

        img {
            max-width: 300px;
            border-radius: 10px;
        }

        button {
            background: #ff5a5f;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #e04848;
        }

        footer {
            text-align: center;
            padding: 1em;
            background: #ff7e5f;
            color: white;
            margin-top: 20px;
        }

        .notification {
            display: none;
            background-color: #28a745;
            color: white;
            padding: 10px;
            margin-top: 20px;
            text-align: center;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <nav>
        <div class="logo">KaosKita</div>
        <div>
            <?php if ($user_id): ?>

                <a href="cart.php">Cart</a>
                <a href="#">Welcome, <?php echo htmlspecialchars($username); ?></a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="home.php">Home</a>
                <a href="index.php">Login</a>
            <?php endif; ?>
        </div>
    </nav>
    <h1 style="font-family:Lobster;">Produk KaosKita</h1> -->

    <!-- Notifikasi -->
    <!-- <div class="notification" id="notification">Produk telah ditambahkan ke dalam keranjang</div>

    <div class="product-list">
        <?php while ($row = $products->fetch_assoc()) { ?>
            <div class="product">
                <img src="../images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                <p><strong><?php echo htmlspecialchars($row['name']); ?></strong></p>
                <p>Harga: Rp<?php echo number_format($row['price'], 0, ',', '.'); ?></p>
                <form method="POST" action="">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                    <button type="submit" name="add_to_cart">Tambah ke Keranjang</button>
                </form>

            </div>
        <?php } ?>
    </div>

    <footer>
        &copy; 2025 KaosKita - Semua Hak Cipta Dilindungi
    </footer>

</body>

</html> -->