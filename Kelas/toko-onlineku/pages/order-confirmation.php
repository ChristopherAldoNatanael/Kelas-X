<?php
// Include database configuration
require_once('../includes/config.php');

try {
    // Get the order ID from URL parameter
    $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

    if ($order_id) {
        // Updated query to match your database structure
        $stmt = $koneksi->prepare("SELECT transaction_id FROM orders WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #000000, #1a1a1a);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        .container {
            background: rgba(0, 0, 0, 0.8);
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 90%;
            text-align: center;
            border: 2px solid #FFD700;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
            animation: fadeIn 1s ease-in;
        }

        .checkmark {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #FFD700;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 30px;
            animation: bounceIn 1s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .checkmark::after {
            content: '✓';
            font-size: 60px;
            color: #000;
        }

        h1 {
            color: #FFD700;
            margin-bottom: 20px;
            font-size: 2.5em;
            animation: slideInDown 1s ease-out;
        }

        p {
            margin: 15px 0;
            line-height: 1.6;
            font-size: 1.2em;
            animation: fadeIn 1.5s ease-in;
        }

        .order-number {
            background: rgba(255, 215, 0, 0.1);
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px solid #FFD700;
            animation: pulse 2s infinite;
        }

        .transaction-id {
            background: rgba(255, 215, 0, 0.15);
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px solid #FFD700;
            animation: fadeIn 1.5s ease-in;
        }

        .transaction-number {
            font-family: monospace;
            font-size: 1.2em;
            color: #FFD700;
            letter-spacing: 1px;
            animation: pulse 2s infinite;
        }

        .thank-you {
            color: #FFD700;
            font-size: 1.5em;
            margin-top: 30px;
            animation: fadeIn 2s ease-in;
        }

        .back-button {
            display: inline-block;
            margin-top: 40px;
            padding: 15px 40px;
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #000;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            font-size: 1.1em;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            animation: slideInUp 1s ease-out;
        }

        .back-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 215, 0, 0.3);
        }

        .back-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.2),
                    transparent);
            transition: 0.5s;
        }

        .back-button:hover::before {
            left: 100%;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideInUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .shipping-details {
            margin-top: 30px;
            padding: 20px;
            border-top: 1px solid rgba(255, 215, 0, 0.3);
            animation: fadeIn 2.5s ease-in;
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 2em;
            }

            p {
                font-size: 1em;
            }

            .back-button {
                padding: 12px 30px;
                font-size: 1em;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="checkmark"></div>
        <h1 class="animate__animated animate__fadeInDown">Order Confirmed!</h1>

        <?php if (isset($order) && $order): ?>
            <div class="transaction-id">
                <p>Transaction ID:</p>
                <p class="transaction-number"><?php echo htmlspecialchars($order['transaction_id']); ?></p>
            </div>
        <?php endif; ?>

        <div class="order-number">
            <p>Order #: ORD-<?php echo date('Y-m-d'); ?>-<?php echo sprintf('%03d', isset($order_id) ? $order_id : '001'); ?></p>
        </div>
        <p>Your order has been successfully confirmed and is now being processed.</p>
        <div class="shipping-details">
            <p>Your items will be shipped soon.</p>
            <p>You will receive a tracking number via email once your package is dispatched.</p>
        </div>
        <p class="thank-you">Thank you for shopping with us!</p>
        <a href="../index.php" class="back-button">Back to Home</a>
    </div>
</body>

</html>