<?php
// Start session for user authentication
session_start();
require_once '../includes/config.php';

// Periksa apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");  // Sesuaikan dengan nama file login Anda
    exit();
}

// Database connection - using the connection from config.php
// Assuming $koneksi is your PDO connection from config.php
$pdo = $koneksi; // Use the existing connection


// Handle AJAX requests
// Handle AJAX requests delete di bagian atas file
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'delete_product':
            if (isset($_GET['id'])) {
                try {
                    // Get image path before deleting
                    $stmt = $pdo->prepare("SELECT gambar FROM produk WHERE produk_id = ?");
                    $stmt->execute([$_GET['id']]);
                    $gambar = $stmt->fetchColumn();

                    // Delete from database
                    $stmt = $pdo->prepare("DELETE FROM produk WHERE produk_id = ?");
                    $success = $stmt->execute([$_GET['id']]);

                    // Delete image if exists
                    if ($gambar && file_exists('../uploads/' . $gambar)) {
                        unlink('../images/' . $gambar);
                    }

                    echo json_encode(['success' => true]);
                } catch (PDOException $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
            }
            exit;
    }
}

// update order status
if (isset($_POST['action']) && $_POST['action'] === 'update_order_status') {
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $success = $stmt->execute([$_POST['status'], $_POST['order_id']]);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Handle ajax request update
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'get_product':
            if (isset($_GET['id'])) {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM produk WHERE produk_id = ?");
                    $stmt->execute([$_GET['id']]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($product) {
                        echo json_encode($product);
                    } else {
                        echo json_encode(['error' => 'Product not found']);
                    }
                } catch (PDOException $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                }
                exit;
            }
            break;
    }
}

// Function to get dashboard statistics
function getDashboardStats($pdo)
{
    try {
        $stats = [];

        // Get total products
        $stmt = $pdo->query("SELECT COUNT(*) FROM produk");
        $stats['total_products'] = (int) $stmt->fetchColumn();

        // Get total users
        $stmt = $pdo->query("SELECT COUNT(*) FROM pelanggan");
        $stats['total_users'] = (int) $stmt->fetchColumn();

        // Get pending orders
        $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
        $stats['pending_orders'] = (int) $stmt->fetchColumn();

        // Get total revenue
        $stmt = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'");
        $stats['total_revenue'] = (float) ($stmt->fetchColumn() ?: 0); // Use 0 if null

        // Get total messages
        $stmt = $pdo->query("SELECT COUNT(*) FROM messages");
        $stats['total_messages'] = (int) $stmt->fetchColumn(); // Menambahkan total messages

        return $stats;
    } catch (PDOException $e) {
        error_log("Error getting dashboard stats: " . $e->getMessage());
        return [
            'total_products' => 0,
            'total_users' => 0,
            'pending_orders' => 0,
            'total_revenue' => 0,
            'total_messages' => 0 // Menambahkan default untuk total messages
        ];
    }
}


// Function to handle file upload
// Function to handle file upload
function handleFileUpload($file)
{
    if (!isset($file) || $file['error'] !== 0) {
        return '';
    }

    $upload_dir = '../images/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Get original filename and clean it
    $file_name = basename($file["name"]); // Use original filename

    $target_file = $upload_dir . $file_name;

    // If file already exists, don't rename it
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $file_name;
    }

    throw new Exception('Failed to upload file');
}

// Function to handle adding new product
function handleAddProduct($pdo)
{
    try {
        $gambar = '';
        if (isset($_FILES['gambar'])) {
            $gambar = handleFileUpload($_FILES['gambar']);
        }

        $stmt = $pdo->prepare("
            INSERT INTO produk (nama_produk, deskripsi, harga, stok, brand, gambar) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_POST['nama_produk'],
            $_POST['deskripsi'],
            $_POST['harga'],
            $_POST['stok'],
            $_POST['brand'],
            $gambar
        ]);

        $_SESSION['success'] = "Product added successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error adding product: " . $e->getMessage();
    }
}

