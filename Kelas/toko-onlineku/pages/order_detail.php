<?php
// Start session
session_start();

// Include the database configuration file
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['pelanggan_id'])) {
    header('Location: login.php');
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: history.php');
    exit();
}

$order_id = $_GET['id'];

// Get customer ID from session
$pelanggan_id = $_SESSION['pelanggan_id'];

try {
    // Ensure the order belongs to the logged-in user
    $stmt = $koneksi->prepare("SELECT * FROM orders WHERE order_id = :order_id AND pelanggan_id = :pelanggan_id");
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->bindParam(':pelanggan_id', $pelanggan_id, PDO::PARAM_INT);
    $stmt->execute();

    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        // Order not found or doesn't belong to this user
        header('Location: history.php');
        exit();
    }

    // Fetch order items with product details
    $sql = "SELECT oi.*, p.nama_produk, p.gambar, p.brand, p.harga
            FROM order_items oi 
            JOIN produk p ON oi.produk_id = p.produk_id 
            WHERE oi.order_id = :order_id";

    $stmt = $koneksi->prepare($sql);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all items

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    die();
}

// Function to get status badge class
function getStatusBadgeClass($status)
{
    switch ($status) {
        case 'pending':
            return 'bg-warning';
        case 'processing':
            return 'bg-info';
        case 'shipped':
            return 'bg-primary';
        case 'completed':
            return 'bg-success';
        case 'cancelled':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

// Function to get step status
function getStepStatus($orderStatus, $step)
{
    $steps = [
        'pending' => 1,
        'processing' => 2,
        'shipped' => 3,
        'completed' => 4
    ];

    $currentStep = $steps[$orderStatus] ?? 0;
    $stepNumber = $steps[$step] ?? 0;

    if ($orderStatus === 'cancelled') {
        return 'cancelled';
    } elseif ($currentStep > $stepNumber) {
        return 'completed';
    } elseif ($currentStep === $stepNumber) {
        return 'active';
    } else {
        return 'pending';
    }
}

// Function to format date
function formatDate($dateString)
{
    $date = new DateTime($dateString);
    return $date->format('d M Y, H:i');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?php echo $order_id; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --info-color: #4895ef;
            --warning-color: #f72585;
            --danger-color: #e63946;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: #424242;
        }

        .container {
            max-width: 1200px;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.1);
        }

        .status-badge {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 30px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            border-bottom: none;
            padding: 20px 25px;
            font-weight: 600;
        }

        .product-item {
            border-bottom: 1px solid #eef0f5;
            padding: 20px 25px;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            border: none;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .product-placeholder {
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #eef0f5;
            border-radius: 12px;
            color: #adb5bd;
        }

        .progress-track {
            margin: 50px 0;
            position: relative;
        }
        
        .progress-line {
            position: absolute;
            top: 18px;
            left: 10%;
            right: 10%;
            height: 3px;
            background-color: #e9ecef;
            z-index: 0;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            z-index: 1;
            color: #6c757d;
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 3px solid white;
            transition: all 0.3s ease;
        }

        .step-completed .step-icon {
            background-color: #4cc9f0;
            color: white;
            transform: scale(1.1);
        }

        .step-active .step-icon {
            background-color: var(--primary-color);
            color: white;
            transform: scale(1.2);
            animation: pulse 2s infinite;
        }

        .step-cancelled .step-icon {
            background-color: var(--danger-color);
            color: white;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(67, 97, 238, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(67, 97, 238, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(67, 97, 238, 0);
            }
        }

        .completed-line {
            position: absolute;
            top: 18px;
            height: 3px;
            background-color: #4cc9f0;
            z-index: 0;
            transition: width 0.5s ease;
        }

        .step-text {
            color: #6c757d;
            font-size: 0.85rem;
            text-align: center;
            font-weight: 500;
            margin-top: 5px;
        }

        .step-completed .step-text,
        .step-active .step-text,
        .step-cancelled .step-text {
            font-weight: 600;
            color: #212529;
        }

        .order-summary {
            background-color: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }

        .summary-total {
            font-size: 1.25rem;
            font-weight: 700;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            color: var(--primary-color);
        }

        .btn-back {
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            background-color: white;
            color: var(--dark-color);
            border: 2px solid transparent;
        }

        .btn-back:hover {
            background-color: var(--light-color);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .alert-cancelled {
            background-color: rgba(230, 57, 70, 0.1);
            border-left: 5px solid var(--danger-color);
            color: var(--danger-color);
            border-radius: 8px;
            padding: 15px 20px;
        }

        .product-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .brand-tag {
            display: inline-block;
            background-color: rgba(72, 149, 239, 0.1);
            color: var(--info-color);
            border-radius: 30px;
            padding: 3px 10px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .info-card {
            border-radius: 15px;
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .info-label {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark-color);
        }
        
        .address-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 5px;
            font-size: 0.9rem;
        }

        @media (max-width: 767.98px) {
            .page-header {
                padding: 20px;
                margin-bottom: 20px;
            }
            
            .progress-line {
                display: none;
            }
            
            .progress-track {
                display: flex;
                flex-direction: column;
                margin: 20px 0;
            }
            
            .progress-step {
                flex-direction: row;
                align-items: center;
                margin-bottom: 20px;
                width: 100%;
            }
            
            .step-icon {
                margin-right: 15px;
                margin-bottom: 0;
            }
            
            .step-text {
                text-align: left;
                margin-top: 0;
            }
            
            .product-img,
            .product-placeholder {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 fw-bold">Detail Pesanan #<?php echo $order_id; ?></h2>
                <p class="mb-0 opacity-75">
                    <i class="far fa-calendar-alt me-2"></i><?php echo formatDate($order['order_date']); ?>
                </p>
            </div>
            <a href="history.php" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Order Status Card -->
                <div class="order-card card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Status Pesanan</h5>
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

                        <?php if ($order['status'] !== 'cancelled'): ?>
                            <!-- Progress Tracker -->
                            <div class="progress-track d-flex justify-content-between position-relative">
                                <!-- Background Line -->
                                <div class="progress-line"></div>
                                
                                <?php
                                $currentStep = [
                                    'pending' => 1,
                                    'processing' => 2, 
                                    'shipped' => 3,
                                    'completed' => 4
                                ][$order['status']] ?? 0;
                                
                                // Calculate width for completed line
                                $lineWidth = ($currentStep - 1) * 33.33;
                                if ($lineWidth > 0):
                                ?>
                                <div class="completed-line" style="left: 10%; width: <?php echo $lineWidth; ?>%"></div>
                                <?php endif; ?>
                                
                                <div class="progress-step step-<?php echo getStepStatus($order['status'], 'pending'); ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div class="step-text">Pesanan Dibuat</div>
                                </div>
                                <div class="progress-step step-<?php echo getStepStatus($order['status'], 'processing'); ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <div class="step-text">Diproses</div>
                                </div>
                                <div class="progress-step step-<?php echo getStepStatus($order['status'], 'shipped'); ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                    <div class="step-text">Dikirim</div>
                                </div>
                                <div class="progress-step step-<?php echo getStepStatus($order['status'], 'completed'); ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Selesai</div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert-cancelled mt-3" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-circle fs-4 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Pesanan Dibatalkan</h6>
                                        <p class="mb-0 small">Pesanan ini telah dibatalkan dan tidak akan diproses lebih lanjut.</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="order-card card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Produk dalam Pesanan</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($items): ?>
                            <?php foreach ($items as $item): ?>
                                <div class="product-item d-flex">
                                    <?php if ($item['gambar']): ?>
                                        <img src="../images/<?php echo htmlspecialchars($item['gambar']); ?>" class="product-img" alt="<?php echo htmlspecialchars($item['nama_produk']); ?>">
                                    <?php else: ?>
                                        <div class="product-placeholder">
                                            <i class="fas fa-image fa-2x"></i>
                                        </div>
                                    <?php endif; ?>

                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="fw-bold mb-2"><?php echo htmlspecialchars($item['nama_produk']); ?></h6>
                                        
                                        <?php if ($item['brand']): ?>
                                            <span class="brand-tag mb-2 d-inline-block">
                                                <i class="fas fa-tag me-1"></i> <?php echo htmlspecialchars($item['brand']); ?>
                                            </span>
                                        <?php endif; ?>

                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="text-muted">
                                                <?php echo $item['quantity']; ?> × <span class="fw-medium">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></span>
                                            </span>
                                            <span class="product-price">
                                                Rp <?php echo number_format($item['quantity'] * $item['harga'], 0, ',', '.'); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-4 text-center">
                                <p class="text-muted mb-0">Tidak ada item dalam pesanan ini</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="order-card card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="summary-item">
                            <span>Subtotal Produk</span>
                            <span>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="summary-item">
                            <span>Biaya Pengiriman</span>
                            <span class="text-success fw-medium">Gratis</span>
                        </div>
                        <?php if (isset($order['discount']) && $order['discount'] > 0): ?>
                        <div class="summary-item">
                            <span>Diskon</span>
                            <span class="text-danger">- Rp <?php echo number_format($order['discount'], 0, ',', '.'); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="summary-item summary-total">
                            <span>Total Pembayaran</span>
                            <span>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Info -->
                <div class="order-card card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informasi Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-label">Metode Pembayaran</div>
                        <div class="d-flex align-items-center">
                            <?php 
                            $paymentIcons = [
                                'bank_transfer' => 'fa-university',
                                'credit_card' => 'fa-credit-card',
                                'e-wallet' => 'fa-wallet',
                                'cod' => 'fa-money-bill-wave'
                            ];
                            $paymentMethod = strtolower(str_replace(' ', '_', $order['payment_method'] ?? ''));
                            $icon = $paymentIcons[$paymentMethod] ?? 'fa-credit-card';
                            ?>
                            <span class="fa-stack fa-lg me-2">
                                <i class="fas fa-circle fa-stack-2x text-primary opacity-25"></i>
                                <i class="fas <?php echo $icon; ?> fa-stack-1x text-primary"></i>
                            </span>
                            <span class="fs-5"><?php echo htmlspecialchars($order['payment_method'] ?? 'Tidak Tersedia'); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Info -->
                <div class="order-card card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informasi Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-label">Alamat Pengiriman</div>
                        <div class="address-box">
                            <?php echo htmlspecialchars($order['shipping_address'] ?? 'Tidak Tersedia'); ?>
                        </div>
                        
                        <?php if (isset($order['shipping_method'])): ?>
                        <div class="info-label mt-3">Metode Pengiriman</div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-truck text-primary me-2"></i>
                            <span><?php echo htmlspecialchars($order['shipping_method']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($order['tracking_number'])): ?>
                        <div class="info-label mt-3">Nomor Resi</div>
                        <div class="d-flex align-items-center">
                            <span class="me-2"><?php echo htmlspecialchars($order['tracking_number']); ?></span>
                            <a href="#" class="btn btn-sm btn-outline-primary">Lacak</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Add animations and interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Animate progress line fill
            setTimeout(function() {
                const completedLine = document.querySelector('.completed-line');
                if (completedLine) {
                    completedLine.style.transition = 'width 1.5s ease';
                    completedLine.style.width = completedLine.style.width;
                }
            }, 300);
            
            // Add hover effects on product items
            const productItems = document.querySelectorAll('.product-item');
            productItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
    </script>
</body>

</html>