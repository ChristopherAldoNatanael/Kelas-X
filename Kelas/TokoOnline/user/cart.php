<?php
session_start();
include '../db/connection.php';

// Cek apakah pengguna memiliki peran 'user'
if ($_SESSION['role'] != 'user') {
    header('Location: ../index.php');
    exit;
}

$username = $_SESSION['username'];
$user_id = $_SESSION['id'];

// Fetch user address from users table
$user_query = "SELECT alamat FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $user_address = $user_data['alamat'];
} else {
    $user_address = ''; // Default value if no address is found
}

// Query untuk mengambil data keranjang dengan size
$query = "SELECT c.cart_id, c.quantity, c.size, p.id as product_id, p.name, p.price 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Menggabungkan produk yang sama (dengan mempertimbangkan size)
$combined_cart = [];
while ($item = $result->fetch_assoc()) {
    // Menggunakan kombinasi product_id dan size sebagai key
    $key = $item['product_id'] . '_' . ($item['size'] ?? ''); // Memastikan size bisa kosong
    if (isset($combined_cart[$key])) {
        $combined_cart[$key]['quantity'] += $item['quantity'];
    } else {
        $combined_cart[$key] = $item;
    }
}

// Process deletion (dengan size)
if (isset($_POST['delete'])) {
    $delete_product_id = $_POST['product_id'];
    $delete_size = $_POST['size'];
    $delete_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ? AND size = ?");
    $delete_stmt->bind_param("iis", $user_id, $delete_product_id, $delete_size);
    if ($delete_stmt->execute()) {
        echo "<script>alert('Produk berhasil dihapus dari keranjang.'); window.location.href='cart.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus produk: " . htmlspecialchars($delete_stmt->error) . "');</script>";
    }
    $delete_stmt->close();
}

// Process quantity update (dengan size)
if (isset($_POST['update'])) {
    $update_product_id = $_POST['product_id'];
    $update_size = $_POST['size'];
    $new_quantity = $_POST['quantity'];

    if ($new_quantity > 0) {
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ? AND size = ?");
        $update_stmt->bind_param("iiis", $new_quantity, $user_id, $update_product_id, $update_size);
        if ($update_stmt->execute()) {
            echo "<script>alert('Jumlah produk berhasil diperbarui.'); window.location.href='cart.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat memperbarui produk: " . htmlspecialchars($update_stmt->error) . "');</script>";
        }
        $update_stmt->close();
    } else {
        echo "<script>alert('Jumlah harus lebih dari 0.');</script>";
    }
}

