<?php
session_start();
require_once '../includes/config.php';


if (!isset($_SESSION['pelanggan_id'])) {
    header('Location: login.php');
    exit();
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

// Function to get orders for a customer
function getCustomerOrders($koneksi, $pelanggan_id)
{
    try {
        $sql = "SELECT * FROM orders WHERE pelanggan_id = :pelanggan_id ORDER BY order_date DESC";
        $stmt = $koneksi->prepare($sql);
        $stmt->execute([':pelanggan_id' => $pelanggan_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Get customer orders
$orders = [];
if (isset($_SESSION['pelanggan_id'])) {
    $orders = getCustomerOrders($koneksi, $_SESSION['pelanggan_id']);
}

// Function to get status badge class
function getStatusBadgeClass($status)
{
    switch ($status) {
        case 'pending':
            return 'badge-warning';
        case 'processing':
            return 'badge-info';
        case 'shipped':
            return 'badge-primary';
        case 'completed':
            return 'badge-success';
        default:
            return 'badge-secondary';
    }
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



// Get customer ID from session
// Get the customer ID from the session
$pelanggan_id = $_SESSION['pelanggan_id'];

try {
    // Fetch customer information
    $stmt = $koneksi->prepare("SELECT username, email, alamat FROM pelanggan WHERE pelanggan_id = :pelanggan_id");
    $stmt->bindParam(':pelanggan_id', $pelanggan_id, PDO::PARAM_INT);
    $stmt->execute();
    $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch orders for this customer
    $sql = "SELECT o.*, COUNT(oi.order_item_id) as item_count 
            FROM orders o 
            LEFT JOIN order_items oi ON o.order_id = oi.order_id 
            WHERE o.pelanggan_id = :pelanggan_id 
            GROUP BY o.order_id 
            ORDER BY o.order_date DESC";

    $stmt = $koneksi->prepare($sql);
    $stmt->bindParam(':pelanggan_id', $pelanggan_id, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Function to format date
function formatDate($dateString)
{
    $date = new DateTime($dateString);
    return $date->format('d M Y, H:i');
}

// Example usage of formatted date and displaying customer info

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - EliteWatch</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <style>
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

        .order-card {
            transition: transform 0.3s;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.85rem;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .order-date {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .empty-orders {
            text-align: center;
            padding: 50px 0;
        }

        .empty-orders i {
            font-size: 5rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .avatar-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .user-welcome h4 {
            margin-bottom: 0;
        }

        .user-welcome p {
            margin-bottom: 0;
            color: #6c757d;
        }

        .btn-detail {
            border-radius: 20px;
            padding: 8px 20px;
        }

        body {
            background: linear-gradient(145deg, #0a0a0a, #1a1a1a);
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
            max-width: 300px;
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

        /* Order History Specific Styles */
        .order-history-container {
            padding: 2rem;
            margin-top: 100px;
            background: linear-gradient(145deg, #0a0a0a, #1a1a1a);
            min-height: calc(100vh - 100px);
            margin-bottom: 0;
            /* Remove any bottom margin */
        }

        .history-title {
            color: var(--gold);
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
        }

        .history-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        .order-card {
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid var(--gold);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .order-card::before {
            content: '';
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), transparent);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .order-card:hover::before {
            opacity: 1;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            padding-bottom: 1rem;
        }

        .order-id {
            color: var(--gold);
            font-size: 1.2rem;
            font-weight: 600;
        }

        .order-date {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .detail-label {
            color: var(--gold);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .detail-value {
            color: var(--text-light);
            font-size: 1rem;
        }

        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .badge-warning {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .badge-info {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
            border: 1px solid rgba(23, 162, 184, 0.3);
        }

        .badge-primary {
            background: rgba(0, 123, 255, 0.2);
            color: #007bff;
            border: 1px solid rgba(0, 123, 255, 0.3);
        }

        .badge-success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .order-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1rem;
        }

        .order-btn {
            padding: 0.5rem 1rem;
            border: 1px solid var(--gold);
            border-radius: 5px;
            background: transparent;
            color: var(--gold);
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .order-btn:hover {
            background: var(--gold);
            color: var(--dark);
        }

        .no-orders {
            text-align: center;
            color: var(--text-light);
            padding: 3rem;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 15px;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .no-orders i {
            font-size: 3rem;
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .no-orders h3 {
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .no-orders p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 1.5rem;
        }

        .shop-now-btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: var(--gold);
            color: var(--dark);
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .shop-now-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }



        /* Footer */
        footer {
            background: linear-gradient(to bottom, #0a0a0a, #000000);
            padding: 5rem 0 2rem;
            margin-top: 0;
            /* Remove top margin to eliminate gap */
            position: relative;
            border-top: 1px solid rgba(212, 175, 55, 0.1);
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


        .order-card {
            transition: transform 0.3s;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.85rem;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .order-date {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .empty-orders {
            text-align: center;
            padding: 50px 0;
        }

        .empty-orders i {
            font-size: 5rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .avatar-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .user-welcome h4 {
            margin-bottom: 0;
        }

        .user-welcome p {
            margin-bottom: 0;
            color: #6c757d;
        }

        .btn-detail {
            border-radius: 20px;
            padding: 8px 20px;
        }

        .coba {
            margin-top: 100px;
        }

        .judul {
            color: var(--gold);
        }

        .background-nama {
            background-color: black;
            border-color: var(--gold);
            border: 1px solid var(--gold);
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



    <div class="container py-4">
        <div class="row mb-4 coba">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="mb-0 judul">Riwayat Pesanan</h1>
                    <a href="../index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
                    </a>
                </div>
                <hr>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body background-nama">
                        <div class="avatar-container">
                            <div class="avatar">
                                <?php echo strtoupper(substr($pelanggan['username'], 0, 1)); ?>
                            </div>
                            <div class="user-welcome">
                                <h4>Hi, <?php echo htmlspecialchars($pelanggan['username']); ?></h4>
                                <p><?php echo htmlspecialchars($pelanggan['email']); ?></p>
                            </div>
                        </div>

                        <div class="row">
                            <!-- <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                    <strong>Alamat:</strong>
                                    <?php echo $pelanggan['alamat'] ? htmlspecialchars($pelanggan['alamat']) : 'Belum diatur'; ?>
                                </p>
                            </div> -->
                            <div class="col-md-6">
                                <p class="mb-2 text-light">
                                    <i class="fas fa-shopping-bag me-2 text-primary"></i>
                                    <strong class="text-primary">Total Pesanan :</strong>
                                    <?php echo count($orders); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (count($orders) > 0): ?>
            <div class="row">
                <?php foreach ($orders as $order): ?>
                    <div class="col-md-12">
                        <div class="order-card card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="d-flex align-items-center mb-3">
                                            <h5 class="mb-0 me-3 text-light">Pesanan #<?php echo $order['order_id']; ?></h5>
                                            <span class="status-badge text-white <?php echo getStatusBadgeClass($order['status']); ?>">
                                                <?php
                                                $statusLabels = [
                                                    'pending' => 'Menunggu Pembayaran',
                                                    'processing' => 'Diproses',
                                                    'shipped' => 'Dikirim',
                                                    'completed' => 'Selesai',
                                                    'cancelled' => 'Dibatalkan'
                                                ];
                                                echo $statusLabels[$order['status']] ?? ucfirst($order['status']);
                                                ?>
                                            </span>
                                        </div>
                                        <p class="order-date mb-2 text-light">
                                            <i class="far fa-calendar-alt me-2"></i>
                                            <?php echo formatDate($order['order_date']); ?>
                                        </p>

                                        <?php if ($order['payment_method']): ?>
                                            <p class="mb-1 text-light">
                                                <i class="fas fa-credit-card me-2 "></i>
                                                Metode Pembayaran: <?php echo htmlspecialchars($order['payment_method']); ?>
                                            </p>
                                        <?php endif; ?>

                                        <p class="mb-0 text-light">
                                            <i class="fas fa-box me-2 "></i>
                                            <?php echo $order['item_count']; ?> produk
                                        </p>
                                    </div>
                                    <div class="col-md-5 d-flex flex-column justify-content-center align-items-end">
                                        <h5 class="text-light mb-3">
                                            Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                        </h5>
                                        <a href='order_detail.php?id=<?php echo $order['order_id']; ?>'" class=" btn btn-primary btn-detail">
                                            <i class="fas fa-eye me-2"></i>Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-orders text-center mt-4">
                <i class="fas fa-shopping-bag fa-3x"></i>
                <h3>Tidak ada pesanan</h3>
                <p class="text-muted">Anda belum memiliki pesanan. Mulai belanja sekarang!</p>
                <a href="products.php" class="btn btn-primary mt-3">Mulai Belanja</a>
            </div>
        <?php endif; ?>
    </div>




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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileToggle = document.getElementById('profile-toggle');
            const profileModule = document.getElementById('profile-module');
            let isProfileOpen = false;

            // Toggle profile module on click
            if (profileToggle) {
                profileToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    isProfileOpen = !isProfileOpen;
                    if (isProfileOpen) {
                        profileModule.style.display = 'block';
                        setTimeout(() => {
                            profileModule.style.opacity = '1';
                            profileModule.style.transform = 'translateY(0)';
                        }, 50);
                    } else {
                        profileModule.style.opacity = '0';
                        profileModule.style.transform = 'translateY(10px)';
                        setTimeout(() => {
                            profileModule.style.display = 'none';
                        }, 300);
                    }
                });
            }

            // Close profile module when clicking outside
            document.addEventListener('click', function(e) {
                if (isProfileOpen && !profileModule.contains(e.target) && e.target !== profileToggle) {
                    isProfileOpen = false;
                    profileModule.style.opacity = '0';
                    profileModule.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        profileModule.style.display = 'none';
                    }, 300);
                }
            });

            // Prevent profile module from closing when clicking inside it
            if (profileModule) {
                profileModule.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Add hover effect to profile action buttons
            const profileActionBtns = document.querySelectorAll('.profile-action-btn');
            profileActionBtns.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>

</html>