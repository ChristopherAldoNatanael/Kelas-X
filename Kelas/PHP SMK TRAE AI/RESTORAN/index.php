<?php 
    session_start();
    require_once "dbcontroller.php";
    $db = new DB;

    $sql = "SELECT * FROM tblkategori ORDER BY kategori";
    $row = $db->getAll($sql);

    if (isset($_GET['log'])) {
       session_destroy();
       header("location:index.php");
    }

    function cart()
    {
        global $db;

        $cart = 0;

        foreach ($_SESSION as $key => $value) {
           if($key<>'pelanggan' && $key<> 'idpelanggan' && $key<> 'user' && $key<> 'level' && $key<> 'iduser') {
                $id = substr($key,1);

                $sql = "SELECT * FROM tblmenu WHERE idmenu=$id";

                $row = $db->getAll($sql);

                if($row) { // Tambahkan pengecekan apakah $row tidak null
                    foreach ($row as $r) {
                        $cart++;
                    }
                }
            }
        }    

        return $cart;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Aldo | Authentic Indonesian Cuisine</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B35;
            --secondary: #2E294E;
            --accent: #1B998B;
            --light: #F7F7F2;
            --dark: #252422;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--dark);
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .brand-logo {
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            font-size: 1.8rem;
            transition: all 0.3s ease;
        }
        
        .brand-logo:hover {
            color: var(--secondary);
            text-decoration: none;
        }
        
        .nav-link {
            color: var(--dark);
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--primary);
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #e55a2a;
            border-color: #e55a2a;
        }
        
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('/placeholder.svg?height=500&width=1200');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
            margin-bottom: 30px;
            border-radius: 10px;
        }
        
        .category-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .category-title {
            font-weight: 600;
            color: var(--secondary);
            border-left: 4px solid var(--primary);
            padding-left: 10px;
        }
        
        .category-list {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 20px;
        }
        
        .category-list .nav-link {
            color: var(--dark);
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }
        
        .category-list .nav-link:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .cart-icon {
            position: relative;
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }
        
        .content-area {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 20px;
            min-height: 500px;
        }
        
        .footer {
            background-color: var(--secondary);
            color: white;
            padding: 30px 0;
            margin-top: 50px;
        }
        
        .user-welcome {
            background-color: var(--accent);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 40px 0;
            }
            
            .navbar-brand {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="brand-logo" href="index.php">
                <i class="fas fa-utensils mr-2"></i> WARUNG ALDO
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <?php 
                    if (isset($_SESSION['pelanggan'])) {
                        echo '
                            <li class="nav-item">
                                <span class="nav-link user-welcome mr-3">
                                    <i class="fas fa-user mr-1"></i> '.$_SESSION['pelanggan'].'
                                </span>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?f=home&m=histori">
                                    <i class="fas fa-history mr-1"></i> Order History
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?f=home&m=beli">
                                    <div class="cart-icon">
                                        <i class="fas fa-shopping-cart mr-1"></i>
                                        <span class="cart-badge">'.cart().'</span>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn btn-outline-primary ml-2 px-3" href="?log=logout">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                                </a>
                            </li>
                        ';
                    } else {
                        echo '
                            <li class="nav-item">
                                <a class="nav-link" href="?f=home&m=login">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn btn-primary text-white ml-2 px-3" href="?f=home&m=daftar">
                                    <i class="fas fa-user-plus mr-1"></i> Register
                                </a>
                            </li>
                        ';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (!isset($_GET['f']) || (isset($_GET['f']) && $_GET['f'] == 'home' && $_GET['m'] == 'produk')): ?>
        <div class="hero-section">
            <div class="container text-center">
                <h1 class="display-4 font-weight-bold">Authentic Indonesian Cuisine</h1>
                <p class="lead">Experience the rich flavors of Indonesia at Warung Aldo</p>
                <a href="#menu-section" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-utensils mr-2"></i> Explore Our Menu
                </a>
            </div>
        </div>
        <?php endif; ?>

        <div class="row" id="menu-section">
            <div class="col-md-3 mb-4">
                <h3 class="category-title mb-3">Categories</h3>
                <div class="category-list">
                    <?php if(!empty($row)) { ?>
                    <ul class="nav flex-column">
                        <?php foreach($row as $r): ?>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="?f=home&m=produk&id=<?php echo $r['idkategori']?>">
                                <i class="fas fa-angle-right mr-2"></i>
                                <?php echo $r['kategori'] ?>
                            </a>
                        </li>
                        <?php endforeach ?>
                    </ul>
                    <?php } else { ?>
                        <p class="text-muted">No categories available</p>
                    <?php } ?>
                </div>
                
                <div class="mt-4">
                    <div class="card category-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Need Help?</h5>
                            <p class="card-text">Contact our customer service</p>
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fas fa-headset mr-1"></i> Contact Us
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="content-area">
                    <?php 
                    if (isset($_GET['f']) && isset($_GET['m'])) {
                        $f = $_GET['f'];
                        $m = $_GET['m'];
                        $file = $f.'/'.$m.'.php';
                        require_once $file;
                    } else {
                        require_once "home/produk.php";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h4><i class="fas fa-utensils mr-2"></i> WARUNG ALDO</h4>
                    <p>Authentic Indonesian cuisine with the finest ingredients and traditional recipes.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="?f=home&m=about" class="text-white">About Us</a></li>
                        <li><a href="?f=home&m=contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Connect With Us</h5>
                    <div class="d-flex">
                        <a href="#" class="text-white mr-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="text-white mr-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white mr-3"><i class="fab fa-twitter fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center">
                <p>2024 - Copyright &copy; <strong>ChristopherAldo</strong></p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
