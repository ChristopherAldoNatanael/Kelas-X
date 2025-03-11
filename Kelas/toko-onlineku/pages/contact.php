<?php
session_start();
include("../includes/config.php");

// CONTACT FORM PROCESSING
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            // Use prepared statements to prevent SQL injection
            $sql = "INSERT INTO messages (username, email, message) VALUES (?, ?, ?)";
            $stmt = $koneksi->prepare($sql);

            // Execute the statement
            $stmt->execute([$name, $email, $message]);

            $success = "Your message has been sent! We'll get back to you soon.";
        } catch (PDOException $e) {
            $error = "Failed to send message. Please try again.";
            // Log the error (important for debugging)
            error_log("PDO error: " . $e->getMessage());
        }
    }
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
    <title>EliteWatch - Contact Us</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #000000, #1a1a1a);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
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

        .btn-custom-gold {
            background-color: transparent;
            border: 2px solid var(--gold);
            color: var(--gold);
            transition: all 0.3s ease;
        }

        .btn-custom-gold:hover {
            background-color: var(--gold);
            color: #111;
            transform: scale(1.05);
        }



        /* Contact Form Specific Styles */
        .container-contact {
            flex: 1;
            width: 100%;
            max-width: 800px;
            padding: 20px;
            margin: 120px auto 60px;
        }

        .contact-container {
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(212, 175, 55, 0.2);
            border: 1px solid rgba(212, 175, 55, 0.3);
            position: relative;
            overflow: hidden;
        }

        .contact-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
        }

        .contact-container h2 {
            color: #D4AF37;
            font-size: 2.5rem;
            margin-bottom: 40px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 3px;
            position: relative;
            padding-bottom: 15px;
        }

        .contact-container h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }

        .form-control {
            width: 100%;
            padding: 20px 15px;
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(0, 0, 0, 0.5);
            color: #ffffff;
            box-sizing: border-box;
        }

        .form-control::placeholder {
            color: transparent;
        }

        .form-control:focus {
            border-color: #D4AF37;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
            outline: none;
            background: rgba(0, 0, 0, 0.7);
        }

        .form-group label {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            padding: 0 5px;
            color: rgba(212, 175, 55, 0.7);
            font-size: 1rem;
            transition: all 0.3s ease;
            pointer-events: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group textarea~label {
            top: 30px;
        }

        .form-control:focus~label,
        .form-control:not(:placeholder-shown)~label {
            top: 0;
            left: 15px;
            font-size: 0.85rem;
            padding: 0 10px;
            background: #000;
            color: #D4AF37;
            border-radius: 10px;
            transform: translateY(-50%);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 150px;
            padding: 20px 15px;
        }

        .fas {
            margin-right: 8px;
            color: #D4AF37;
            transition: all 0.3s ease;
        }

        .form-group:focus-within .fas {
            transform: scale(1.2);
        }

        .btn-custom-gold {
            background: linear-gradient(45deg, #D4AF37, #FFD700);
            border: none;
            color: #000;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 2px;
            width: 100%;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
        }

        .btn-custom-gold::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s ease;
        }

        .btn-custom-gold:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
            background: linear-gradient(45deg, #FFD700, #D4AF37);
        }

        .btn-custom-gold:hover::before {
            left: 100%;
        }

        /* Alert Styles */
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            animation: slideIn 0.5s ease-out;
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid;
        }

        .alert-success {
            color: #4BB543;
            /* Bright green text */
            border-color: #4BB543;
            background: rgba(75, 181, 67, 0.1);
            /* Subtle green background */
        }

        .alert-error {
            color: #ff4444;
            border-color: #ff4444;
            background: rgba(255, 68, 68, 0.1);
            /* Subtle red background */
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

        /* Additional hover effects
        .form-group:hover .form-control:not(:focus):not(:placeholder-shown) {
            border-color: rgba(212, 175, 55, 0.5);
        } */

        .form-control:focus::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container-contact {
                padding: 10px;
                margin-top: 100px;
            }

            .contact-container {
                padding: 20px;
            }

            h2 {
                font-size: 2rem;
            }

            .form-control {
                padding: 15px;
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
            color: white;
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
    <div class="animated-background"></div>

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

    <div class="container-contact">
        <div class="contact-container animate__animated animate__fadeIn">
            <h2 class="animate__animated animate__fadeInDown">
                <i class="fas fa-envelope"></i> Contact Us
            </h2>

            <?php if (isset($success)): ?>
                <div class="alert alert-success animate__animated animate__fadeIn">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error animate__animated animate__fadeIn">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="contact.php" class="animate__animated animate__fadeInUp">
                <div class="form-group">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                    <label for="name"><i class="fas fa-user"></i> Name</label>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                </div>
                <div class="form-group">
                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Message" required></textarea>
                    <label for="message"><i class="fas fa-comment"></i> Message</label>
                </div>
                <button type="submit" class="btn btn-custom-gold animate__animated animate__pulse">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
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
                            <a href="/about.php">About</a>
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
            <div class="text-center mt-4 text-light">
                <p>&copy; <?php echo date("Y"); ?> EliteWatch. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script>
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', () => {
                input.classList.add('animate__animated', 'animate__pulse');
            });

            input.addEventListener('blur', () => {
                input.classList.remove('animate__animated', 'animate__pulse');
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