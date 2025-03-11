<?php
session_start();
include("../includes/config.php");

// Fungsi untuk menampilkan pesan error atau sukses
function displayMessage($message, $type = 'error')
{
    echo '<div class="alert alert-' . $type . '">' . htmlspecialchars($message) . '</div>';
}

// Default query untuk menampilkan semua produk
$sql = "SELECT * FROM produk";
$params = array();
$whereConditions = array();

// Handle pencarian
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = trim($_GET['search']);
    $whereConditions[] = "(nama_produk LIKE ? OR brand LIKE ?)";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
}

// Handle filter brand
if (isset($_GET['brand']) && !empty($_GET['brand'])) {
    $whereConditions[] = "brand = ?";
    $params[] = $_GET['brand'];
}

// Gabungkan where conditions jika ada
if (!empty($whereConditions)) {
    $sql .= " WHERE " . implode(" AND ", $whereConditions);
}

// Handle sorting parameter
$sort = $_GET['sort'] ?? '';

// Handle sorting
if (isset($_GET['sort']) && !empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc':
            $sql .= " ORDER BY harga ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY harga DESC";
            break;
        default:
            $sql .= " ORDER BY produk_id DESC";
    }
} else {
    $sql .= " ORDER BY produk_id DESC";
}

// Handle add to cart action
// Handle add to cart action
if (isset($_GET['action']) && $_GET['action'] === 'add_to_cart') {
    if (!isset($_SESSION['username'])) {
        // Redirect ke halaman login dengan pesan
        header('Location: login.php?redirect=' . $_SERVER['PHP_SELF'] . '&produk_id=' . $_GET['produk_id']);
        exit;
    }

    // If user is logged in, proceed with adding to cart
    if (isset($_GET['produk_id'])) {
        $produk_id = $_GET['produk_id'];
        $pelanggan_id = $_SESSION['pelanggan_id'];

        try {
            // Check if product already exists in cart
            $check_sql = "SELECT cart_id, quantity FROM cart WHERE pelanggan_id = ? AND produk_id = ?";
            $check_stmt = $koneksi->prepare($check_sql);
            $check_stmt->execute([$pelanggan_id, $produk_id]);
            $existing_cart = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_cart) {
                // Update quantity if product exists
                $update_sql = "UPDATE cart SET quantity = quantity + 1 WHERE cart_id = ?";
                $update_stmt = $koneksi->prepare($update_sql);
                $update_stmt->execute([$existing_cart['cart_id']]);
            } else {
                // Insert new cart item if product doesn't exist
                $insert_sql = "INSERT INTO cart (pelanggan_id, produk_id, quantity) VALUES (?, ?, 1)";
                $insert_stmt = $koneksi->prepare($insert_sql);
                $insert_stmt->execute([$pelanggan_id, $produk_id]);
            }

            // Redirect back with success message
            header('Location: ' . $_SERVER['PHP_SELF'] . '?action=add_to_cart&success=true');
            exit;
        } catch (PDOException $e) {
            displayMessage("Error adding to cart: " . $e->getMessage());
        }
    }
}

try {
    $stmt = $koneksi->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $result = array();
}

// Function untuk mendapatkan jumlah item di cart
function getCartCount($koneksi, $pelanggan_id)
{
    try {
        $sql = "SELECT SUM(quantity) as total FROM cart WHERE pelanggan_id = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->execute([$pelanggan_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        return 0;
    }
}

