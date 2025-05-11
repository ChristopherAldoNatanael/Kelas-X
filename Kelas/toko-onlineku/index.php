<?php
session_start(); // Only call this once
require_once 'includes/config.php'; // Ensures database connection is established

// Check if database connection is working
try {
    $testQuery = $koneksi->query("SELECT 1");
    if (!$testQuery) {
        error_log("Database connection test failed without throwing exception");
    }
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
}

// Check for username in session
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    error_log("Username not set in session or empty");
    // Consider redirecting to login page
    // header("Location: login.php");
    // exit;
}

// Query untuk carousel banner
// Query untuk mengambil banner
$stmt_banner = $koneksi->prepare("SELECT gambar, nama_produk, deskripsi FROM produk ORDER BY produk_id DESC LIMIT 3");
$stmt_banner->execute();

if ($stmt_banner) {
    $banners = $stmt_banner->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Jika ada kesalahan dalam eksekusi query
    // error_log("Query failed: " . implode(", ", $stmt_banner->errorInfo()));
    $banners = []; // Atur ke array kosong jika query gagal
}

// Debug output untuk memeriksa hasil
var_dump($banners);

// Query untuk produk unggulan
$stmt_featured = $koneksi->prepare("SELECT produk_id, gambar, nama_produk, harga FROM produk ORDER BY stok DESC LIMIT 3");
$stmt_featured->execute();
$featured_products = $stmt_featured->fetchAll(PDO::FETCH_ASSOC);

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
        error_log("Error getting cart count: " . $e->getMessage());
        return 0;
    }
}

// Get cart count if user is logged in
$cartCount = 0;
if (isset($_SESSION['pelanggan_id'])) {
    $cartCount = getCartCount($koneksi, $_SESSION['pelanggan_id']);
}

// Fetch active banners
$stmt = $koneksi->prepare("
    SELECT * FROM banners 
    WHERE status = 'active' 
    ORDER BY position ASC
");
$stmt->execute();
$banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Brand query
$stmt_brands = $koneksi->prepare("
    SELECT DISTINCT brand 
    FROM produk 
    WHERE brand IS NOT NULL 
    ORDER BY brand ASC
");
$stmt_brands->execute();
$brands = $stmt_brands->fetchAll(PDO::FETCH_ASSOC);

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
        // Validate input
        if (empty($username)) {
            error_log("Empty username provided to getUserEmail()");
            return "";
        }

        $stmt = $koneksi->prepare("SELECT email FROM pelanggan WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['email'];
        } else {
            error_log("No user found with username: " . $username);
            return "";
        }
    } catch (PDOException $e) {
        error_log("Database error in getUserEmail(): " . $e->getMessage());
        return "";
    }
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
        // Validate input
        if (empty($username)) {
            error_log("Empty username provided to getUserAddress()");
            return "No address provided";
        }

        $stmt = $koneksi->prepare("SELECT alamat FROM pelanggan WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return !empty($result['alamat']) ? $result['alamat'] : 'No address provided';
        } else {
            error_log("No user found with username for address: " . $username);
            return "No address provided";
        }
    } catch (PDOException $e) {
        error_log("Database error in getUserAddress(): " . $e->getMessage());
        return "No address provided";
    }
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
        // Validate input
        if (empty($username)) {
            error_log("Empty username provided to getUserRole()");
            return "user";
        }

        $stmt = $koneksi->prepare("SELECT role FROM pelanggan WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['role'];
        } else {
            error_log("No user found with username for role: " . $username);
            return "user";
        }
    } catch (PDOException $e) {
        error_log("Database error in getUserRole(): " . $e->getMessage());
        return "user"; // Default to user if role can't be determined
    }
}

/**
 * Get user registration date
 * 
 * @param string $username The username to look up
 * @return string Formatted registration date
 */
function getUserRegistrationDate($username)
{
    global $koneksi;

    try {
        // Validate input
        if (empty($username)) {
            error_log("Empty username provided to getUserRegistrationDate()");
            return date('M Y');
        }

        $stmt = $koneksi->prepare("SELECT created_at FROM pelanggan WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($result['created_at']) && !empty($result['created_at'])) {
                return date('M Y', strtotime($result['created_at']));
            } else {
                error_log("User found but created_at is empty for: " . $username);
                return date('M Y');
            }
        } else {
            error_log("No user found with username for registration date: " . $username);
            return date('M Y');
        }
    } catch (PDOException $e) {
        error_log("Database error in getUserRegistrationDate(): " . $e->getMessage());
        return date('M Y'); // Fallback to current date if no record found
    }
}

