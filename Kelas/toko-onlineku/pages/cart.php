<?php
session_start();
include("../includes/config.php");

// Initialize variables
$cart_items = [];
$total_belanja = 0;
$is_logged_in = isset($_SESSION['pelanggan_id']);
$alert = [];

// Handle cart actions for logged in users
if ($is_logged_in) {
    try {
        // Handle update cart
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_cart'])) {
            $produk_id = filter_var($_POST['produk_id'], FILTER_SANITIZE_NUMBER_INT);
            $quantity = filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT);

            if ($quantity >= 0) {
                // Check if product exists in cart
                $check = $koneksi->prepare("SELECT cart_id FROM cart WHERE pelanggan_id = ? AND produk_id = ?");
                $check->execute([$_SESSION['pelanggan_id'], $produk_id]);

                // Check product stock
                $stock_check = $koneksi->prepare("SELECT stok FROM produk WHERE produk_id = ?");
                $stock_check->execute([$produk_id]);
                $stock = $stock_check->fetchColumn();

                if ($quantity > $stock) {
                    $alert = ['type' => 'danger', 'message' => 'Requested quantity exceeds available stock!'];
                } else {
                    if ($check->rowCount() > 0) {
                        $stmt = $koneksi->prepare("UPDATE cart SET quantity = ? WHERE pelanggan_id = ? AND produk_id = ?");
                        $stmt->execute([$quantity, $_SESSION['pelanggan_id'], $produk_id]);
                    } else {
                        $stmt = $koneksi->prepare("INSERT INTO cart (pelanggan_id, produk_id, quantity) VALUES (?, ?, ?)");
                        $stmt->execute([$_SESSION['pelanggan_id'], $produk_id, $quantity]);
                    }
                    $alert = ['type' => 'success', 'message' => 'Cart updated successfully!'];
                }
            } else {
                $alert = ['type' => 'danger', 'message' => 'Invalid quantity provided.'];
            }
        }

        // Handle remove from cart
        if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['produk_id'])) {
            $produk_id = filter_var($_GET['produk_id'], FILTER_SANITIZE_NUMBER_INT);
            $stmt = $koneksi->prepare("DELETE FROM cart WHERE pelanggan_id = ? AND produk_id = ?");
            $stmt->execute([$_SESSION['pelanggan_id'], $produk_id]);
            $alert = ['type' => 'success', 'message' => 'Item removed from cart!'];
        }

        // Handle checkout process
        if (isset($_POST['checkout'])) {
            // Begin transaction
            $koneksi->beginTransaction();

            try {
                // Get cart items for order creation
                $cart_query = $koneksi->prepare("
            SELECT c.produk_id, c.quantity, p.harga, p.stok 
            FROM cart c 
            JOIN produk p ON c.produk_id = p.produk_id 
            WHERE c.pelanggan_id = ?
        ");
                $cart_query->execute([$_SESSION['pelanggan_id']]);
                $cart_items_checkout = $cart_query->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($cart_items_checkout)) {
                    // Validate shipping address
                    $shipping_address = filter_var($_POST['shipping_address'], FILTER_SANITIZE_STRING);
                    if (empty($shipping_address)) {
                        throw new Exception("Shipping address is required!");
                    }

                    // Validate payment method
                    $payment_method = filter_var($_POST['payment_method'], FILTER_SANITIZE_STRING);
                    if (empty($payment_method)) {
                        throw new Exception("Payment method is required!");
                    }

                    // Generate transaction ID
                    $transaction_id = 'TRX-' . date('YmdHis') . '-' . substr(uniqid(), -4);

                    // Calculate total amount and validate stock
                    $total_amount = 0;
                    foreach ($cart_items_checkout as $item) {
                        // Recheck current stock
                        $stock_check = $koneksi->prepare("SELECT stok FROM produk WHERE produk_id = ?");
                        $stock_check->execute([$item['produk_id']]);
                        $current_stock = $stock_check->fetchColumn();

                        if ($item['quantity'] > $current_stock) {
                            throw new Exception("Insufficient stock for some items! Please refresh your cart.");
                        }
                        $total_amount += $item['harga'] * $item['quantity'];
                    }

                    // Create order
                    $order_stmt = $koneksi->prepare("
                INSERT INTO orders (pelanggan_id, total_amount, shipping_address, payment_method, transaction_id, order_date) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
                    $order_stmt->execute([
                        $_SESSION['pelanggan_id'],
                        $total_amount,
                        $shipping_address,
                        $payment_method,
                        $transaction_id
                    ]);
                    $order_id = $koneksi->lastInsertId();

                    // Create order items and update stock
                    foreach ($cart_items_checkout as $item) {
                        // Insert order item
                        $order_item_stmt = $koneksi->prepare("
                    INSERT INTO order_items (order_id, produk_id, quantity, price_per_item) 
                    VALUES (?, ?, ?, ?)
                ");
                        $order_item_stmt->execute([
                            $order_id,
                            $item['produk_id'],
                            $item['quantity'],
                            $item['harga']
                        ]);

                        // Update stock
                        $update_stock = $koneksi->prepare("
                    UPDATE produk 
                    SET stok = stok - ? 
                    WHERE produk_id = ?
                ");
                        $update_stock->execute([$item['quantity'], $item['produk_id']]);
                    }

                    // Clear cart
                    $clear_cart = $koneksi->prepare("DELETE FROM cart WHERE pelanggan_id = ?");
                    $clear_cart->execute([$_SESSION['pelanggan_id']]);

                    $koneksi->commit();
                    $alert = ['type' => 'success', 'message' => 'Order placed successfully! Transaction ID: ' . $transaction_id];

                    // Redirect to order confirmation or thank you page
                    $_SESSION['alert'] = $alert;
                    header("Location: order-confirmation.php?order_id=" . $order_id);
                    exit();
                } else {
                    throw new Exception("Cart is empty!");
                }
            } catch (Exception $e) {
                $koneksi->rollBack();
                $alert = ['type' => 'danger', 'message' => 'Checkout failed: ' . $e->getMessage()];
            }
        }
        // Fetch current cart items
        $stmt = $koneksi->prepare("
            SELECT c.cart_id, c.produk_id, p.nama_produk, p.harga, p.gambar, p.stok, c.quantity 
            FROM cart c 
            JOIN produk p ON c.produk_id = p.produk_id 
            WHERE c.pelanggan_id = ?
        ");
        $stmt->execute([$_SESSION['pelanggan_id']]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate total
        foreach ($cart_items as $item) {
            $total_belanja += $item['harga'] * $item['quantity'];
        }
    } catch (PDOException $e) {
        $alert = ['type' => 'danger', 'message' => 'Database error occurred. Please try again later.'];
        error_log($e->getMessage());
    }
}

// Store alert in session for display after redirect
if (!empty($alert)) {
    $_SESSION['alert'] = $alert;
}

// Function to get cart count
function getCartCount($koneksi, $pelanggan_id)
{
    try {
        $sql = "SELECT SUM(quantity) as total FROM cart WHERE pelanggan_id = :pelanggan_id";
        $stmt = $koneksi->prepare($sql);
        $stmt->execute([':pelanggan_id' => $pelanggan_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        return 0;
    }
}

// Get cart count if user is logged in
$cartCount = 0;
if (isset($_SESSION['pelanggan_id'])) {
    $cartCount = getCartCount($koneksi, $_SESSION['pelanggan_id']);
}



/**
 * Get user email from database
 * 
 * @param string $username The username to look up
 * @return string The user's email or empty string if not found
 */
function getUserEmail($username)
{
    global $koneksi;

    try {
        $stmt = $koneksi->prepare("SELECT email FROM pelanggan WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['email'];
        }
    } catch (PDOException $e) {
        // Log error (in a production environment)
        // error_log("Database error: " . $e->getMessage());
    }

    return "";
}

/**
 * Get user address from database
 * 
 * @param string $username The username to look up
 * @return string The user's address or "No address provided" if not found
 */
function getUserAddress($username)
{
    global $koneksi;

    try {
        $stmt = $koneksi->prepare("SELECT alamat FROM pelanggan WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['alamat'] ?: 'No address provided';
        }
    } catch (PDOException $e) {
        // Log error (in a production environment)
        // error_log("Database error: " . $e->getMessage());
    }

    return "No address provided";
}

/**
 * Get user role from database
 * 
 * @param string $username The username to look up
 * @return string The user's role (admin or user) 
 */
function getUserRole($username)
{
    global $koneksi;

    try {
        $stmt = $koneksi->prepare("SELECT role FROM pelanggan WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['role'];
        }
    } catch (PDOException $e) {
        // Log error (in a production environment)
        // error_log("Database error: " . $e->getMessage());
    }

    return "user"; // Default to user if role can't be determined
}

/**
 * Get user registration date (implement this according to your database schema)
 * You'll need to add a registration_date column to your pelanggan table
 * 
 * @param string $username The username to look up
 * @return string Formatted registration date
 */
function getUserRegistrationDate($username)
{
    global $koneksi;

    try {
        $stmt = $koneksi->prepare("SELECT created_at FROM pelanggan WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($result['created_at'])) {
                return date('M Y', strtotime($result['created_at']));
            }
        }
    } catch (PDOException $e) {
        // Log error (in a production environment)
        // error_log("Database error: " . $e->getMessage());
    }

    return date('M Y'); // Fallback to current date if no record found
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EliteWatch - Shopping Cart</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #000000;
            --secondary-color: #1a1a1a;
            --accent-color: #D4AF37;
            --text-color: #ffffff;
            --primary-color: #000000;
            --secondary-color: #1a1a1a;
            --accent-color: #D4AF37;
            --text-color: #ffffff;
            --primary-color: #0a0a0a;
            --accent-color: #d4af37;
            --text-light: #ffffff;
            --text-dark: #1a1a1a;
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            --dark: #000000;
            --dark-lighter: #1a1a1a;
            --gold: #ffd700;
            --gold-light: #ffe44d;
            --text-light: #ffffff;
            --orange: #ffa500;
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: var(--primary-color);
            color: var(--text-light);
            font-family: 'Helvetica Neue', sans-serif;
        }

        /* Enhanced Navigation Styles */
        /* Enhanced Navigation Styles */
        .navbar {
            background: rgba(10, 10, 10, 0.95) !important;
            backdrop-filter: blur(20px);
            padding: 1rem 0;
            transition: all 0.4s ease;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar.scrolled {
            padding: 0.5rem 0;
            background: rgba(10, 10, 10, 0.98) !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: 2px;
            background: linear-gradient(135deg, var(--accent-color), #f8e7b3);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            padding: 0.5rem 0;
        }

        .navbar-brand::after {
            content: 'LUXURY TIMEPIECES';
            position: absolute;
            bottom: 0;
            left: 5px;
            font-size: 0.8rem;
            letter-spacing: 3px;
            color: var(--accent-color);
            opacity: 0.8;
            transform: translateY(100%);
        }

        .navbar-nav {
            margin-left: 2rem;
        }

        .nav-item {
            position: relative;
            margin: 0 0.5rem;
        }

        .nav-link {
            color: var(--text-light) !important;
            font-weight: 500;
            padding: 0.8rem 1.5rem !important;
            position: relative;
            transition: all 0.3s ease;
            font-size: 1rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }

        .nav-link:hover::before {
            transform: scale(1.1);
            border-color: var(--accent-color);
            opacity: 0.3;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--accent-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 70%;
        }

        /* Cart Icon Enhancement */
        .cart-icon {
            position: relative;
            padding-right: 2rem !important;
        }

        .cart-badge {
            position: absolute;
            top: 0;
            right: 1rem;
            background: var(--accent-color);
            color: var(--primary-color);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }

        .cart-icon:hover .cart-badge {
            transform: translateY(-5px) scale(1.1);
        }

        /* Search Bar Enhancement */
        .search-container {
            position: relative;
            margin-right: 1rem;
        }

        .search-form {
            display: flex;
            align-items: center;
        }

        .search-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 25px;
            padding: 0.5rem 1rem;
            color: var(--text-light);
            width: 200px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            width: 250px;
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-color);
            outline: none;
        }

        .search-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-color);
            transition: all 0.3s ease;
        }

        .search-container:hover .search-icon {
            transform: translateY(-50%) scale(1.1);
        }

        .auth-buttons {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 15px;
            background: linear-gradient(145deg, var(--dark), var(--dark-lighter));
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.8);
        }

        .auth-btn {
            display: inline-block;
            padding: 10px 25px;
            margin-left: 15px;
            text-decoration: none;
            color: var(--gold);
            background-color: rgba(255, 215, 0, 0.05);
            border: 2px solid var(--gold);
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .auth-btn:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: var(--gold);
            transform: translate(-50%, -50%);
            border-radius: 50%;
            transition: all 0.5s ease;
            z-index: -1;
        }

        .auth-btn:hover {
            color: var(--dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        }

        .auth-btn:hover:before {
            width: 300%;
            height: 300%;
        }

        .user-welcome {
            font-weight: 500;
            color: var(--gold-light);
            margin-right: 15px;
            cursor: pointer;
            position: relative;
            padding: 8px 20px;
            border-radius: 30px;
            transition: all 0.3s ease;
            background: rgba(255, 215, 0, 0.05);
        }

        .user-welcome:hover {
            background: rgba(255, 215, 0, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.2);
        }

        /* Profile Module */
        .profile-module {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            width: 280px;
            background: linear-gradient(135deg, var(--dark), var(--dark-lighter));
            margin-top: 20px;
            border-radius: 15px;
            border: 1px solid var(--gold);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.7),
                0 0 20px rgba(255, 215, 0, 0.2);
            z-index: 1000;
            overflow: hidden;
            transform: translateY(10px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .user-welcome-container {
            position: relative;
        }

        .profile-module.active,
        .user-welcome-container:hover .profile-module {
            display: block;
            transform: translateY(0);
            opacity: 1;
        }

        .profile-header {
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.9), rgba(26, 26, 26, 0.9));
        }

        .profile-pic {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin: 0 auto 15px;
            border: 3px solid var(--gold);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
            background-size: cover;
            background-position: center;
            background-color: var(--dark);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .profile-pic:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.4);
        }

        .profile-pic-icon {
            font-size: 40px;
            color: var(--orange);
        }

        .edit-pic {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 5px;
            background-color: rgba(0, 0, 0, 0.7);
            color: var(--gold);
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .profile-pic:hover .edit-pic {
            opacity: 1;
        }

        .username {
            font-size: 18px;
            font-weight: 600;
            color: var(--gold);
            margin-bottom: 5px;
        }

        .user-role {
            font-size: 14px;
            color: var(--gold-light);
            opacity: 0.8;
        }

        .profile-body {
            padding: 20px;
        }

        .profile-info {
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .info-icon {
            width: 20px;
            color: var(--gold);
            margin-right: 10px;
            font-size: 16px;
        }

        .info-text {
            flex: 1;
            font-size: 14px;
            color: var(--text-light);
            word-break: break-word;
        }

        .profile-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }


        .profile-action-btn {
            padding: 10px 0;
            text-align: center;
            border-radius: 8px;
            background: rgba(255, 215, 0, 0.05);
            color: var(--gold);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .profile-action-btn:hover {
            background: rgba(255, 215, 0, 0.15);
            border-color: var(--gold);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.2);
        }

        /* Gold accent elements */
        .gold-accent {
            position: absolute;
            background: radial-gradient(circle, var(--gold) 0%, transparent 70%);
            opacity: 0.1;
            pointer-events: none;
        }

        .accent-1 {
            width: 150px;
            height: 150px;
            top: -75px;
            right: -75px;
        }

        .accent-2 {
            width: 100px;
            height: 100px;
            bottom: -50px;
            left: -50px;
        }

        /* Additional styling for badges */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            margin-left: 8px;
            background: linear-gradient(135deg, var(--gold), var(--orange));
            color: var(--dark);
            box-shadow: 0 2px 5px rgba(255, 215, 0, 0.3);
        }

        .gold-accent {
            position: absolute;
            background: radial-gradient(circle, var(--gold) 0%, transparent 70%);
            opacity: 0.05;
            pointer-events: none;
            transition: all 0.5s ease;
        }

        .profile-module:hover .gold-accent {
            opacity: 0.08;
            transform: scale(1.1);
        }

        .auth-btn.login-btn {
            background: transparent;
            border-color: var(--accent-color);
            padding: 0.4rem 0.8rem;
        }

        .auth-btn.register-btn {
            background: var(--accent-color);
            color: var(--primary-color);
            padding: 0.4rem 0.8rem;
        }

        .auth-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
        }

        .user-welcome {
            color: var(--accent-color);
            margin-right: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            white-space: nowrap;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Adjust search container for better spacing */
        .search-container {
            position: relative;
            margin-right: 0.5rem;
        }

        .search-input {
            width: 180px;
        }

        .search-input:focus {
            width: 220px;
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .auth-buttons {
                margin: 0.5rem 0;
                width: auto;
            }

            .auth-btn {
                padding: 0.4rem 0.8rem;
                width: auto;
            }

            .navbar-nav {
                margin-left: 0;
            }

            .search-container {
                margin: 0.5rem 0;
            }

            .search-input,
            .search-input:focus {
                width: 100%;
            }
        }

        @media (min-width: 992px) and (max-width: 1200px) {
            .nav-link {
                padding: 0.8rem 1rem !important;
            }

            .search-input {
                width: 150px;
            }

            .search-input:focus {
                width: 180px;
            }
        }

        .cart-container {
            background: linear-gradient(145deg, #1a1a1a, #0a0a0a);
            border-radius: 30px;
            padding: 3rem;
            margin: 2rem auto;
            border: 1px solid rgba(212, 175, 55, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            margin-top: 140px;
        }

        .cart-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
            opacity: 0.3;
        }

        .cart-header {
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            padding-bottom: 2rem;
            margin-bottom: 3rem;
        }

        .cart-header h2 {
            color: var(--accent-color);
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: 2px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .product-card {
            background: linear-gradient(165deg, rgba(26, 26, 26, 0.9) 0%, rgba(10, 10, 10, 0.95) 100%);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            display: flex;
            gap: 2.5rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-color);
        }

        .product-card:hover::before {
            opacity: 1;
        }

        .product-image-wrapper {
            position: relative;
            width: 200px;
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-hover-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(212, 175, 55, 0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.08);
        }

        .product-card:hover .product-hover-overlay {
            opacity: 1;
        }

        .product-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-name {
            color: var(--accent-color);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        .product-meta {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .product-price {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .quantity-wrapper {
            margin-bottom: 1.5rem;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 0.3rem;
            width: fit-content;
        }

        .quantity-btn {
            background: none;
            border: none;
            color: var(--accent-color);
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background: var(--accent-color);
            color: var(--primary-color);
            border-radius: 6px;
        }

        .quantity-input {
            background: none;
            border: none;
            color: var(--text-light);
            width: 50px;
            text-align: center;
            font-size: 1.1rem;
        }

        .btn-update,
        .btn-remove {
            background: none;
            border: 1px solid var(--accent-color);
            color: var(--accent-color);
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-update:hover {
            background: var(--accent-color);
            color: var(--primary-color);
        }

        .btn-remove {
            border-color: #e74c3c;
            color: #e74c3c;
        }

        .btn-remove:hover {
            background: #e74c3c;
            color: var(--text-light);
        }

        .stock-indicator {
            margin-top: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            padding: 0.8rem;
            position: relative;
            overflow: hidden;
        }

        .stock-bar {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: linear-gradient(90deg, rgba(231, 76, 60, 0.2), rgba(231, 76, 60, 0.1));
            z-index: 1;
        }

        .stock-text {
            position: relative;
            z-index: 2;
            color: #e74c3c;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .cart-summary {
            background: linear-gradient(165deg, rgba(26, 26, 26, 0.9) 0%, rgba(10, 10, 10, 0.95) 100%);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 15px;
            padding: 2rem;
            position: sticky;
            top: 2rem;
        }

        .summary-title {
            color: var(--accent-color);
            font-size: 1.8rem;
            margin-bottom: 2rem;
            font-weight: 600;
            text-align: center;
            letter-spacing: 1px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid var(--accent-color);
            font-size: 1.2rem;
            font-weight: 600;
        }

        .total-price {
            color: var(--accent-color);
            font-size: 1.5rem;
        }

        .address-selection-container {
            background: rgba(26, 26, 26, 0.9);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(212, 175, 55, 0.1);
            margin-bottom: 2rem;
        }

        .address-section-title {
            color: var(--accent-color);
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .address-type-toggle {
            display: flex;
            justify-content: center;
        }

        .btn-outline-gold {
            color: var(--accent-color);
            border: 1px solid var(--accent-color);
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            background: transparent;
        }

        .btn-outline-gold:hover,
        .btn-check:checked+.btn-outline-gold {
            background: var(--accent-color);
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
        }

        .saved-address-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 10px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .saved-address-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.1);
            border-color: var(--accent-color);
        }

        .address-card-content {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .address-icon {
            color: var(--accent-color);
            font-size: 1.5rem;
            margin-top: 0.2rem;
        }

        .address-text {
            color: var(--text-color);
            font-size: 1rem;
            line-height: 1.6;
            flex: 1;
        }

        .luxury-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.1);
            color: var(--text-color);
            padding: 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .luxury-input:focus {
            outline: none;
            border-color: var(--accent-color);
            color: #ffffff;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.1);
            background: rgba(255, 255, 255, 0.08);
        }

        .form-text {
            color: rgba(255, 255, 255, 0.6);
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .address-selection-container {
                padding: 1.5rem;
            }

            .btn-outline-gold {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }

        .btn-checkout {
            background: var(--accent-color);
            color: var(--primary-color);
            border: none;
            width: 100%;
            padding: 1.2rem;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 2rem;
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
        }

        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .secure-badge i {
            color: var(--accent-color);
        }

        .cart-empty {
            text-align: center;
            padding: 4rem 2rem;
        }

        .cart-empty i {
            font-size: 5rem;
            color: var(--accent-color);
            margin-bottom: 2rem;
            animation: floatAnimation 3s ease-in-out infinite;
        }

        .cart-empty h3 {
            color: var(--accent-color);
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .cart-empty p {
            color: var(--text-light);
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.8;
        }

        .auth-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
        }

        .auth-btn {
            padding: 0.4rem 1rem;
            border: 1px solid var(--accent-color);
            border-radius: 20px;
            color: var(--text-light);
            background: transparent;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            white-space: nowrap;
        }

        .auth-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
        }

        @keyframes floatAnimation {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        /* Alert Styling */
        .alert {
            background: linear-gradient(145deg, #1a1a1a, #0a0a0a);
            border: 1px solid var(--accent-color);
            color: var(--text-light);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Stock Warning */
        .stock-warning {
            color: var(--warning-color);
            background: rgba(241, 196, 15, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }

            100% {
                opacity: 1;
            }
        }

        /* Enhanced Footer Styles */
        footer {
            background: linear-gradient(to bottom, #0a0a0a, #000000);
            padding: 5rem 0 2rem;
            margin-top: 6rem;
            position: relative;
            border-top: 1px solid rgba(212, 175, 55, 0.1);
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
            opacity: 0.3;
        }

        .footer-content {
            position: relative;
            z-index: 1;
        }

        .footer-title {
            color: var(--accent-color);
            font-size: 1.5rem;
            margin-bottom: 2rem;
            position: relative;
            display: inline-block;
            padding-bottom: 0.5rem;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--accent-color);
            transition: width 0.3s ease;
        }

        .footer-section:hover .footer-title::after {
            width: 100%;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .link-group {
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .link-group a {
            color: var(--text-light);
            text-decoration: none;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
            padding-left: 0;
        }

        .link-group:hover {
            transform: translateX(10px);
        }

        .link-group:hover a {
            color: var(--accent-color);
        }

        .link-group i {
            color: var(--accent-color);
            font-size: 1.2rem;
            transition: all 0.3s ease;
            opacity: 0.7;
        }

        .link-group:hover i {
            opacity: 1;
            transform: scale(1.2);
        }

        /* Enhanced Payment Methods Section */
        .payment-methods {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .payment-icon {
            width: 60px;
            height: 40px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 0.5rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }

        .payment-icon:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-color);
        }

        .payment-icon img {
            width: 100%;
            height: auto;
            transition: all 0.3s ease;
        }

        .payment-icon:hover img {
            filter: brightness(1.2);
        }

        /* Enhanced Social Icons */
        .social-icons {
            display: flex;
            gap: 1.2rem;
            margin-top: 2rem;
            justify-content: flex-start;
        }

        .social-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color);
            transition: all 0.4s ease;
            border: 1px solid rgba(212, 175, 55, 0.1);
            position: relative;
            overflow: hidden;
        }

        .social-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: var(--accent-color);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.4s ease;
            z-index: 0;
        }

        .social-icon i {
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .social-icon:hover::before {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        .social-icon:hover {
            transform: translateY(-5px) rotate(360deg);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2);
        }

        .social-icon:hover i {
            color: var(--primary-color);
        }

        /* Copyright Section */
        .footer-bottom {
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            position: relative;
        }

        .footer-bottom::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
        }

        .footer-bottom p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin: 0;
            transition: all 0.3s ease;
        }

        .footer-bottom:hover p {
            color: var(--text-light);
        }

        /* Newsletter Section (New Addition) */
        .newsletter-form {
            position: relative;
            margin-top: 2rem;
        }

        .newsletter-input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 8px;
            color: var(--text-light);
            transition: all 0.3s ease;
        }

        .newsletter-input:focus {
            outline: none;
            border-color: var(--accent-color);
            background: rgba(255, 255, 255, 0.1);
        }

        .newsletter-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--accent-color);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .newsletter-btn:hover {
            background: #e5c158;
            transform: translateY(-50%) scale(1.05);
        }

        .col-md-4 {
            display: flex;
            flex-direction: column;
            align-items: center;
        }


        /* Add smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Add custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--primary-color);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #b38f28;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">EliteWatch</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link cart-icon" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-badge"><?php echo $cartCount; ?></span>
                        </a>
                    </li>
                </ul>

                <div class="search-container">
                    <form class="search-form" method="GET" action="products.php">
                        <input type="search" name="search" class="search-input"
                            placeholder="Search timepieces..."
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" style="background: none; border: none;">
                            <i class="fas fa-search search-icon"></i>
                        </button>
                    </form>
                </div>

                <div class="auth-buttons">
                    <?php if (isset($_SESSION['username'])): ?>
                        <div class="user-welcome-container">
                            <span class="user-welcome" id="profile-toggle">
                                <i class="fas fa-user-circle" style="color: var(--orange);"></i>
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                                <i class="fas fa-chevron-down" style="font-size: 0.8em; margin-left: 5px;"></i>
                            </span>

                            <div class="profile-module" id="profile-module">
                                <div class="profile-header">
                                    <div class="profile-pic">
                                        <div class="profile-pic-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <!-- <span class="edit-pic">Change photo</span> -->
                                    </div>
                                    <div class="username">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></div>
                                    <?php
                                    $userRole = getUserRole($_SESSION['username']);
                                    ?>
                                    <div class="user-role">
                                        <?php echo ($userRole == 'admin') ? 'Administrator' : 'User'; ?>
                                        <?php if ($userRole == 'admin'): ?>
                                            <span class="badge">VIP</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="profile-body">
                                    <div class="profile-info">
                                        <div class="info-item">
                                            <div class="info-icon"><i class="fas fa-envelope"></i></div>
                                            <div class="info-text">
                                                <?php echo htmlspecialchars(getUserEmail($_SESSION['username'])); ?>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div class="info-text">
                                                <?php echo htmlspecialchars(getUserAddress($_SESSION['username'])); ?>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <div class="info-icon"><i class="fas fa-user-tag"></i></div>
                                            <div class="info-text">
                                                Member since: <?php echo getUserRegistrationDate($_SESSION['username']); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="profile-actions">
                                        <a href="profile.php" class="profile-action-btn">
                                            <i class="fas fa-user-edit"></i> Edit Profile
                                        </a>
                                        <?php if ($userRole == 'admin'): ?>
                                            <a href="admin/dashboard.php" class="profile-action-btn">
                                                <i class="fas fa-tachometer-alt"></i> Dashboard
                                            </a>
                                        <?php else: ?>
                                            <a href="history.php" class="profile-action-btn">
                                                <i class="fas fa-shopping-bag"></i> My Orders
                                            </a>
                                        <?php endif; ?>
                                        <a href="settings.php" class="profile-action-btn">
                                            <i class="fas fa-cog"></i> Settings
                                        </a>
                                        <a href="../logout.php" class="profile-action-btn">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </a>
                                    </div>
                                </div>

                                <!-- Gold accent elements -->
                                <div class="gold-accent accent-1"></div>
                                <div class="gold-accent accent-2"></div>
                            </div>
                        </div>
                        <!-- <a href="logout.php" class="auth-btn">Logout</a> -->
                    <?php else: ?>
                        <a href="pages/login.php" class="auth-btn">Login</a>
                        <a href="pages/register.php" class="auth-btn">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="cart-container">
            <div class="cart-header">
                <h2><i class="fas fa-shopping-cart"></i>Your EliteWatch Cart</h2>
            </div>

            <?php if (isset($_SESSION['alert'])): ?>
                <div class="alert alert-<?= $_SESSION['alert']['type'] ?> alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['alert']['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>

            <?php if (!$is_logged_in): ?>
                <div class="cart-empty">
                    <i class="fas fa-user-clock"></i>
                    <h3>Welcome to EliteWatch</h3>
                    <p>Please sign in to access your curated collection</p>
                    <div class="auth-buttons">
                        <a href="login.php" class="auth-btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </a>
                        <a href="register.php" class="auth-btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>Join Us
                        </a>
                    </div>
                </div>
            <?php elseif (empty($cart_items)): ?>
                <div class="cart-empty">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your collection is empty</h3>
                    <p>Start shopping for quality watches</p>
                    <a href="products.php" class="btn btn-primary auth-btn">
                        <i class="fas fa-compass me-2"></i>Discover Our Collection
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-8">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="product-card">
                                <div class="product-image-wrapper">
                                    <img src="../images/<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" class="product-image">
                                    <div class="product-hover-overlay"></div>
                                </div>

                                <div class="product-details">
                                    <div class="product-info">
                                        <h5 class="product-name"><?= htmlspecialchars($item['nama_produk']) ?></h5>
                                        <div class="product-meta">
                                            <span class="product-ref">Ref. <?= substr(md5($item['produk_id']), 0, 8) ?></span>
                                            <span class="product-serial">Serial: <?= strtoupper(substr(md5($item['produk_id']), 8, 8)) ?></span>
                                        </div>
                                        <div class="product-price">Rp.<?= number_format($item['harga']) ?></div>
                                    </div>

                                    <div class="product-controls">
                                        <div class="quantity-wrapper">
                                            <form method="post" class="quantity-form">
                                                <input type="hidden" name="produk_id" value="<?= htmlspecialchars($item['produk_id']) ?>">
                                                <div class="quantity-control">
                                                    <button type="button" class="quantity-btn minus">−</button>
                                                    <input type="number" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>"
                                                        min="1" max="<?= htmlspecialchars($item['stok']) ?>" class="quantity-input"
                                                        readonly>
                                                    <button type="button" class="quantity-btn plus">+</button>
                                                </div>
                                                <button type="submit" name="update_cart" class="btn-update">
                                                    <span class="btn-text">Update</span>
                                                    <span class="btn-icon"><i class="fas fa-sync-alt"></i></span>
                                                </button>
                                            </form>
                                        </div>

                                        <a href="cart.php?action=remove&produk_id=<?= htmlspecialchars($item['produk_id']) ?>"
                                            class="btn-remove" onclick="return confirm('Remove this timepiece from your collection?')">
                                            <span class="btn-text">Remove</span>
                                            <span class="btn-icon"><i class="fas fa-times"></i></span>
                                        </a>
                                    </div>

                                    <?php if ($item['stok'] < 5): ?>
                                        <div class="stock-indicator">
                                            <div class="stock-bar" style="width: <?= ($item['stok'] / 10) * 100 ?>%"></div>
                                            <span class="stock-text">Limited Availability: <?= $item['stok'] ?> pieces remaining</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="col-md-4">
                        <div class="cart-summary">
                            <h4 class="summary-title">Collection Summary</h4>

                            <div class="summary-details">
                                <div class="summary-item">
                                    <span>Pieces Selected</span>
                                    <span><?= count($cart_items) ?></span>
                                </div>
                                <div class="summary-item">
                                    <span>Subtotal</span>
                                    <span class="price">Rp.<?= number_format($total_belanja) ?></span>
                                </div>
                                <div class="summary-item">
                                    <span>Insurance</span>
                                    <span>Included</span>
                                </div>
                                <div class="summary-item">
                                    <span>Shipping</span>
                                    <span>Complimentary</span>
                                </div>
                                <div class="summary-total">
                                    <span>Total Investment</span>
                                    <span class="total-price">Rp.<?= number_format($total_belanja) ?></span>
                                </div>
                            </div>

                            <form method="post" class="checkout-form">
                                <div class="address-selection-container">
                                    <h5 class="address-section-title">Delivery Address</h5>

                                    <!-- Address Type Selection -->
                                    <div class="address-type-toggle mb-4">
                                        <div class="btn-group address-toggle" role="group" aria-label="Address Selection Type">
                                            <input type="radio" class="btn-check" name="address_type" id="saved_address" value="saved" checked>
                                            <label class="btn btn-outline-gold" for="saved_address">
                                                <i class="fas fa-map-marker-alt me-2"></i>Saved Address
                                            </label>

                                            <input type="radio" class="btn-check" name="address_type" id="new_address" value="new">
                                            <label class="btn btn-outline-gold" for="new_address">
                                                <i class="fas fa-plus me-2"></i>New Address
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Saved Address Section -->
                                    <div id="saved_address_section" class="address-section">
                                        <?php
                                        try {
                                            $pelanggan_id = $_SESSION['pelanggan_id'];
                                            $query = "SELECT alamat FROM pelanggan WHERE pelanggan_id = :pelanggan_id";
                                            $stmt = $koneksi->prepare($query);
                                            $stmt->execute([':pelanggan_id' => $pelanggan_id]);
                                            $saved_address = $stmt->fetchColumn();
                                        } catch (PDOException $e) {
                                            $saved_address = '';
                                        }
                                        ?>
                                        <div class="saved-address-card">
                                            <div class="address-card-content">
                                                <i class="fas fa-home address-icon"></i>
                                                <div class="address-text">
                                                    <?php echo htmlspecialchars($saved_address); ?>
                                                </div>
                                            </div>
                                            <input type="hidden" id="saved_address_input" value="<?php echo htmlspecialchars($saved_address); ?>">
                                        </div>
                                    </div>

                                    <!-- New Address Section -->
                                    <div id="new_address_section" class="address-section" style="display: none;">
                                        <div class="form-group">
                                            <textarea class="form-control luxury-input" id="shipping_address" name="shipping_address" rows="3" placeholder="Enter your new delivery address..."><?php echo htmlspecialchars($saved_address); ?></textarea>
                                            <small class="form-text text-muted">Please provide complete address details including street name, building number, and postal code.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <select class="form-control luxury-input" id="payment_method" name="payment_method" required>
                                        <option value="visa">Visa</option>
                                        <option value="master-card">Master Card</option>
                                        <option value="paypal">Paypal</option>
                                    </select>
                                </div>
                                <button type="submit" name="checkout" class="btn-checkout">
                                    <span class="btn-text">Complete Acquisition</span>
                                    <span class="btn-icon"><i class="fas fa-lock"></i></span>
                                </button>
                            </form>

                            <div class="summary-footer">
                                <div class="secure-badge">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>Secured & Insured Transaction</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>

                <footer>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-4">
                                <h5 class="footer-title">Quick Links</h5>
                                <div class="footer-links">
                                    <div class="link-group">
                                        <i class="fas fa-home"></i>
                                        <a href="index.php">Home</a>
                                    </div>
                                    <div class="link-group">
                                        <i class="fas fa-store"></i>
                                        <a href="pages/products.php">Products</a>
                                    </div>
                                    <div class="link-group">
                                        <i class="fas fa-shopping-cart"></i>
                                        <a href="pages/cart.php">Cart</a>
                                    </div>
                                    <div class="link-group">
                                        <i class="fas fa-info-circle"></i>
                                        <a href="pages/about.php">About</a>
                                    </div>
                                    <div class="link-group">
                                        <i class="fas fa-envelope"></i>
                                        <a href="pages/contact.php">Contact</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <h5 class="footer-title mb-4">Payment Methods</h5>
                                <div class="payment-methods">
                                    <div class="payment-icon">
                                        <img src="https://raw.githubusercontent.com/aaronfagan/svg-credit-card-payment-icons/main/flat-rounded/visa.svg"
                                            alt="Visa">
                                    </div>
                                    <div class="payment-icon">
                                        <img src="https://raw.githubusercontent.com/aaronfagan/svg-credit-card-payment-icons/main/flat-rounded/mastercard.svg"
                                            alt="Mastercard">
                                    </div>
                                    <div class="payment-icon">
                                        <img src="https://raw.githubusercontent.com/aaronfagan/svg-credit-card-payment-icons/main/flat-rounded/paypal.svg"
                                            alt="PayPal">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h5 class="footer-title">Connect With Us</h5>
                                <div class="social-icons">
                                    <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="social-icon"><i class="fab fa-pinterest"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <p>&copy; <?php echo date("Y"); ?> EliteWatch. All rights reserved.</p>
                        </div>
                    </div>
                </footer>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Quantity controls
                        document.querySelectorAll('.quantity-btn').forEach(button => {
                            button.addEventListener('click', function() {
                                const input = this.parentElement.querySelector('.quantity-input');
                                const currentValue = parseInt(input.value);
                                const max = parseInt(input.getAttribute('max'));
                                const min = parseInt(input.getAttribute('min')) || 1;

                                if (this.classList.contains('plus') && currentValue < max) {
                                    input.value = currentValue + 1;
                                } else if (this.classList.contains('minus') && currentValue > min) {
                                    input.value = currentValue - 1;
                                }

                                // Trigger change event for form handling
                                const changeEvent = new Event('change', {
                                    bubbles: true,
                                    cancelable: true
                                });
                                input.dispatchEvent(changeEvent);

                                // Optional: Update any price calculations
                                updateTotalPrice(input);
                            });
                        });

                        // Handle direct input changes
                        document.querySelectorAll('.quantity-input').forEach(input => {
                            input.addEventListener('change', function() {
                                const value = parseInt(this.value);
                                const max = parseInt(this.getAttribute('max'));
                                const min = parseInt(this.getAttribute('min')) || 1;

                                // Enforce min/max bounds
                                if (value > max) {
                                    this.value = max;
                                } else if (value < min || isNaN(value)) {
                                    this.value = min;
                                }

                                // Optional: Update any price calculations
                                updateTotalPrice(this);
                            });
                        });

                        // Optional: Function to update total price
                        function updateTotalPrice(input) {
                            const itemPrice = parseFloat(input.getAttribute('data-price') || 0);
                            const quantity = parseInt(input.value);
                            const totalPriceElement = input.closest('.item').querySelector('.total-price');

                            if (totalPriceElement && !isNaN(itemPrice) && !isNaN(quantity)) {
                                const total = (itemPrice * quantity).toFixed(2);
                                totalPriceElement.textContent = `$${total}`;
                            }
                        }
                    });

                    document.addEventListener('DOMContentLoaded', function() {
                        const savedAddressSection = document.getElementById('saved_address_section');
                        const newAddressSection = document.getElementById('new_address_section');
                        const savedAddressInput = document.getElementById('saved_address_input');
                        const shippingAddressTextarea = document.getElementById('shipping_address');

                        // Handle address type toggle
                        document.querySelectorAll('input[name="address_type"]').forEach(radio => {
                            radio.addEventListener('change', function() {
                                if (this.value === 'saved') {
                                    savedAddressSection.style.display = 'block';
                                    newAddressSection.style.display = 'none';
                                    shippingAddressTextarea.value = savedAddressInput.value;
                                } else {
                                    savedAddressSection.style.display = 'none';
                                    newAddressSection.style.display = 'block';
                                    shippingAddressTextarea.value = '';
                                }
                            });
                        });

                        // Animate saved address card on hover
                        const savedAddressCard = document.querySelector('.saved-address-card');
                        savedAddressCard.addEventListener('mouseover', function() {
                            this.style.transform = 'translateY(-3px)';
                        });
                        savedAddressCard.addEventListener('mouseout', function() {
                            this.style.transform = 'translateY(0)';
                        });
                    });

                    const scrollBtn = document.getElementById('scrollBtn');
                    let isScrolling = false;
                    let scrollTimeout;

                    // Tampilkan tombol saat scroll
                    window.addEventListener('scroll', () => {
                        if (window.scrollY > 200) {
                            scrollBtn.classList.add('visible');
                        } else {
                            scrollBtn.classList.remove('visible');
                        }

                        // Tambahkan efek saat scrolling
                        scrollBtn.classList.add('scrolling');
                        clearTimeout(scrollTimeout);

                        scrollTimeout = setTimeout(() => {
                            scrollBtn.classList.remove('scrolling');
                        }, 150);
                    });

                    // Scroll ke atas saat tombol diklik
                    scrollBtn.addEventListener('click', () => {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    });


                    document.addEventListener('DOMContentLoaded', function() {
                        const profileToggle = document.getElementById('profile-toggle');
                        const profileModule = document.getElementById('profile-module');

                        if (profileToggle && profileModule) {
                            // Toggle profile module on click
                            profileToggle.addEventListener('click', function(e) {
                                e.stopPropagation(); // Prevent click from bubbling to document
                                profileModule.classList.toggle('active');
                            });

                            // Close profile module when clicking outside
                            document.addEventListener('click', function(e) {
                                // Check if click is outside the profile module and toggle
                                if (!profileModule.contains(e.target) && e.target !== profileToggle) {
                                    profileModule.classList.remove('active');
                                }
                            });

                            // Prevent clicks inside profile module from closing it
                            profileModule.addEventListener('click', function(e) {
                                e.stopPropagation(); // Prevent click from bubbling to document
                            });
                        }
                    });
                </script>
</body>

</html>