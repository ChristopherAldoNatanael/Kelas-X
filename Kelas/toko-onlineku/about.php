<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Toko Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #6a11cb;
            --light-background: #f4f7fc;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-background);
            color: #4a4a4a;
        }

        .about-header {
            background: rgb(100, 100, 255);
            height: 350px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .about-header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .about-section {
            padding: 60px 15px;
            background-color: white;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .statistic {
            text-align: center;
            background-color: var(--light-background);
            padding: 30px;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .statistic:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .statistic h2 {
            font-size: 2.8rem;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .mission-vision {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 50px 15px;
        }

        .mission-vision h3 {
            font-weight: bold;
            margin-bottom: 25px;
            color: white;
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

    <div class="about-header">
        <h1>About Toko Online</h1>
    </div>

    <div class="container about-section">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="mb-4">Who We Are</h2>
                <p class="lead">We are a dedicated team passionate about providing the best products and services to our customers. Our mission is to deliver excellence in every aspect of our business, from quality products to exceptional customer service.</p>
            </div>
            <div class="col-md-6">
                <img src="images/banner3.png" alt="About Us" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>

    <div class="mission-vision text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3>Our Mission</h3>
                    <p>To inspire and innovate, offering top-notch products that improve the lives of our customers.</p>
                </div>
                <div class="col-md-6">
                    <h3>Our Vision</h3>
                    <p>To be a global leader recognized for our quality, integrity, and customer-centric approach.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container about-section">
        <h2 class="text-center mb-5">Our Achievements</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="statistic">
                    <h2>5,000+</h2>
                    <p>Sellers Love Us</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="statistic">
                    <h2>10,000+</h2>
                    <p>Products Available</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="statistic">
                    <h2>15+ Years</h2>
                    <p>Experience in the Market</p>
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

    <!-- Add Font Awesome for social media icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>