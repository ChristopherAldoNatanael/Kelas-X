<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online - Premium Watch Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Add Font Awesome for social media icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .product-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .carousel-item img {
            height: 70vh;
            object-fit: cover;
        }

        .section-header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 30px;
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

    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/banner1.png" class="d-block w-100" alt="Banner 1">
            </div>
            <div class="carousel-item">
                <img src="images/banner2.png" class="d-block w-100" alt="Banner 2">
            </div>
            <div class="carousel-item">
                <img src="images/banner3.png" class="d-block w-100" alt="Banner 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>

    <div class="container my-5">
        <h2 class="text-center section-header">Our Premium Watch Collection</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card product-card">
                    <img src="images/jam1.png" class="card-img-top" alt="Watch 1">
                    <div class="card-body text-center">
                        <h5 class="card-title">Classic Timepiece</h5>
                        <p class="card-text text-primary fw-bold">Rp 1,500,000</p>
                        <button class="btn btn-outline-primary"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card product-card">
                    <img src="images/jam2.png" class="card-img-top" alt="Watch 2">
                    <div class="card-body text-center">
                        <h5 class="card-title">Modern Elegance</h5>
                        <p class="card-text text-primary fw-bold">Rp 2,000,000</p>
                        <button class="btn btn-outline-primary"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card product-card">
                    <img src="images/jam3.png" class="card-img-top" alt="Watch 3">
                    <div class="card-body text-center">
                        <h5 class="card-title">Sport Chronograph</h5>
                        <p class="card-text text-primary fw-bold">Rp 1,750,000</p>
                        <button class="btn btn-outline-primary"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card product-card">
                    <img src="images/jam4.png" class="card-img-top" alt="Watch 4">
                    <div class="card-body text-center">
                        <h5 class="card-title">Luxury Timekeeper</h5>
                        <p class="card-text text-primary fw-bold">Rp 2,500,000</p>
                        <button class="btn btn-outline-primary"><i class="bi bi-cart-plus"></i> Add to Cart</button>
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