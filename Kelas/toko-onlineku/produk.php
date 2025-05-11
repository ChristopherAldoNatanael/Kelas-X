<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online - Watch Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <!-- Add Font Awesome for social media icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .product-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .product-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .quick-view-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover .quick-view-overlay {
            opacity: 1;
        }

        .filter-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .footer {
            background-color: #4a90e2;
            /* Primary color */
            padding: 40px 0;
        }

        .footer h5 {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .footer a {
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: #6a11cb;
            /* Secondary color */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <strong>Toko Online</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="produk.php#" class="nav-link">Produk</a></li>
                    <li class="nav-item"><a href="cart.php" class="nav-link">Cart</a></li>
                    <li class="nav-item"><a href=about.php#" class="nav-link">About</a></li>
                    <li class="nav-item"><a href="kontak.php" class="nav-link">Kontak</a></li>
                </ul>
                <div class="d-flex">
                    <div class="input-group me-2">
                        <input type="text" class="form-control" placeholder="Search products">
                        <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                    </div>
                    <a href="register.php" class="btn btn-outline-primary me-2"><i class="bi bi-person-plus"></i> Register</a>
                    <a href="login.php" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="filter-section p-4 mb-4">
                    <h5>Filter Products</h5>
                    <div class="mb-3">
                        <label class="form-label">Price Range</label>
                        <div class="d-flex">
                            <input type="number" class="form-control me-2" placeholder="Min">
                            <input type="number" class="form-control" placeholder="Max">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <select class="form-select">
                            <option>All Brands</option>
                            <option>Rolex</option>
                            <option>Omega</option>
                            <option>Seiko</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">Apply Filters</button>
                </div>
            </div>
            <div class="col-md-9">
                <div class="row g-4">
                    <!-- Product 1 -->
                    <div class="col-md-4">
                        <div class="card product-card position-relative">
                            <img src="images/jam1.png" class="card-img-top product-image" alt="Classic Watch">
                            <div class="quick-view-overlay">
                                <button class="btn btn-light">Quick View</button>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title">Classic Timepiece</h5>
                                <p class="text-muted">Luxury Analog</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 text-primary mb-0">Rp 1,500,000</span>
                                    <button class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-cart-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product 2 -->
                    <div class="col-md-4">
                        <div class="card product-card position-relative">
                            <img src="images/jam2.png" class="card-img-top product-image" alt="Modern Watch">
                            <div class="quick-view-overlay">
                                <button class="btn btn-light">Quick View</button>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title">Modern Elegance</h5>
                                <p class="text-muted">Digital Smart Watch</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 text-primary mb-0">Rp 2,000,000</span>
                                    <button class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-cart-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product 3 -->
                    <div class="col-md-4">
                        <div class="card product-card position-relative">
                            <img src="images/jam3.png" class="card-img-top product-image" alt="Sport Watch">
                            <div class="quick-view-overlay">
                                <button class="btn btn-light">Quick View</button>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title">Sport Chronograph</h5>
                                <p class="text-muted">Performance Series</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 text-primary mb-0">Rp 1,750,000</span>
                                    <button class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-cart-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row text-white">
                <div class="col-md-3">
                    <h5>Menu</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="produk.php" class="text-white">Produk</a></li>
                        <li><a href="about.php" class="text-white">About</a></li>
                        <li><a href="kontak.php" class="text-white">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Pembayaran</h5>
                    <p>Visa, MasterCard, PayPal</p>
                </div>
                <div class="col-md-3">
                    <h5>Media Sosial</h5>
                    <p>
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i> Facebook</a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i> Instagram</a>
                        <a href="#" class="text-white"><i class="fab fa-twitter"></i> Twitter</a>
                    </p>
                </div>
                <div class="col-md-3">
                    <h5>Kontak</h5>
                    <p>Email: <a href="mailto:contact@tokoonline.com" class="text-white">contact@tokoonline.com</a></p>
                    <p>Telepon: <a href="tel:08123456789" class="text-white">08123456789</a></p>
                </div>
            </div>
            <div class="text-center mt-4">
                <p>&copy; 2025 Toko Online. All rights reserved.</p>
            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>