// After successful update
if (isset($_SESSION['username'])) {
    // Retrieve email and address here to ensure they're always up-to-date
    $userEmail = getUserEmail($_SESSION['username']);
    $userAddress = getUserAddress($_SESSION['username']);
} else {
    $userEmail = "";
    $userAddress = "No address provided";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EliteWatch - Luxury Timepieces</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>

    <style>
        :root {
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
            background: var(--primary-color);
            color: var(--text-light);
            font-family: 'Helvetica Neue', sans-serif;
        }

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

        /* Container for the entire carousel section */
        .splide-container {
            max-width: 100%;
            margin: 0 auto;
            padding-bottom: 60px;
            position: relative;
        }

        /* Main carousel styling */
        #main-carousel {
            margin-bottom: 1rem;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        #main-carousel .splide__track {
            border-radius: 15px;
            overflow: hidden;
        }

        #main-carousel .splide__slide {
            height: 500px;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }

        #main-carousel .slide-content {
            position: relative;
            width: 100%;
            height: 100%;
        }

        #main-carousel .slide-content img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
            transition: transform 0.5s ease;
        }

        /* #main-carousel .slide-content:hover img {
            transform: scale(1.03);
        } */

        /* Slide caption styling */
        .slide-caption {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 25px;
            text-align: center;
            transform: translateY(100%);
            transition: transform 0.4s ease;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        /* #main-carousel .slide-content:hover .slide-caption {
            transform: translateY(0);
        } */

        .slide-caption h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #d4af37;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .slide-caption p {
            font-size: 1.1rem;
            color: #fff;
        }

        /* Custom arrows styling */
        .splide__arrow {
            width: 50px !important;
            height: 50px !important;
            background: #d4af37 !important;
            border-radius: 50% !important;
            opacity: 0.7 !important;
            transition: all 0.3s ease !important;
        }

        .splide__arrow:hover {
            opacity: 1 !important;
            background: #e5c158 !important;
        }

        .arrow-icon {
            font-size: 18px;
            color: #000;
            font-weight: bold;
        }

        .splide__arrow--prev {
            left: 1em !important;
        }

        .splide__arrow--next {
            right: 1em !important;
        }

        .splide__arrow svg {
            display: none;
        }

        /* Thumbnail carousel styling */
        #thumbnail-carousel {
            margin-top: 20px;
        }

        #thumbnail-carousel .splide__track {
            padding: 5px 0;
        }

        #thumbnail-carousel .splide__slide {
            opacity: 0.7;
            border-radius: 5px;
            overflow: hidden;
            height: 60px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }

        #thumbnail-carousel .splide__slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #thumbnail-carousel .splide__slide.is-active {
            opacity: 1;
            transform: scale(1.1);
            border-color: #d4af37;
            box-shadow: 0 2px 10px rgba(212, 175, 55, 0.5);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #main-carousel .splide__slide {
                height: 400px;
            }

            .slide-caption h2 {
                font-size: 1.5rem;
            }

            .slide-caption p {
                font-size: 1rem;
            }

            #thumbnail-carousel .splide__slide {
                height: 50px;
            }
        }

        @media (max-width: 480px) {
            #main-carousel .splide__slide {
                height: 350px;
            }

            .slide-caption {
                padding: 15px;
            }

            .slide-caption h2 {
                font-size: 1.3rem;
            }

            #thumbnail-carousel .splide__slide {
                height: 40px;
            }
        }

        /* Product Cards */
        .product-card {
            background: linear-gradient(145deg, #1a1a1a, #0a0a0a);
            border-radius: 20px;
            padding: 1.5rem;
            margin: 1rem 0;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(212, 175, 55, 0.1);
        }

        .product-image {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
        }

        .product-image img {
            transition: var(--transition);
            transform: scale(1.05);
        }

        .product-card:hover .product-image img {
            transform: scale(1);
        }

        .product-info {
            padding: 1.5rem 0 0;
            text-align: center;
        }

        .product-info h4 {
            color: var(--accent-color);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .price {
            font-size: 1.25rem;
            color: var(--text-light);
            font-weight: 700;
            position: relative;
            display: inline-block;
        }

        /* Feature Cards */
        .feature-card {
            background: linear-gradient(145deg, #1a1a1a, #0a0a0a);
            border-radius: 20px;
            padding: 2rem;
            margin: 1rem 0;
            transition: var(--transition);
            border: 1px solid rgba(212, 175, 55, 0.1);
            text-align: center;
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--accent-color);
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .feature-card:hover .feature-icon {
            transform: rotateY(360deg);
        }

        /* Footer */
        footer {
            background: linear-gradient(to bottom, var(--primary-color), #000);
            padding: 4rem 0 2rem;
            margin-top: 4rem;
            position: relative;
        }

        .footer-title {
            color: var(--accent-color);
            margin-bottom: 2rem;
            position: relative;
            display: inline-block;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 2px;
            bottom: -10px;
            left: 0;
            background: var(--accent-color);
        }

        .social-icons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color);
            transition: var(--transition);
        }

        .social-icon:hover {
            background: var(--accent-color);
            color: var(--primary-color);
            transform: translateY(-5px);
        }

        /* Custom Animations */
        @keyframes floatAnimation {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes glowingBorder {

            0%,
            100% {
                border-color: rgba(212, 175, 55, 0.1);
            }

            50% {
                border-color: rgba(212, 175, 55, 0.3);
            }
        }

        .animate-float {
            animation: floatAnimation 3s ease-in-out infinite;
        }

        .animate-border {
            animation: glowingBorder 2s ease-in-out infinite;
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

        .brand-navigation {
            background: linear-gradient(to bottom, rgba(10, 10, 10, 0.95), rgba(10, 10, 10, 0.9));
            padding: 20px 0;
            margin-top: -130px;
            border-top: 1px solid rgba(212, 175, 55, 0.1);
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }

        .brand-nav-wrapper {
            position: relative;
            overflow: hidden;
        }

        .brand-nav-wrapper::before,
        .brand-nav-wrapper::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 50px;
            z-index: 2;
            pointer-events: none;
        }

        .brand-nav-wrapper::before {
            left: 0;
            background: linear-gradient(to right, rgba(10, 10, 10, 0.95), transparent);
        }

        .brand-nav-wrapper::after {
            right: 0;
            background: linear-gradient(to left, rgba(10, 10, 10, 0.95), transparent);
        }

        .brand-scroll {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding: 10px 20px;
            scrollbar-width: none;
            -ms-overflow-style: none;
            scroll-behavior: smooth;
        }

        .brand-scroll::-webkit-scrollbar {
            display: none;
        }

        .brand-item {
            flex: 0 0 auto;
            padding: 10px 25px;
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 25px;
            color: #d4af37;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .brand-item::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(212, 175, 55, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .brand-item:hover::before {
            width: 200%;
            height: 200%;
        }

        .brand-name {
            position: relative;
            z-index: 1;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .brand-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.15);
            border-color: rgba(212, 175, 55, 0.4);
            color: #d4af37;
        }

        /* Add scroll buttons for wider screens */
        @media (min-width: 768px) {
            .brand-nav-wrapper {
                position: relative;
            }

            .brand-scroll-btn {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 40px;
                height: 40px;
                background: rgba(212, 175, 55, 0.8);
                border: none;
                border-radius: 50%;
                color: #000;
                cursor: pointer;
                z-index: 3;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .scroll-left {
                left: 10px;
            }

            .scroll-right {
                right: 10px;
            }

            .brand-scroll-btn:hover {
                background: #d4af37;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .brand-item {
                padding: 8px 20px;
            }

            .brand-name {
                font-size: 0.85rem;
            }
        }

        .video-section {
            padding: 10rem 0;
            background: linear-gradient(to bottom, rgba(10, 10, 10, 0.95), rgba(10, 10, 10, 0.9));
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }

        .video-wrapper {
            position: relative;
            width: 100%;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(212, 175, 55, 0.2);
            transform: translateY(50px);
            opacity: 0;
            transition: all 0.6s ease;
        }

        .video-wrapper.visible {
            transform: translateY(0);
            opacity: 1;
        }

        .custom-video {
            width: 100%;
            display: block;
            border-radius: 15px;
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

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">EliteWatch</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="pages/products.php">Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link cart-icon" href="pages/cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-badge"><?php echo $cartCount; ?></span>
                        </a>
                    </li>
                </ul>

                <div class="search-container">
                    <form class="search-form" method="GET" action="pages/products.php">
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
                                        <?php if (isset($_SESSION['username']) && !empty($_SESSION['username'])): ?>
                                            <div class="info-item">
                                                <div class="info-icon"><i class="fas fa-envelope"></i></div>
                                                <div class="info-text">
                                                    <?php
                                                    $email = getUserEmail($_SESSION['username']);
                                                    echo empty($email) ? "Email tidak tersedia" : htmlspecialchars($email);
                                                    ?>
                                                </div>
                                            </div>

                                            <div class="info-item">
                                                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                                <div class="info-text">
                                                    <?php
                                                    $address = getUserAddress($_SESSION['username']);
                                                    echo htmlspecialchars($address);
                                                    ?>
                                                </div>
                                            </div>

                                            <div class="info-item">
                                                <div class="info-icon"><i class="fas fa-user-tag"></i></div>
                                                <div class="info-text">
                                                    Member since: <?php echo htmlspecialchars(getUserRegistrationDate($_SESSION['username'])); ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="info-item">
                                                <p>Please log in to view your profile information.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="profile-actions">
                                        <a href="pages/profile.php" class="profile-action-btn">
                                            <i class="fas fa-user-edit"></i> Edit Profile
                                        </a>
                                        <?php if ($userRole == 'admin'): ?>
                                            <a href="admin/dashboard.php" class="profile-action-btn">
                                                <i class="fas fa-tachometer-alt"></i> Dashboard
                                            </a>
                                        <?php else: ?>
                                            <a href="pages/history.php" class="profile-action-btn">
                                                <i class="fas fa-shopping-bag"></i> My Orders
                                            </a>
                                        <?php endif; ?>
                                        <a href="pages/setting.php" class="profile-action-btn">
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
                        <a href="pages/login.php" class="auth-btn">Login</a>
                        <a href="pages/register.php" class="auth-btn">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>


    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-12 animate__animated animate__fadeInLeft">
                <!-- Main Splide carousel -->
                <div class="splide-container">
                    <div id="main-carousel" class="splide">
                        <div class="splide__track">
                            <ul class="splide__list">
                                <?php foreach ($banners as $key => $banner): ?>
                                    <li class="splide__slide">
                                        <div class="slide-content">
                                            <img src="images/<?php echo htmlspecialchars($banner['image_url']); ?>"
                                                alt="<?php echo htmlspecialchars($banner['title']); ?>">
                                            <div class="slide-caption">
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <!-- Custom arrows -->
                        <div class="splide__arrows">
                            <button class="splide__arrow splide__arrow--prev">
                                <span class="arrow-icon">&lt;</span>
                            </button>
                            <button class="splide__arrow splide__arrow--next">
                                <span class="arrow-icon">&gt;</span>
                            </button>
                        </div>
                    </div>

                    <!-- Thumbnail Splide carousel -->
                    <div id="thumbnail-carousel" class="splide">
                        <div class="splide__track">
                            <ul class="splide__list">
                                <?php foreach ($banners as $key => $banner): ?>
                                    <li class="splide__slide">
                                        <img src="images/<?php echo htmlspecialchars($banner['image_url']); ?>"
                                            alt="Thumbnail">
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="brand-navigation">
        <div class="container">
            <div class="brand-nav-wrapper">
                <div class="brand-scroll">
                    <?php foreach ($brands as $brand): ?>
                        <a href="pages/products.php?brand=<?php echo urlencode($brand['brand']); ?>" class="brand-item">
                            <span class="brand-name"><?php echo htmlspecialchars($brand['brand']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="video-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="video-wrapper" data-video-wrapper>
                        <video id="autoplayVideo" class="custom-video" muted playsinline>
                            <source src="images/video1.mp4" type="video/mp4">
                            <!-- Your browser does not support the video tag. -->
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- <div class="col-md-4 animate__animated animate__fadeInRight">
                    <div class="feature-card">
                        <i class="fas fa-shipping-fast feature-icon"></i>
                        <h4>Free Shipping</h4>
                        <p>Worldwide delivery on all orders over $500</p>
                    </div>
                    <div class="feature-card">
                        <a href="pages/products.php"><img src="images/banner 1.png" height="100px" alt=""></a>
                    </div>
                </div>
            </div> -->

    <div class="feature-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 animate-on-scroll">
                    <div class="feature-card">
                        <i class="fas fa-crown feature-icon"></i>
                        <h4>Premium Quality</h4>
                        <p>Only the finest materials and craftsmanship</p>
                    </div>
                </div>
                <div class="col-md-4 animate-on-scroll">
                    <div class="feature-card">
                        <i class="fas fa-shield-alt feature-icon"></i>
                        <h4>Secure Shopping</h4>
                        <p>Your security is our top priority</p>
                    </div>
                </div>
                <div class="col-md-4 animate-on-scroll">
                    <div class="feature-card">
                        <i class="fas fa-clock feature-icon"></i>
                        <h4>24/7 Support</h4>
                        <p>Always here to assist you</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="featured-products-section">
        <div class="container">
            <h2 class="text-center mb-5 animate-on-scroll">Featured Products</h2>
            <div class="row">
                <?php foreach ($featured_products as $product): ?>
                    <div class="col-md-4 animate-on-scroll">
                        <div class="product-card">
                            <a href="#?id=<?php echo $product['produk_id']; ?>" class="product-link">
                                <div class="product-image">
                                    <img src="images/<?php echo htmlspecialchars($product['gambar']); ?>"
                                        alt="<?php echo htmlspecialchars($product['nama_produk']); ?>"
                                        class="img-fluid">
                                </div>
                                <div class="product-info">
                                    <h4><?php echo htmlspecialchars($product['nama_produk']); ?></h4>
                                    <p class="price">Rp.<?php echo number_format($product['harga']); ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
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


    <script src="assets/js/jquery-3.5.1.slim.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
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

        // Search Input Animation
        const searchInput = document.querySelector('.search-input');
        const searchIcon = document.querySelector('.search-icon');

        searchInput.addEventListener('focus', () => {
            searchIcon.style.color = 'var(--accent-color)';
        });

        searchInput.addEventListener('blur', () => {
            searchIcon.style.color = 'var(--text-light)';
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize carousel with options
            const carouselElement = document.querySelector('#mainCarousel');
            const carousel = new bootstrap.Carousel(carouselElement, {
                interval: 1000,
                ride: true,
                wrap: true,
                touch: true,
                pause: 'hover'
            });

            // Force start the carousel
            carousel.cycle();

            // Add click handlers for preview items
            document.querySelectorAll('.preview-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (this.classList.contains('preview-left')) {
                        carousel.prev();
                    } else if (this.classList.contains('preview-right')) {
                        carousel.next();
                    }
                });
            });

            // Ensure carousel keeps running after user interaction
            carouselElement.addEventListener('slid.bs.carousel', () => {
                setTimeout(() => {
                    carousel.cycle();
                }, 0);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const brandScroll = document.querySelector('.brand-scroll');

            // Add smooth scrolling with mouse wheel
            brandScroll.addEventListener('wheel', (e) => {
                e.preventDefault();
                brandScroll.scrollLeft += e.deltaY;
            });

            // Optional: Add touch scrolling for mobile
            let isDown = false;
            let startX;
            let scrollLeft;

            brandScroll.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - brandScroll.offsetLeft;
                scrollLeft = brandScroll.scrollLeft;
            });

            brandScroll.addEventListener('mouseleave', () => {
                isDown = false;
            });

            brandScroll.addEventListener('mouseup', () => {
                isDown = false;
            });

            brandScroll.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - brandScroll.offsetLeft;
                const walk = (x - startX) * 2;
                brandScroll.scrollLeft = scrollLeft - walk;
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('autoplayVideo');
            const videoWrapper = document.querySelector('[data-video-wrapper]');

            // Create Intersection Observer
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Play video when it comes into view
                        video.play();
                        videoWrapper.classList.add('visible');
                    } else {
                        // Pause video when it's out of view
                        video.pause();
                        videoWrapper.classList.remove('visible');
                    }
                });
            }, {
                threshold: 0.3 // Trigger when 30% of the video is visible
            });

            // Start observing the video element
            if (videoWrapper) {
                observer.observe(videoWrapper);
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


        document.addEventListener('DOMContentLoaded', function() {
            // Set up the main carousel
            var main = new Splide('#main-carousel', {
                type: 'fade',
                perPage: 1,
                perMove: 1,
                gap: '1rem',
                pagination: false,
                arrows: true,
                speed: 1000,
                autoplay: true,
                interval: 2000,
                pauseOnHover: true,
                pauseOnFocus: true,
                rewind: true, // Allow the carousel to go back to the first slide
                loop: true, // Enable continuous looping
            });

            // Set up the thumbnail carousel
            var thumbnails = new Splide('#thumbnail-carousel', {
                perPage: 5,
                perMove: 1,
                gap: '0.5rem',
                rewind: true,
                pagination: false,
                arrows: false,
                isNavigation: true,
                breakpoints: {
                    768: {
                        perPage: 3,
                    },
                    480: {
                        perPage: 2,
                    },
                },
            });

            // Link the two carousels
            main.sync(thumbnails);

            // Mount the carousels
            main.mount();
            thumbnails.mount();
        });
    </script>

</body>

</html>