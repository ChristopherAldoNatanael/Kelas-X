<?php 
    session_start();
    require_once "../dbcontroller.php";
    $db = new DB;

    if (!isset($_SESSION['user'])) {
        header("location:login.php");
    }

    if (isset($_GET['log'])) {
       session_destroy();
       header("location:index.php");
    }

    // Get statistics for dashboard
    function getDashboardStats($db) {
        $stats = [];
        
        // Total menu items
        $sql = "SELECT COUNT(*) as total FROM tblmenu";
        $result = $db->getITEM($sql);
        $stats['menu_count'] = $result['total'];
        
        // Total categories
        $sql = "SELECT COUNT(*) as total FROM tblkategori";
        $result = $db->getITEM($sql);
        $stats['category_count'] = $result['total'];
        
        // Total customers
        $sql = "SELECT COUNT(*) as total FROM tblpelanggan";
        $result = $db->getITEM($sql);
        $stats['customer_count'] = $result['total'];
        
        // Total orders
        $sql = "SELECT COUNT(*) as total FROM tblorder";
        $result = $db->getITEM($sql);
        $stats['order_count'] = $result['total'];
        
        // Total revenue
        $sql = "SELECT SUM(total) as total FROM tblorder";
        $result = $db->getITEM($sql);
        $stats['total_revenue'] = $result['total'] ?? 0;
        
        // Pending orders
        $sql = "SELECT COUNT(*) as total FROM tblorder WHERE status=0";
        $result = $db->getITEM($sql);
        $stats['pending_orders'] = $result['total'];
        
        // Completed orders
        $sql = "SELECT COUNT(*) as total FROM tblorder WHERE status=1";
        $result = $db->getITEM($sql);
        $stats['completed_orders'] = $result['total'];
        
        // Recent orders
        $sql = "SELECT o.*, p.pelanggan FROM tblorder o 
                INNER JOIN tblpelanggan p ON o.idpelanggan = p.idpelanggan 
                ORDER BY o.tglorder DESC LIMIT 5";
        $stats['recent_orders'] = $db->getALL($sql);
        
        // Popular menu items
        $sql = "SELECT m.menu, m.gambar, COUNT(od.idmenu) as order_count 
                FROM tblmenu m 
                INNER JOIN tblorderdetail od ON m.idmenu = od.idmenu 
                GROUP BY m.idmenu 
                ORDER BY order_count DESC 
                LIMIT 5";
        $stats['popular_items'] = $db->getALL($sql);
        
        // Orders by day (last 7 days)
        $sql = "SELECT DATE(tglorder) as order_date, COUNT(*) as count 
                FROM tblorder 
                WHERE tglorder >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                GROUP BY DATE(tglorder) 
                ORDER BY order_date";
        $stats['orders_by_day'] = $db->getALL($sql);
        
        return $stats;
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Warung Aldo</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.css">
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #2e294e;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--secondary) 0%, #1a1627 100%);
            min-height: 100vh;
            position: fixed;
            width: 250px;
            z-index: 999;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s ease;
        }
        
        .sidebar-brand {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-brand-icon {
            color: var(--primary);
            font-size: 2rem;
            margin-right: 0.5rem;
        }
        
        .sidebar-brand-text {
            color: white;
            font-weight: 800;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
        }
        
        .nav-item {
            position: relative;
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--primary);
        }
        
        .nav-link i {
            margin-right: 0.5rem;
            font-size: 0.85rem;
            width: 1.5rem;
            text-align: center;
        }
        
        .active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--primary);
        }
        
        .topbar {
            height: 70px;
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            position: fixed;
            top: 0;
            right: 0;
            left: 250px;
            z-index: 100;
            transition: all 0.3s ease;
        }
        
        .navbar-search {
            width: 25rem;
        }
        
        .search-input {
            border-radius: 20px;
            font-size: 0.85rem;
            background-color: #f8f9fc;
        }
        
        .topbar-divider {
            width: 0;
            border-right: 1px solid #e3e6f0;
            height: 2rem;
            margin: auto 1rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-name {
            color: var(--dark);
            font-weight: 600;
            font-size: 0.85rem;
            margin-right: 0.5rem;
        }
        
        .user-level {
            background-color: var(--primary);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .content {
            margin-left: 250px;
            padding-top: 70px;
            min-height: 100vh;
            padding-bottom: 80px;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .container-fluid {
            flex: 1 0 auto;
        }
        
        .page-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            background-color: white;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .heading-title {
            color: var(--secondary);
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
        }
        
        .content-wrapper {
            background-color: white;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .footer {
            background-color: white;
            padding: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--dark);
            margin-left: 250px;
            box-shadow: 0 -0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            transition: all 0.3s ease;
        }
        
        .logout-btn {
            background-color: var(--danger);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 0.375rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background-color: #d52a1a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .profile-link {
            color: var(--primary);
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .profile-link:hover {
            color: var(--secondary);
            text-decoration: none;
        }
        
        /* Dashboard Styles */
        .stat-card {
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
        }
        
        .stat-card .card-body {
            padding: 1.25rem;
        }
        
        .stat-card-primary {
            border-left: 0.25rem solid var(--primary);
        }
        
        .stat-card-success {
            border-left: 0.25rem solid var(--success);
        }
        
        .stat-card-info {
            border-left: 0.25rem solid var(--info);
        }
        
        .stat-card-warning {
            border-left: 0.25rem solid var(--warning);
        }
        
        .stat-card-danger {
            border-left: 0.25rem solid var(--danger);
        }
        
        .stat-card-secondary {
            border-left: 0.25rem solid var(--secondary);
        }
        
        .stat-card-title {
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }
        
        .stat-card-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0;
        }
        
        .stat-card-icon {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            font-size: 2rem;
            color: rgba(0, 0, 0, 0.1);
        }
        
        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
            width: 100%;
        }
        
        .recent-orders-table th, .recent-orders-table td {
            padding: 0.75rem;
            vertical-align: middle;
        }
        
        .recent-orders-table .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 700;
        }
        
        .recent-orders-table .status-badge-success {
            background-color: rgba(28, 200, 138, 0.1);
            color: var(--success);
        }
        
        .recent-orders-table .status-badge-warning {
            background-color: rgba(246, 194, 62, 0.1);
            color: var(--warning);
        }
        
        .popular-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .popular-item:last-child {
            border-bottom: none;
        }
        
        .popular-item-img {
            width: 50px;
            height: 50px;
            border-radius: 0.25rem;
            object-fit: cover;
            margin-right: 1rem;
        }
        
        .popular-item-name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }
        
        .popular-item-count {
            font-size: 0.75rem;
            color: var(--primary);
            font-weight: 700;
        }
        
        .popular-item-badge {
            margin-left: auto;
            background-color: rgba(78, 115, 223, 0.1);
            color: var(--primary);
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 700;
        }
        
        .dashboard-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .dashboard-section-title i {
            margin-right: 0.5rem;
            color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100px;
            }
            
            .sidebar-brand-text {
                display: none;
            }
            
            .nav-link span {
                display: none;
            }
            
            .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .topbar, .content, .footer {
                left: 100px;
                margin-left: 100px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fas fa-utensils"></i>
            </div>
            <div class="sidebar-brand-text">Warung Aldo</div>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <div class="sidebar-divider my-3"></div>
            
            <?php 
                $level = $_SESSION['level'];
                switch ($level) {
                    case 'admin':
                        echo '
                        <li class="nav-item">
                            <a class="nav-link" href="?f=kategori&m=select">
                                <i class="fas fa-fw fa-list"></i>
                                <span>Kategori</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?f=menu&m=select">
                                <i class="fas fa-fw fa-utensils"></i>
                                <span>Menu</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?f=pelanggan&m=select">
                                <i class="fas fa-fw fa-users"></i>
                                <span>Pelanggan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?f=order&m=select">
                                <i class="fas fa-fw fa-shopping-cart"></i>
                                <span>Order</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?f=orderdetail&m=select">
                                <i class="fas fa-fw fa-receipt"></i>
                                <span>Order Detail</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?f=user&m=select">
                                <i class="fas fa-fw fa-user-shield"></i>
                                <span>User</span>
                            </a>
                        </li>
                        ';
                        break;

                    case 'kasir':
                        echo '
                        <li class="nav-item">
                            <a class="nav-link" href="?f=order&m=select">
                                <i class="fas fa-fw fa-shopping-cart"></i>
                                <span>Order</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?f=orderdetail&m=select">
                                <i class="fas fa-fw fa-receipt"></i>
                                <span>Order Detail</span>
                            </a>
                        </li>
                        ';
                        break;
                        
                    case 'koki':
                        echo '   
                        <li class="nav-item">
                            <a class="nav-link" href="?f=orderdetail&m=select">
                                <i class="fas fa-fw fa-receipt"></i>
                                <span>Order Detail</span>
                            </a>
                        </li>
                        ';
                        break;
                        
                    default:
                        echo "<li class='nav-item'><a class='nav-link'><i class='fas fa-fw fa-exclamation-circle'></i><span>Tidak Ada Menu</span></a></li>";  
                        break;
                }
            ?>
        </ul>
    </div>

    <!-- Topbar -->
    <div class="topbar">
        <div class="container-fluid d-flex justify-content-between align-items-center h-100 px-4">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="d-flex align-items-center">
                <div class="user-info">
                    <span class="user-name"><?php echo $_SESSION['user']; ?></span>
                    <span class="user-level"><?php echo $_SESSION['level']; ?></span>
                </div>
                
                <div class="topbar-divider"></div>
                
                <a href="?f=user&m=updateuser&id=<?php echo $_SESSION['iduser'] ?>" class="btn btn-link profile-link">
                    <i class="fas fa-user-circle mr-1"></i> Profil
                </a>
                
                <a href="?log=logout" class="btn logout-btn ml-3">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="container-fluid">
            <?php if (!isset($_GET['f']) && !isset($_GET['m'])): ?>
                <?php $stats = getDashboardStats($db); ?>
                
                <div class="page-heading">
                    <h1 class="heading-title">Dashboard</h1>
                    <div class="d-flex align-items-center">
                        <span class="text-muted mr-2">Hari ini:</span>
                        <span class="font-weight-bold"><?php echo date('d F Y'); ?></span>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card stat-card-primary">
                            <div class="card-body">
                                <div class="stat-card-title">Total Menu</div>
                                <div class="stat-card-value"><?php echo $stats['menu_count']; ?></div>
                                <i class="fas fa-utensils stat-card-icon"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card stat-card-success">
                            <div class="card-body">
                                <div class="stat-card-title">Total Pelanggan</div>
                                <div class="stat-card-value"><?php echo $stats['customer_count']; ?></div>
                                <i class="fas fa-users stat-card-icon"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card stat-card-info">
                            <div class="card-body">
                                <div class="stat-card-title">Total Order</div>
                                <div class="stat-card-value"><?php echo $stats['order_count']; ?></div>
                                <i class="fas fa-shopping-cart stat-card-icon"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card stat-card-warning">
                            <div class="card-body">
                                <div class="stat-card-title">Total Pendapatan</div>
                                <div class="stat-card-value">Rp <?php echo number_format($stats['total_revenue'], 0, ',', '.'); ?></div>
                                <i class="fas fa-money-bill-wave stat-card-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card stat-card-secondary">
                            <div class="card-body">
                                <div class="stat-card-title">Total Kategori</div>
                                <div class="stat-card-value"><?php echo $stats['category_count']; ?></div>
                                <i class="fas fa-list stat-card-icon"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card stat-card-danger">
                            <div class="card-body">
                                <div class="stat-card-title">Order Pending</div>
                                <div class="stat-card-value"><?php echo $stats['pending_orders']; ?></div>
                                <i class="fas fa-clock stat-card-icon"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card stat-card-success">
                            <div class="card-body">
                                <div class="stat-card-title">Order Selesai</div>
                                <div class="stat-card-value"><?php echo $stats['completed_orders']; ?></div>
                                <i class="fas fa-check-circle stat-card-icon"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card stat-card-primary">
                            <div class="card-body">
                                <div class="stat-card-title">Selamat Datang</div>
                                <div class="stat-card-value"><?php echo $_SESSION['user']; ?></div>
                                <i class="fas fa-user stat-card-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts and Tables -->
                <div class="row">
                    <!-- Order Chart -->
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chart-line mr-1"></i> Statistik Order (7 Hari Terakhir)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="orderChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Popular Items -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-star mr-1"></i> Menu Terpopuler
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if(!empty($stats['popular_items'])): ?>
                                    <?php foreach($stats['popular_items'] as $item): ?>
                                        <div class="popular-item">
                                            <img src="../upload/<?php echo $item['gambar']; ?>" class="popular-item-img" alt="<?php echo $item['menu']; ?>">
                                            <div>
                                                <div class="popular-item-name"><?php echo $item['menu']; ?></div>
                                                <div class="popular-item-count"><?php echo $item['order_count']; ?> kali dipesan</div>
                                            </div>
                                            <span class="popular-item-badge">#<?php echo $item['order_count']; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-exclamation-circle fa-2x text-muted mb-3"></i>
                                        <p class="mb-0">Belum ada data menu terpopuler</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-shopping-cart mr-1"></i> Order Terbaru
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if(!empty($stats['recent_orders'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered recent-orders-table" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Pelanggan</th>
                                                    <th>Tanggal</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($stats['recent_orders'] as $order): ?>
                                                    <tr>
                                                        <td>#<?php echo $order['idorder']; ?></td>
                                                        <td><?php echo $order['pelanggan']; ?></td>
                                                        <td><?php echo date('d M Y H:i', strtotime($order['tglorder'])); ?></td>
                                                        <td>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                                                        <td>
                                                            <?php if($order['status'] == 1): ?>
                                                                <span class="status-badge status-badge-success">Lunas</span>
                                                            <?php else: ?>
                                                                <span class="status-badge status-badge-warning">Pending</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <a href="?f=order&m=detail&id=<?php echo $order['idorder']; ?>" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i> Detail
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-exclamation-circle fa-2x text-muted mb-3"></i>
                                        <p class="mb-0">Belum ada data order terbaru</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="content-wrapper">
                <?php 
                    if (isset($_GET['f']) && isset($_GET['m'])) {
                        $f = $_GET['f'];
                        $m = $_GET['m'];

                        $file = '../'.$f.'/'.$m.'.php';

                        require_once $file;
                    }
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <span>&copy; 2024 Warung Aldo - Developed by Christopher Aldo</span>
        </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('toggled');
            document.querySelector('.content').classList.toggle('toggled');
            document.querySelector('.topbar').classList.toggle('toggled');
            document.querySelector('.footer').classList.toggle('toggled');
        });
        
        // Highlight active menu item
        const currentUrl = window.location.href;
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            if (currentUrl.includes(link.getAttribute('href')) && link.getAttribute('href') !== 'index.php') {
                link.classList.add('active');
                document.querySelector('.nav-link.active').classList.remove('active');
            }
        });
        
        // Chart initialization
        <?php if (!isset($_GET['f']) && !isset($_GET['m']) && !empty($stats['orders_by_day'])): ?>
        // Order chart
        var ctx = document.getElementById('orderChart').getContext('2d');
        var orderChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    <?php 
                        $labels = [];
                        $data = [];
                        
                        // Create an array of the last 7 days
                        for ($i = 6; $i >= 0; $i--) {
                            $date = date('Y-m-d', strtotime("-$i days"));
                            $labels[] = '"' . date('d M', strtotime($date)) . '"';
                            $data[] = 0; // Default to 0 orders
                            
                            // Update with actual data if available
                            foreach ($stats['orders_by_day'] as $orderDay) {
                                if (date('Y-m-d', strtotime($orderDay['order_date'])) === $date) {
                                    $data[6-$i] = $orderDay['count'];
                                    break;
                                }
                            }
                        }
                        
                        echo implode(',', $labels);
                    ?>
                ],
                datasets: [{
                    label: 'Jumlah Order',
                    data: [<?php echo implode(',', $data); ?>],
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    lineTension: 0.3
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            beginAtZero: true,
                            callback: function(value) {
                                return value;
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }]
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ' + tooltipItem.yLabel;
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