// Process checkout (dengan size)
if (isset($_POST['checkout'])) {
    if (empty($combined_cart)) {
        echo "<script>alert('Keranjang kosong! Harap tambahkan produk terlebih dahulu.'); window.location.href='cart.php';</script>";
        exit;
    }

    // Ambil data dari form checkout
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';

    // Validasi input
    if (empty($name) || empty($address) || empty($payment_method)) {
        echo "<script>alert('Semua field harus diisi!'); window.location.href='cart.php';</script>";
        exit;
    }

    // Menambahkan size ke dalam order
    foreach ($combined_cart as $item) {
        // Jika size tidak ada, biarkan NULL
        // ENUM di MySQL tidak bisa NULL jika tidak diatur sebelumnya.
        // Jadi kita akan menggunakan string kosong jika tidak ada ukuran.
        // Pastikan kolom `size` di tabel `orders` mengizinkan NULL jika ingin menyimpan NULL.
        
        if (empty($item['size'])) {
            // Jika tidak ada ukuran, gunakan string kosong untuk ENUM
            $size_to_insert = ''; 
        } else {
            // Jika ada ukuran, gunakan ukuran tersebut
            $size_to_insert = $item['size'];
        }

        // Siapkan query SQL untuk memasukkan order
        $query = "INSERT INTO orders (user_id, product_id, quantity, size, name, address, payment_method) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        // Siapkan statement
        if ($order_stmt = $conn->prepare($query)) {
            // Bind parameter termasuk size
            if (!$order_stmt->bind_param(
                "iiissss",
                $user_id,
                $item['product_id'],
                $item['quantity'],
                $size_to_insert,
                $name,
                $address,
                $payment_method
            )) {
                echo "<script>alert('Terjadi kesalahan saat mengikat parameter: " . htmlspecialchars($order_stmt->error) . "');</script>";
                continue; // Lanjutkan ke item berikutnya jika ada kesalahan
            }

            if (!$order_stmt->execute()) {
                echo "<script>alert('Terjadi kesalahan saat menyimpan order: " . htmlspecialchars($order_stmt->error) . "');</script>";
                break; // Hentikan jika terjadi kesalahan dalam eksekusi
            }
        } else {
            echo "<script>alert('Terjadi kesalahan saat mempersiapkan statement: " . htmlspecialchars($conn->error) . "');</script>";
            break; // Hentikan jika terjadi kesalahan dalam persiapan statement
        }
        
        // Hapus statement untuk order setelah eksekusi
        unset($order_stmt);
    }

    // Hapus keranjang setelah checkout berhasil
    if (!empty($combined_cart)) {
        // Hapus semua item di keranjang untuk user ini
        if ($clear_cart_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?")) {
            // Bind parameter untuk hapus keranjang
            if ($clear_cart_stmt->bind_param("i", $user_id)) {
                if ($clear_cart_stmt->execute()) {
                    echo "<script>alert('Checkout berhasil!'); window.location.href='cart.php';</script>";
                } else {
                    echo "<script>alert('Terjadi kesalahan saat menghapus keranjang: " . htmlspecialchars($clear_cart_stmt->error) . "');</script>";
                }
            }
            unset($clear_cart_stmt);
        }
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html>

<head>
    <title>KaJaTu Store - Cart</title>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }

        .nav-links a:hover {
            opacity: 0.8;
        }

        /* Cart Container Styles */
        .cart-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            flex-grow: 1;
        }

        .cart-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            width: 100%;
        }

        .cart-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-table th {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        .cart-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .quantity-input {
            width: 80px;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-right: 0.5rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .update-btn {
            background: var(--primary-color);
            color: white;
        }

        .delete-btn {
            background: var(--accent-color);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Cart Summary */
        .cart-summary {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .cart-total {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1rem;
        }

        .checkout-btn {
            background: var(--accent-color);
            color: white;
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
        }

        /* Checkout Form */
        .checkout-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            max-width: 600px;
            margin: 2rem auto;
            display: none;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 2rem;
            margin-top: auto;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .cart-table {
                display: block;
                overflow-x: auto;
            }

            .nav-content {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        /* Checkbox Styling */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
        }

        .custom-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid var(--primary-color);
            border-radius: 4px;
            cursor: pointer;
            position: relative;
        }

        .custom-checkbox input {
            opacity: 0;
            position: absolute;
        }

        .custom-checkbox input:checked+span {
            background: var(--primary-color);
        }

        .custom-checkbox span {
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
    </style>
</head>

<body>
    <nav>
        <div class="nav-content">
            <div class="logo">
                <a href="../index.php">
                    <i class="fas fa-store"></i>
                    KaJaTu Store - Shopping Cart
                </a>
            </div>
            <div class="nav-links">
                <span style="color: white;">Welcome, <?php echo htmlspecialchars($username); ?></span>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="cart-container">
        <?php if (empty($combined_cart)): ?>
            <div class="cart-summary">
                <p>Your cart is empty. Start shopping!</p>
                <a href="../index.php" class="btn checkout-btn">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="cart-table">
                <table>
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($combined_cart as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td>
                                    <label class="checkbox-wrapper">
                                        <span class="custom-checkbox">
                                            <input type="checkbox" class="product-checkbox"
                                                data-price="<?php echo $subtotal; ?>"
                                                data-product-id="<?php echo $item['product_id']; ?>"
                                                data-size="<?php echo $item['size']; ?>">
                                            <span></span>
                                        </span>
                                    </label>
                                </td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['size']); ?></td>
                                <td>Rp<?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                <td>
                                    <form action="" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                                        <input type="hidden" name="size" value="<?php echo htmlspecialchars($item['size']); ?>">
                                        <input type="number" class="quantity-input" name="quantity"
                                            value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                                        <button type="submit" name="update" class="btn update-btn">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>Rp<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                <td>
                                    <form action="" method="POST" style="display: inline;">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                                        <input type="hidden" name="size" value="<?php echo htmlspecialchars($item['size']); ?>">
                                        <button type="submit" name="delete" class="btn delete-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-summary">
                <div class="cart-total">Total: Rp<?php echo number_format($total, 0, ',', '.'); ?></div>
                <button id="checkout-button" class="btn checkout-btn">
                    <i class="fas fa-shopping-bag"></i> Proceed to Checkout
                </button>
            </div>

            <div id="checkout-form" class="checkout-form" style="display: none;">
                <h2>Checkout Details</h2>
                <form action="" method="POST">
                    <input type="hidden" name="selected_items" id="selected-items-input">

                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Delivery Address</label>
                        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user_address); ?>" required>
                    </div>


                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" name="payment_method">
                            <option value="COD">Cash on Delivery (COD)</option>
                        </select>
                    </div>

                    <div class="cart-total">Total Payment: Rp<span id="checkout-total">0</span></div>

                    <button type="submit" name="checkout" class="btn checkout-btn">
                        <i class="fas fa-check"></i> Confirm Order
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="footer-content">
            <p>&copy; 2025 KaJaTu Store - All Rights Reserved</p>
            <p>Find your perfect style with us</p>
        </div>
    </footer>

    <script>
        document.getElementById('checkout-button').addEventListener('click', function() {
            let checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
            let cartEmpty = <?php echo empty($combined_cart) ? 'true' : 'false'; ?>;

            if (cartEmpty) {
                alert('Your cart is empty! Please add products first.');
            } else if (checkedBoxes.length === 0) {
                alert('Please select at least one product to proceed to checkout.');
            } else {
                let checkoutForm = document.getElementById('checkout-form');
                checkoutForm.style.display = checkoutForm.style.display === 'none' || checkoutForm.style.display === '' ? 'block' : 'none';

                // Collect selected items data
                let selectedItems = Array.from(checkedBoxes).map(checkbox => ({
                    product_id: checkbox.getAttribute('data-product-id'),
                    size: checkbox.getAttribute('data-size'),
                    price: checkbox.getAttribute('data-price')
                }));

                document.getElementById('selected-items-input').value = JSON.stringify(selectedItems);
            }
        });

        document.querySelectorAll('.product-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                let total = 0;
                document.querySelectorAll('.product-checkbox:checked').forEach(function(checkedBox) {
                    total += parseFloat(checkedBox.getAttribute('data-price'));
                });
                document.querySelector('.cart-total').innerText = 'Total: Rp' + total.toLocaleString('id-ID');
                document.getElementById('checkout-total').innerText = total.toLocaleString('id-ID');
            });
        });
    </script>
</body>

</html>