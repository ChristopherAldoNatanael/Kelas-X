<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayam Goreng Joss Gandos - @yield('title')</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- AOS Library -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <!-- Swiper CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="spinner"></div>
    </div>

    <!-- Custom Cursor (Desktop only) -->
    <div class="cursor"></div>
    <div class="cursor-follower"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}" data-aos="fade-right" data-aos-duration="1000">
                <i class="fas fa-drumstick-bite me-2"></i>Ayam Goreng Joss Gandos
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item" data-aos="fade-down" data-aos-delay="100">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item" data-aos="fade-down" data-aos-delay="200">
                        <a class="nav-link {{ request()->routeIs('menu') ? 'active' : '' }}" href="{{ route('menu') }}">Menu</a>
                    </li>
                    <li class="nav-item" data-aos="fade-down" data-aos-delay="300">
                        <a class="nav-link {{ request()->routeIs('order') ? 'active' : '' }}" href="{{ route('order') }}">Order</a>
                    </li>
                    <li class="nav-item" data-aos="fade-down" data-aos-delay="400">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                    </li>
                    <li class="nav-item" data-aos="fade-down" data-aos-delay="500">
                        <a class="nav-link {{ request()->routeIs('chat') ? 'active' : '' }}" href="{{ route('chat') }}">Chat</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0" data-aos="fade-down" data-aos-delay="600">
                        <a class="btn btn-order" href="{{ route('order') }}">
                            <i class="fas fa-shopping-cart me-2"></i>Order Now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Transition Effect -->
    <div class="page-transition"></div>

    <!-- Content -->
    <main class="py-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="footer-widget">
                        <a class="footer-logo">
                            <i class="fas fa-drumstick-bite me-2"></i>Ayam Goreng Joss Gandos
                        </a>
                        <div class="footer-content">
                            <p>Sajian ayam goreng dengan racikan spesial yang membuat lidah anda bergoyang dan ketagihan untuk terus menikmati sensasi rasa yang tak terlupakan.</p>
                            <div class="social-icons">
                                <a href="#" data-aos="zoom-in" data-aos-delay="100"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" data-aos="zoom-in" data-aos-delay="200"><i class="fab fa-instagram"></i></a>
                                <a href="#" data-aos="zoom-in" data-aos-delay="300"><i class="fab fa-twitter"></i></a>
                                <a href="#" data-aos="zoom-in" data-aos-delay="400"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="footer-widget">
                        <h4 class="footer-heading">Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><a href="{{ route('menu') }}">Menu</a></li>
                            <li><a href="{{ route('order') }}">Order</a></li>
                            <li><a href="{{ route('contact') }}">Contact</a></li>
                            <li><a href="{{ route('chat') }}">Chat</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="footer-widget">
                        <h4 class="footer-heading">Open Hours</h4>
                        <ul class="footer-links">
                            <li>Monday - Friday: 9am - 10pm</li>
                            <li>Saturday - Sunday: 10am - 11pm</li>
                            <li>Holiday: 10am - 9pm</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="footer-widget">
                        <h4 class="footer-heading">Contact Info</h4>
                        <ul class="footer-links">
                            <li><i class="fas fa-map-marker-alt me-2"></i> Jl. Makan Enak No. 123, Jakarta</li>
                            <li><i class="fas fa-phone-alt me-2"></i> +62 812 3456 7890</li>
                            <li><i class="fas fa-envelope me-2"></i> info@ayamjossgandos.id</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="text-center copyright" data-aos="fade-up">
                <p>© 2025 Ayam Goreng Joss Gandos. All rights reserved. Developed with <i class="fas fa-heart text-danger"></i> using Laravel 12.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <div class="scroll-top">
        <i class="fas fa-arrow-up"></i>
    </div>

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <!-- Swiper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.js"></script>
    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js"></script>

    <!-- Custom JS -->
    <script>
        $(document).ready(function() {
            // Initialize AOS
            AOS.init({
                duration: 1000,
                once: true,
                mirror: false
            });

            // Preloader
            setTimeout(function() {
                $('.preloader').fadeOut(500);
            }, 1500);

            // Navbar Scroll Effect
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                    $('.scroll-top').addClass('active');
                } else {
                    $('.navbar').removeClass('scrolled');
                    $('.scroll-top').removeClass('active');
                }
            });

            // Scroll to Top
            $('.scroll-top').click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
                return false;
            });

            // Page Transition Effect
            $('a').not('[href="#"]').not('[href="#0"]').not('[target="_blank"]').click(function(e) {
                var href = $(this).attr('href');

                if (href) {
                    e.preventDefault();
                    $('.page-transition').addClass('active');

                    setTimeout(function() {
                        window.location.href = href;
                    }, 500);
                }
            });

            // Custom Cursor Effect (Desktop only)
            if ($(window).width() > 991) {
                $(window).mousemove(function(e) {
                    $('.cursor').css({
                        left: e.clientX,
                        top: e.clientY
                    });

                    setTimeout(function() {
                        $('.cursor-follower').css({
                            left: e.clientX,
                            top: e.clientY
                        });
                    }, 100);
                });

                $('a, button').hover(
                    function() {
                        $('.cursor').css({
                            transform: 'translate(-50%, -50%) scale(1.5)',
                            opacity: 0.5
                        });
                        $('.cursor-follower').css({
                            transform: 'translate(-50%, -50%) scale(1.3)',
                            background: 'rgba(255, 107, 53, 0.1)'
                        });
                    },
                    function() {
                        $('.cursor').css({
                            transform: 'translate(-50%, -50%) scale(1)',
                            opacity: 1
                        });
                        $('.cursor-follower').css({
                            transform: 'translate(-50%, -50%) scale(1)',
                            background: 'transparent'
                        });
                    }
                );
            }

            // GSAP Animations
            gsap.from('.navbar-brand', {
                duration: 1,
                y: -50,
                opacity: 0,
                delay: 0.5
            });

            // Floating elements animation for images
            $('.floating-img').each(function() {
                gsap.to($(this), {
                    y: 20,
                    duration: 2,
                    repeat: -1,
                    yoyo: true,
                    ease: "power1.inOut"
                });
            });
        });
    </script>
</body>
</html>