// Function to handle updating product
function handleUpdateProduct($pdo)
{
    try {
        // Get current image
        $stmt = $pdo->prepare("SELECT gambar FROM produk WHERE produk_id = ?");
        $stmt->execute([$_POST['produk_id']]);
        $current_gambar = $stmt->fetchColumn();

        // Handle new image if uploaded
        $gambar = $current_gambar;
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
            $gambar = handleFileUpload($_FILES['gambar']);

            // Delete old image
            if ($current_gambar && file_exists('../images/' . $current_gambar)) {
                unlink('../images/' . $current_gambar);
            }
        }

        $stmt = $pdo->prepare("
            UPDATE produk 
            SET nama_produk = ?,
                deskripsi = ?,
                harga = ?,
                stok = ?,
                brand = ?,
                gambar = ?
            WHERE produk_id = ?
        ");

        $success = $stmt->execute([
            $_POST['nama_produk'],
            $_POST['deskripsi'],
            $_POST['harga'],
            $_POST['stok'],
            $_POST['brand'],
            $gambar,
            $_POST['produk_id']
        ]);

        // Send proper JSON response
        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update product']);
        }
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Function to get product details
function getProduct($pdo, $id)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM produk WHERE produk_id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            echo json_encode($product);
        } else {
            echo json_encode(['error' => 'Product not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Function to delete product
// Function to delete product
function deleteProduct($pdo, $id)
{
    try {
        // Get image path before deleting
        $stmt = $pdo->prepare("SELECT gambar FROM produk WHERE produk_id = ?");
        $stmt->execute([$id]);
        $gambar = $stmt->fetchColumn();

        // Delete product from database
        $stmt = $pdo->prepare("DELETE FROM produk WHERE produk_id = ?");
        $success = $stmt->execute([$id]);

        // Delete image file if exists
        if ($success && $gambar && file_exists('../uploads/' . $gambar)) {
            unlink('../uploads/' . $gambar);
        }

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
        }
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Function to get all products
function getAllProducts($pdo)
{
    try {
        $stmt = $pdo->query("SELECT * FROM produk ORDER BY produk_id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting products: " . $e->getMessage());
        return [];
    }
}


if (isset($_GET['action'])) {
    switch ($_GET['action']) {
            // ... existing cases ...

        case 'get_user':
            if (isset($_GET['id'])) {
                $user = getUser($pdo, $_GET['id']);
                echo json_encode($user);
                exit;
            }
            break;

        case 'delete_user':
            if (isset($_GET['id'])) {
                handleDeleteUser($pdo, $_GET['id']);
            }
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
                // ... existing cases ...

            case 'update_user':
                handleUpdateUser($pdo);
                break;
        }
    }
}
// Function to get users list
// Function to get user details
// Function to get all users (for the list)
function getUsers($pdo)
{
    try {
        $stmt = $pdo->query("SELECT * FROM pelanggan ORDER BY pelanggan_id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting users: " . $e->getMessage());
        return [];
    }
}

// Function to get single user (for editing)
function getUser($pdo, $id)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE pelanggan_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Function to update user
function handleUpdateUser($pdo)
{
    try {
        $userId = $_POST['pelanggan_id'];
        $password = $_POST['password'];

        // Start with basic fields
        $fields = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'role' => $_POST['role'],
            'alamat' => $_POST['alamat']
        ];

        // If password is provided, update it
        if (!empty($password)) {
            $fields['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Build the SQL query dynamically
        $sql = "UPDATE pelanggan SET ";
        $updates = [];
        $params = [];

        foreach ($fields as $key => $value) {
            if (!empty($value) || $value === '0') {
                $updates[] = "$key = ?";
                $params[] = $value;
            }
        }

        $sql .= implode(', ', $updates);
        $sql .= " WHERE pelanggan_id = ?";
        $params[] = $userId;

        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute($params);

        echo json_encode(['success' => $success]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Function to delete user
function handleDeleteUser($pdo, $id)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM pelanggan WHERE pelanggan_id = ?");
        $success = $stmt->execute([$id]);

        echo json_encode(['success' => $success]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Function to get orders list

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
            // ... existing cases ...

        case 'get_order_details':
            if (isset($_GET['id'])) {
                $details = getOrderDetails($pdo, $_GET['id']);
                echo json_encode($details);
                exit;
            }
            break;
    }
}
// Fungsi untuk mendapatkan detail pesanan
function getOrderDetails($pdo, $orderId = null)
{
    try {
        // Jika tidak ada orderId yang diberikan, dapatkan semua pesanan
        if ($orderId === null) {
            $stmt = $pdo->prepare("
                SELECT o.*, p.username as customer_name 
                FROM orders o 
                JOIN pelanggan p ON o.pelanggan_id = p.pelanggan_id 
                ORDER BY o.order_id DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Dapatkan info pesanan dasar untuk pesanan tertentu
        $stmt = $pdo->prepare("
            SELECT o.*, p.username as customer_name 
            FROM orders o 
            JOIN pelanggan p ON o.pelanggan_id = p.pelanggan_id 
            WHERE o.order_id = ?
        ");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return ['error' => 'Order not found'];
        }

        // Dapatkan item pesanan dengan detail produk
        $stmt = $pdo->prepare("
            SELECT oi.*, p.nama_produk, p.gambar
            FROM order_items oi
            JOIN produk p ON oi.produk_id = p.produk_id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'order' => $order,
            'items' => $items
        ];
    } catch (PDOException $e) {
        error_log("Error getting order details: " . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}

// Handle AJAX request untuk mendapatkan detail pesanan
if (isset($_GET['action']) && $_GET['action'] === 'get_order_details') {
    if (isset($_GET['id'])) {
        $details = getOrderDetails($pdo, $_GET['id']);
        echo json_encode($details);
        exit;
    } else {
        echo json_encode(['error' => 'Order ID not provided']);
        exit;
    }
}

// Function to get status badge class
function getStatusBadgeClass($status)
{
    $statusClasses = [
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'completed' => 'success',
        'cancelled' => 'danger'
    ];

    return isset($statusClasses[$status]) ? $statusClasses[$status] : 'secondary';
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_product':
                handleAddProduct($pdo);
                break;
            case 'update_product':
                handleUpdateProduct($pdo);
                break;
            case 'add_user':
                try {
                    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("
                        INSERT INTO pelanggan (username, email, password, role, alamat) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $_POST['username'],
                        $_POST['email'],
                        $hashed_password,
                        $_POST['role'],
                        $_POST['alamat']
                    ]);
                    $_SESSION['success'] = "User added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error adding user: " . $e->getMessage();
                }
                break;
        }

        // Only redirect if it's not an AJAX request
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

// Add this to your stats calculation
try {
    // Pastikan koneksi database sudah dibuat
    if (!isset($koneksi)) {
        throw new Exception("Database connection not established");
    }

    // Gunakan prepared statement untuk keamanan
    $query = "SELECT COUNT(*) as total_messages FROM messages";
    $stmt = $koneksi->prepare($query);
    $stmt->execute();

    // Fetch result
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_messages'] = (int) ($row['total_messages'] ?? 0); // Pastikan hasilnya adalah integer
} catch (Exception $e) {
    error_log("Error calculating stats: " . $e->getMessage());
    $stats['total_messages'] = 0;
}

// Logika untuk menghapus pesan
if (isset($_POST['message_id'])) {
    $message_id = htmlspecialchars($_POST['message_id']); // Gunakan htmlspecialchars untuk sanitasi
    $query = "DELETE FROM messages WHERE message_id = :message_id";

    $stmt = $koneksi->prepare($query);
    $stmt->bindParam(':message_id', $message_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}





// Get data for dashboard
$stats = getDashboardStats($pdo);
$products = getAllProducts($pdo);
$users = getUsers($pdo);
$orders = getOrderDetails($pdo);
$stats = getDashboardStats($koneksi);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Toko Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
            width: 100%;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            transition: all 0.3s;
            z-index: 999;
            color: white;
            padding: 20px;
        }

        #sidebar.active {
            margin-left: -250px;
        }

        #content {
            width: calc(100% - 250px);
            margin-left: 250px;
            transition: all 0.3s;
        }

        #content.active {
            width: 100%;
            margin-left: 0;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: #343a40;
            border-bottom: 1px solid #47484a;
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul li a {
            padding: 10px;
            font-size: 1.1em;
            display: block;
            color: #ffffff;
            text-decoration: none;
            transition: 0.3s;
        }

        #sidebar ul li a:hover {
            color: #7386D5;
            background: rgba(255, 255, 255, 0.1);
        }

        #sidebar ul li.active>a {
            color: #007bff;
            background: rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <nav id="sidebar" class="bg-dark">
            <div class="sidebar-header">
                <h3><i class="bi bi-shop"></i> EliteWatch</h3>
            </div>
            <ul class="list-unstyled components">
                <li class="active">
                    <a href="#dashboard" data-bs-toggle="tab"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <li>
                    <a href="#products" data-bs-toggle="tab"><i class="bi bi-box-seam"></i> Product Management</a>
                </li>
                <li>
                    <a href="#users" data-bs-toggle="tab"><i class="bi bi-people"></i> User Management</a>
                </li>
                <li>
                    <a href="#orders" data-bs-toggle="tab"><i class="bi bi-cart-check"></i> Order Management</a>
                </li>
                <li>
                    <a href="#messages" data-bs-toggle="tab"><i class="bi bi-envelope"></i> Messages</a>
                </li>
            </ul>
        </nav>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="ms-auto">
                        <a href="../logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="tab-content">
                    <!-- Dashboard Tab -->
                    <div class="tab-pane active" id="dashboard">
                        <h1 class="mb-4">Admin Dashboard</h1>
                        <div class="row">
                            <div class="col-md-3 mb-4">
                                <div class="card text-white bg-primary">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Products</h5>
                                        <p class="card-text display-4"><?php echo $stats['total_products']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card text-white bg-success">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Users</h5>
                                        <p class="card-text display-4"><?php echo $stats['total_users']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card text-white bg-warning">
                                    <div class="card-body">
                                        <h5 class="card-title">Pending Orders</h5>
                                        <p class="card-text display-4"><?php echo $stats['pending_orders']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card text-white bg-danger">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Revenue</h5>
                                        <p class="card-text display-4">Rp.<?php echo number_format($stats['total_revenue']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card text-white bg-info">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Messages</h5>
                                        <p class="card-text display-4">
                                            <?= htmlspecialchars($stats['total_messages'] ?? 0) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Tab -->
                    <div class="tab-pane" id="products">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>Product Management</h1>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="bi bi-plus"></i> Add Product
                            </button>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Brand</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($product['produk_id']); ?></td>
                                                <td><?php echo htmlspecialchars($product['nama_produk']); ?></td>
                                                <td>Rp.<?php echo number_format($product['harga']); ?></td>
                                                <td><?php echo htmlspecialchars($product['stok']); ?></td>
                                                <td><?php echo htmlspecialchars($product['brand']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="editProduct(<?php echo $product['produk_id']; ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['produk_id']; ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Add Product Modal -->
                    <div class="modal fade" id="addProductModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add New Product</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="action" value="add_product">
                                        <div class="mb-3">
                                            <label class="form-label">Product Name</label>
                                            <input type="text" name="nama_produk" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Price</label>
                                            <input type="number" name="harga" class="form-control" step="0.01" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Stock</label>
                                            <input type="number" name="stok" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Brand</label>
                                            <input type="text" name="brand" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Image</label>
                                            <input type="file" name="gambar" class="form-control">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Add Product</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Users Tab -->
                    <div class="tab-pane" id="users">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>User Management</h1>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['pelanggan_id']); ?></td>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="editUser(<?php echo $user['pelanggan_id']; ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['pelanggan_id']; ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editUserForm">
                                        <input type="hidden" name="action" value="update_user">
                                        <input type="hidden" id="edit_pelanggan_id" name="pelanggan_id">
                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" id="edit_username" name="username" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" id="edit_email" name="email" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password (leave blank to keep current)</label>
                                            <input type="password" id="edit_password" name="password" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Role</label>
                                            <select id="edit_role" name="role" class="form-select" required>
                                                <option value="user">User</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea id="edit_alamat" name="alamat" class="form-control"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="updateUser()">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div class="tab-pane" id="orders">
                        <h1 class="mb-4">Order Management</h1>
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Order Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                <td>Rp.<?php echo number_format($order['total_amount']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo getStatusBadgeClass($order['status']); ?>">
                                                        <?php echo htmlspecialchars($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" onclick="viewOrder(<?php echo $order['order_id']; ?>)">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-primary" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>)">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_product">
                        <input type="hidden" id="edit_produk_id" name="produk_id">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" id="edit_nama_produk" name="nama_produk" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea id="edit_deskripsi" name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" id="edit_harga" name="harga" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" id="edit_stok" name="stok" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Brand</label>
                            <input type="text" id="edit_brand" name="brand" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="gambar" class="form-control">
                            <div id="current_image" class="mt-2"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="updateProduct()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>


    <!-- View Order Modal -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="orderDetails">
                        <!-- Order details will be inserted here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates (outside the modal, or at least outside the orderDetails container) -->
    <template id="orderDetailsTemplate">
        <div>
            <p>Order ID: <span id="order-id"></span></p>
            <p>Customer Name: <span id="customer-name"></span></p>
            <p>Order Date: <span id="order-date"></span></p>
            <p>Status: <span id="order-status"></span></p>
            <p>Total: Rp.<span id="order-total"></span></p>
            <p>Items Total: Rp.<span id="items-total"></span></p>

            <h5>Order Items:</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="order-items">
                    <!-- Item rows will be inserted here -->
                </tbody>
            </table>
        </div>
    </template>

    <template id="orderItemTemplate">
        <tr>
            <td class="product-name"></td>
            <td class="product-image"></td>
            <td class="product-price"></td>
            <td class="product-quantity"></td>
            <td class="product-subtotal"></td>
        </tr>
    </template>

    <!-- Update Order Status Modal -->
    <div class="modal fade" id="updateOrderStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="updateOrderStatusForm">
                        <input type="hidden" name="order_id" id="statusOrderId">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Tab -->
    <div class="tab-pane fade" id="messages">
        <div class="container-fluid">
            <h1 class="mb-4">Message Management</h1>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <th>Sent At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $query = "SELECT * FROM messages ORDER BY sent_at DESC";
                                    $stmt = $koneksi->query($query);

                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['message_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['sent_at']) . "</td>";
                                        echo "<td>
                                            <button class='btn btn-sm btn-danger delete-message' data-id='" . $row['message_id'] . "'>
                                                <i class='bi bi-trash'></i>
                                            </button>
                                        </td>";
                                        echo "</tr>";
                                    }
                                } catch (PDOException $e) {
                                    echo "<tr><td colspan='6' class='text-center'>Error loading messages</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebarToggle = document.getElementById('sidebarCollapse');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');

            sidebarToggle?.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                content.classList.toggle('active');
            });

            // Tab switching without page reload
            const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetTab = this.getAttribute('href');

                    // Remove active and show classes from all tab panes
                    const tabPanes = document.querySelectorAll('.tab-pane');
                    tabPanes.forEach(pane => {
                        pane.classList.remove('active', 'show');
                    });

                    // Add active and show classes to target tab
                    const targetPane = document.querySelector(targetTab);
                    if (targetPane) {
                        targetPane.classList.add('active', 'show');
                    }

                    // Update active sidebar link
                    tabLinks.forEach(l => l.parentElement.classList.remove('active'));
                    this.parentElement.classList.add('active');
                });
            });

            // Message deletion handling
            const deleteMessageButtons = document.querySelectorAll('.delete-message');
            deleteMessageButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Are you sure you want to delete this message?')) {
                        const messageId = this.dataset.id;

                        fetch('delete_message.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    message_id: messageId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    location.reload();
                                } else {
                                    alert('Error deleting message');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error deleting message');
                            });
                    }
                });
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }, 5000);
            });
        });

        // Product Management Functions
        function editProduct(productId) {
            // Implement edit product functionality
            console.log('Editing product:', productId);
        }

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                // Implement delete product functionality
                console.log('Deleting product:', productId);
            }
        }

        // User Management Functions
        // Replace the existing editUser and deleteUser functions with these:
        function editUser(userId) {
            fetch(`admin.php?action=get_user&id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('Error: ' + data.error);
                        return;
                    }

                    // Fill form fields with user data
                    document.getElementById('edit_pelanggan_id').value = data.pelanggan_id;
                    document.getElementById('edit_username').value = data.username;
                    document.getElementById('edit_email').value = data.email;
                    document.getElementById('edit_role').value = data.role;
                    document.getElementById('edit_alamat').value = data.alamat || '';

                    // Clear password field as it's optional
                    document.getElementById('edit_password').value = '';

                    // Show modal
                    const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    editModal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching user data');
                });
        }

        function updateUser() {
            const form = document.getElementById('editUserForm');
            const formData = new FormData(form);

            fetch('admin.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User updated successfully');
                        const editModal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                        editModal.hide();
                        location.reload();
                    } else {
                        alert('Error updating user: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating user');
                });
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                fetch(`admin.php?action=delete_user&id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('User deleted successfully');
                            location.reload();
                        } else {
                            alert('Error deleting user: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting user');
                    });
            }
        }

        // Fungsi untuk menampilkan detail pesanan
        function viewOrder(orderId) {
            fetch(`admin.php?action=get_order_details&id=${orderId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        alert('Error: ' + data.error);
                        return;
                    }

                    const order = data.order;
                    const items = data.items;

                    // Pastikan elemen dengan ID 'orderDetailsTemplate' ada
                    const orderTemplate = document.getElementById('orderDetailsTemplate');
                    if (!orderTemplate) {
                        console.error('Element with ID "orderDetailsTemplate" not found');
                        alert('Error: Order details template not found.');
                        return;
                    }

                    // Clone template
                    const orderTemplateContent = orderTemplate.content.cloneNode(true);

                    // Isi detail pesanan
                    orderTemplateContent.getElementById('order-id').textContent = order.order_id;
                    orderTemplateContent.getElementById('customer-name').textContent = order.customer_name;
                    orderTemplateContent.getElementById('order-date').textContent = new Date(order.order_date).toLocaleString();

                    // Set status pesanan dengan badge
                    const statusSpan = orderTemplateContent.getElementById('order-status');
                    statusSpan.innerHTML = `<span class="badge bg-${getStatusBadgeClass(order.status)}">${order.status}</span>`;

                    orderTemplateContent.getElementById('order-total').textContent = parseFloat(order.total_amount).toFixed(2);
                    orderTemplateContent.getElementById('items-total').textContent = parseFloat(order.total_amount).toFixed(2);

                    // Dapatkan elemen tbody untuk item
                    const itemsContainer = orderTemplateContent.getElementById('order-items');
                    if (!itemsContainer) {
                        console.error('Element with ID "order-items" not found');
                        alert('Error: Order items container not found.');
                        return;
                    }

                    const itemTemplate = document.getElementById('orderItemTemplate');
                    if (!itemTemplate) {
                        console.error('Element with ID "orderItemTemplate" not found');
                        alert('Error: Order item template not found.');
                        return;
                    }

                    // Tambahkan setiap item pesanan
                    items.forEach(item => {
                        const itemRow = itemTemplate.content.cloneNode(true);
                        const subtotal = parseFloat(item.price_per_item) * parseInt(item.quantity);

                        itemRow.querySelector('.product-name').textContent = item.nama_produk;
                        itemRow.querySelector('.product-image').innerHTML = item.gambar ?
                            `<img src="../images/${item.gambar}" alt="${item.nama_produk}" style="max-width: 50px;">` :
                            'No image';
                        itemRow.querySelector('.product-price').textContent = `$${parseFloat(item.price_per_item).toFixed(2)}`;
                        itemRow.querySelector('.product-quantity').textContent = item.quantity;
                        itemRow.querySelector('.product-subtotal').textContent = `$${subtotal.toFixed(2)}`;

                        itemsContainer.appendChild(itemRow);
                    });

                    // Update konten modal dan tampilkan
                    const orderDetails = document.getElementById('orderDetails');
                    if (!orderDetails) {
                        console.error('Element with ID "orderDetails" not found');
                        alert('Error: Order details container not found.');
                        return;
                    }
                    orderDetails.innerHTML = ''; // Bersihkan konten sebelumnya
                    orderDetails.appendChild(orderTemplateContent);

                    const modal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching order details: ' + error.message);
                });
        }

        // Fungsi untuk mendapatkan kelas badge status
        function getStatusBadgeClass(status) {
            const statusClasses = {
                'pending': 'warning',
                'processing': 'info',
                'shipped': 'primary',
                'completed': 'success',
                'cancelled': 'danger'
            };

            return statusClasses[status] || 'secondary';
        }


        function updateOrderStatus(orderId) {
            document.getElementById('statusOrderId').value = orderId;
            const modal = new bootstrap.Modal(document.getElementById('updateOrderStatusModal'));
            modal.show();
        }

        // Add this new code for form submission
        document.getElementById('updateOrderStatusForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'update_order_status');

            fetch('admin.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order status updated successfully');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('updateOrderStatusModal'));
                        modal.hide();
                        location.reload();
                    } else {
                        alert('Error updating order status: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating order status');
                });
        });

        function getStatusBadgeClass(status) {
            const statusClasses = {
                'pending': 'warning',
                'processing': 'info',
                'shipped': 'primary',
                'completed': 'success',
                'cancelled': 'danger'
            };
            return statusClasses[status] || 'secondary';
        }

        function editProduct(productId) {
            fetch(`admin.php?action=get_product&id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('Error: ' + data.error);
                        return;
                    }

                    // Fill form fields with product data
                    document.getElementById('edit_produk_id').value = data.produk_id;
                    document.getElementById('edit_nama_produk').value = data.nama_produk;
                    document.getElementById('edit_deskripsi').value = data.deskripsi;
                    document.getElementById('edit_harga').value = data.harga;
                    document.getElementById('edit_stok').value = data.stok;
                    document.getElementById('edit_brand').value = data.brand;

                    // Show current image if exists
                    const currentImage = document.getElementById('current_image');
                    if (data.gambar) {
                        currentImage.innerHTML = `<img src="../uploads/${data.gambar}" alt="Current image" style="max-width: 200px;">`;
                    } else {
                        currentImage.innerHTML = 'No image uploaded';
                    }

                    // Show modal
                    const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
                    editModal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching product data');
                });
        }

        function updateProduct() {
            const form = document.getElementById('editProductForm');
            const formData = new FormData(form);
            formData.append('action', 'update_product');

            fetch('admin.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product updated successfully');
                        const editModal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                        editModal.hide();
                        location.reload();
                    } else {
                        alert('Error updating product: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating product');
                });
        }

        // Function to delete product
        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                fetch(`admin.php?action=delete_product&id=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Product deleted successfully');
                            location.reload();
                        } else {
                            alert('Error deleting product: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting product');
                    });
            }
        }

        // Add this to your existing JavaScript
        $(document).ready(function() {
            $('.delete-message').click(function() {
                if (confirm('Are you sure you want to delete this message?')) {
                    var messageId = $(this).data('id');
                    $.ajax({
                        url: 'delete_message.php',
                        type: 'POST',
                        data: {
                            message_id: messageId
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Error deleting message');
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>