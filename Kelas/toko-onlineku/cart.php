<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Toko Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .cart-item {
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            background-color: #f1f3f5;
        }

        .order-summary {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Your Shopping Cart</h4>
                    </div>
                    <div class="card-body">
                        <div class="cart-item d-flex justify-content-between align-items-center border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <img src="images/jam1.png" class="cart-item-image me-3" alt="Product">
                                <div>
                                    <h5 class="mb-1">Classic Timepiece</h5>
                                    <p class="text-muted mb-0">Luxury Analog Watch</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="input-group me-3" style="width: 130px;">
                                    <button class="btn btn-outline-secondary" type="button">-</button>
                                    <input type="text" class="form-control text-center" value="1">
                                    <button class="btn btn-outline-secondary" type="button">+</button>
                                </div>
                                <span class="text-primary fw-bold me-3">Rp 1,500,000</span>
                                <button class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="cart-item d-flex justify-content-between align-items-center border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <img src="images/jam2.png" class="cart-item-image me-3" alt="Product">
                                <div>
                                    <h5 class="mb-1">Modern Elegance</h5>
                                    <p class="text-muted mb-0">Digital Smart Watch</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="input-group me-3" style="width: 130px;">
                                    <button class="btn btn-outline-secondary" type="button">-</button>
                                    <input type="text" class="form-control text-center" value="1">
                                    <button class="btn btn-outline-secondary" type="button">+</button>
                                </div>
                                <span class="text-primary fw-bold me-3">Rp 2,000,000</span>
                                <button class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="order-summary p-4">
                    <h4 class="border-bottom pb-3 mb-3">Order Summary</h4>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>Rp 3,500,000</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount</span>
                        <span class="text-success">- Rp 350,000</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span>Shipping</span>
                        <span>Rp 50,000</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Total</h5>
                        <h5 class="text-primary">Rp 3,200,000</h5>
                    </div>
                    <button class="btn btn-primary w-100">
                        Proceed to Checkout <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Products</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Cart</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Payment Methods</h5>
                    <div class="d-flex">
                        <i class="bi bi-credit-card me-3 fs-2"></i>
                        <i class="bi bi-paypal me-3 fs-2"></i>
                        <i class="bi bi-wallet2 fs-2"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5>Customer Support</h5>
                    <p>
                        <i class="bi bi-envelope me-2"></i> support@tokoonline.com<br>
                        <i class="bi bi-telephone me-2"></i> 0812-3456-7890
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>