<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Toko Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Add Font Awesome for social media icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            /* Bright blue */
            --secondary-color: #3f51b5;
            /* Darker blue for contrast */
            --accent-color: #27ae60;
            /* Green accent */
            --light-background: #f4f7fc;
            /* Light background */
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-background);
            color: #4a4a4a;
        }

        .contact-header {
            background: linear-gradient(var(--primary-color), var(--secondary-color));
            height: 350px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .contact-header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .contact-form {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .contact-form:hover {
            transform: translateY(-5px);
        }

        .contact-info {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-color) 70%, var(--secondary-color) 100%);
            color: white;
            padding: 40px;
            border-radius: 10px;
            height: 100%;
        }

        .contact-info h5 {
            font-weight: bold;
            color: var(--accent-color);
        }

        .contact-info a {
            color: #f1c40f;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-info a:hover {
            color: #fff;
            text-decoration: underline;
        }

        .form-control,
        .btn {
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
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
                    <li class="nav-item"><a href="about.php#" class="nav-link">About</a></li>
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

    <div class="contact-header">
        <h1>Contact Us</h1>
    </div>

    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="contact-form h-100">
                    <h2 class="mb-4">Get in Touch</h2>
                    <form action="submit_contact.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Your Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Your Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Write your message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Message</button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="contact-info">
                    <h2 class="mb-4 text-white">Contact Information</h2>
                    <div class="mb-4">
                        <h5>Address:</h5>
                        <p>123 Main Street, Cityville, Country</p>
                    </div>

                    <div class="mb-4">
                        <h5>Email:</h5>
                        <p><a href="mailto:info@example.com" class="text-white">info@example.com</a></p>
                    </div>

                    <div class="mb-4">
                        <h5>Phone:</h5>
                        <p><a href="tel:+1234567890" class="text-white">+1 234 567 890</a></p>
                    </div>

                    <div>
                        <h5>Social Media:</h5>
                        <p>
                            <a href="#" class="text-white me-3">Facebook</a>
                            <a href="#" class="text-white me-3">Twitter</a>
                            <a href="#" class="text-white">Instagram</a>
                        </p>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>