// Get cart count
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
    <title>EliteWatch - Luxury Timepieces</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
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
        }

        body {
            background-color: var(--primary-color);
            color: var(--text-color);
            font-family: 'Helvetica Neue', sans-serif;
            overflow-x: hidden;
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

        /* Hero Section */
        .hero-section {
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9)), url('/api/placeholder/1920/1080');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--accent-color), #FFD700);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Product Cards */
        .product-card {
            background: rgba(26, 26, 26, 0.9);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.4s ease;
            border: 1px solid rgba(212, 175, 55, 0.1);
            opacity: 0;
            transform: translateY(30px);
        }

        .product-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.1);
        }

        .product-image {
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            transition: all 0.5s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.1);
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-title {
            font-size: 1.5rem;
            color: var(--accent-color);
            margin-bottom: 0.5rem;
        }

        .product-price {
            font-size: 1.8rem;
            color: var(--accent-color);
            font-weight: 700;
        }

        .btn-custom {
            background: linear-gradient(45deg, var(--accent-color), #FFD700);
            border: none;
            color: var(--primary-color);
            padding: 0.8rem 2rem;
            border-radius: 30px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }

        /* Filter Section */
        .filter-section {
            background: rgba(26, 26, 26, 0.9);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 3rem;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }

        .filter-title {
            color: var(--accent-color);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .form-control {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(212, 175, 55, 0.1);
            color: var(--text-color);
        }

        .form-control:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: var(--accent-color);
            box-shadow: none;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Footer */
        footer {
            background: rgba(26, 26, 26, 0.9);
            border-top: 1px solid rgba(212, 175, 55, 0.1);
            padding: 4rem 0 2rem;
        }

        .footer-title {
            color: var(--accent-color);
            margin-bottom: 1.5rem;
        }

        .social-icons {
            display: flex;
            gap: 1rem;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color);
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            background: var(--accent-color);
            color: var(--primary-color);
            transform: translateY(-5px);
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

        /* Cart Notification Styles */
        .cart-notification {
            position: fixed;
            top: -100px;
            right: 20px;
            background: linear-gradient(135deg, #D4AF37, #FFD700);
            color: #000;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .cart-notification.show {
            top: 100px;
            transform: translateY(0);
            opacity: 1;
        }

        .cart-notification i {
            font-size: 24px;
            animation: bounce 1s infinite;
        }

        .cart-notification-content {
            display: flex;
            flex-direction: column;
        }

        .cart-notification-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .cart-notification-message {
            font-size: 14px;
            opacity: 0.9;
        }

        .cart-notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 0 0 10px 10px;
        }

        .cart-notification-progress::before {
            content: '';
            position: absolute;
            height: 100%;
            width: 100%;
            background: rgba(0, 0, 0, 0.2);
            transform-origin: left;
            animation: progress 3s linear forwards;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-5px);
            }

            60% {
                transform: translateY(-3px);
            }
        }

        @keyframes progress {
            100% {
                transform: scaleX(0);
            }
        }

        .auth-required-notification {
            position: fixed;
            top: -100px;
            right: 20px;
            background: linear-gradient(135deg, #ff4757, #ff6b81);
            color: #fff;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            min-width: 300px;
        }

        .auth-required-notification.show {
            top: 100px;
            transform: translateY(0);
            opacity: 1;
        }

        .auth-required-notification i {
            font-size: 24px;
            animation: bounce 1s infinite;
        }

        .auth-required-notification .notification-content {
            flex-grow: 1;
        }

        .auth-required-notification .notification-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 3px;
            color: #fff;
        }

        .auth-required-notification .notification-message {
            font-size: 14px;
            opacity: 0.9;
            color: #fff;
            margin-bottom: 8px;
        }

        .auth-required-notification .notification-actions {
            display: flex;
            gap: 8px;
            margin-top: 5px;
        }

        .auth-required-notification .notification-btn {
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .auth-required-notification .login-btn {
            background: #fff;
            color: #ff4757;
        }

        .auth-required-notification .login-btn:hover {
            background: #f1f2f6;
        }

        .auth-required-notification .cancel-btn {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .auth-required-notification .cancel-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .auth-required-notification .notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0 0 10px 10px;
        }

        .auth-required-notification .notification-progress::before {
            content: '';
            position: absolute;
            height: 100%;
            width: 100%;
            background: rgba(255, 255, 255, 0.3);
            transform-origin: left;
            animation: progress 3s linear forwards;
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
                        <input type="search" name="search" class="search-input" placeholder="Search timepieces..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        <button type="submit" class="border-0 bg-transparent">
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
                                        <a href="logout.php" class="profile-action-btn">
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
                        <a href="login.php" class="auth-btn">Login</a>
                        <a href="register.php" class="auth-btn">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Discover Timeless Elegance</h1>
                <p class="hero-subtitle mb-4">Experience the finest collection of luxury timepieces</p>
                <a href="#products" class="btn btn-custom">Explore Collection</a>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="py-5">
        <div class="container">
            <!-- Filter Section -->
            <div class="filter-section">
                <h2 class="filter-title text-center">Our Collection</h2>
                <form method="get" class="row g-3">
                    <!-- Preserve search term if exists -->
                    <?php if (!empty($search)): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <?php endif; ?>

                    <div class="col-md-4">
                        <select name="brand" class="form-control">
                            <option value="">All Brands</option>
                            <?php
                            try {
                                $brand_sql = "SELECT DISTINCT brand FROM produk ORDER BY brand";
                                $brand_stmt = $koneksi->query($brand_sql);
                                while ($brand_row = $brand_stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = (isset($_GET['brand']) && $_GET['brand'] == $brand_row['brand']) ? ' selected' : '';
                                    echo '<option value="' . htmlspecialchars($brand_row['brand']) . '"' . $selected . '>' .
                                        htmlspecialchars($brand_row['brand']) . '</option>';
                                }
                            } catch (PDOException $e) {
                                echo "Error: " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="sort" class="form-control">
                            <option value="">Sort By</option>
                            <option value="price_asc" <?php echo ($sort == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_desc" <?php echo ($sort == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-custom w-100">Apply Filters</button>
                    </div>
                </form>
            </div>

            <!-- Products Grid -->
            <!-- Products Grid -->
            <div class="row g-4">
                <?php if (empty($result)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            No products found.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($result as $produk): ?>
                        <div class="col-md-4">
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="../images/<?php echo htmlspecialchars($produk['gambar']); ?>"
                                        class="img-fluid"
                                        alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-title">
                                        <?php echo htmlspecialchars($produk['nama_produk']); ?>
                                    </h3>
                                    <p class="product-description">
                                        <?php echo htmlspecialchars($produk['deskripsi']); ?>
                                    </p>
                                    <p class="product-price">
                                        Rp.<?php echo number_format($produk['harga']); ?>
                                    </p>
                                    <a href="?action=add_to_cart&produk_id=<?php echo $produk['produk_id']; ?>"
                                        class="btn btn-custom w-100">
                                        Add to Cart
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <div class="footer-links">
                        <div class="link-group">
                            <i class="fas fa-home"></i>
                            <a href="../index.php">Home</a>
                        </div>
                        <div class="link-group">
                            <i class="fas fa-store"></i>
                            <a href="products.php">Products</a>
                        </div>
                        <div class="link-group">
                            <i class="fas fa-shopping-cart"></i>
                            <a href="cart.php">Cart</a>
                        </div>
                        <div class="link-group">
                            <i class="fas fa-info-circle"></i>
                            <a href="about.php">About</a>
                        </div>
                        <div class="link-group">
                            <i class="fas fa-envelope"></i>
                            <a href="contact.php">Contact</a>
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

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Product Cards Animation
        const observerOptions = {
            threshold: 0.1
        };

        const observer = new IntersectionObserver(function(entries, observer) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.product-card').forEach(card => {
            observer.observe(card);
        });

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });



        function showCartNotification() {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'cart-notification';

            notification.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <div class="cart-notification-content">
            <div class="cart-notification-title">Successfully Added!</div>
            <div class="cart-notification-message">Product has been added to your cart</div>
        </div>
        <div class="cart-notification-progress"></div>
    `;

            // Add to document
            document.body.appendChild(notification);

            // Trigger animation
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);

            // Remove notification after animation
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }

        // Modified event listener to handle both notifications
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('unauthorized') === 'true') {
                showLoginRequiredNotification();
            } else if (urlParams.get('action') === 'add_to_cart') {
                showCartNotification();
            }
        });


        function showAuthRequiredNotification() {
            const notification = document.createElement('div');
            notification.className = 'auth-required-notification';

            notification.innerHTML = `
        <i class="fas fa-lock"></i>
        <div class="notification-content">
            <div class="notification-title">Authentication Required</div>
            <div class="notification-message">Please log in to add items to your cart</div>
            <div class="notification-actions">
                <a href="login.php" class="notification-btn login-btn">Login Now</a>
                <button class="notification-btn cancel-btn">Cancel</button>
            </div>
            <div class="notification-progress"></div>
        </div>
    `;

            document.body.appendChild(notification);

            // Trigger animation
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);

            // Handle cancel button
            const cancelBtn = notification.querySelector('.cancel-btn');
            cancelBtn.addEventListener('click', () => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 500);
            });

            // Auto remove after 3 seconds
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }

        // Modify your existing event listener
        document.addEventListener('DOMContentLoaded', function() {
            // Add click handlers to all "Add to Cart" buttons
            document.querySelectorAll('.btn-custom').forEach(button => {
                button.addEventListener('click', function(e) {
                    <?php if (!isset($_SESSION['username'])): ?>
                        e.preventDefault();
                        showAuthRequiredNotification();
                    <?php endif; ?>
                });
            });

            // Your existing notification handlers
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'add_to_cart') {
                showCartNotification();
            }
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