@extends('layouts.app')
@section('title', 'Home - Ayam Goreng Joss Gandos')
@section('content')
<!-- Hero Section dengan Background Image -->
<div class="hero-section position-relative">
    <div class="hero-overlay"></div>
    <div class="container position-relative">
        <div class="row min-vh-80 align-items-center">
            <div class="col-lg-7 text-center text-lg-start animate__animated animate__fadeInLeft">
                <h1 class="display-3 fw-bold mb-3" style="color: var(--primary-color);">Ayam Goreng <span class="text-warning">Joss Gandos</span></h1>
                <p class="lead mb-4" style="color: var(--primary-color);">Sensasi cita rasa autentik ayam goreng dengan bumbu rahasia yang membuat lidah bergoyang!</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="{{ route('menu') }}" class="btn btn-warning btn-lg fw-bold shadow-sm animate__animated animate__pulse animate__infinite">
                        <i class="bi bi-menu-button-wide me-2"></i>Lihat Menu
                    </a>
                    <a href="{{ route('order') }}" class="btn btn-lg fw-bold" style="background-color: var(--primary-color);">
                        <i class="bi bi-bag-check me-2"></i>Order Sekarang
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block animate__animated animate__fadeInRight">
                <img src="{{ asset('gambar/Ayam Goreng Krispy.png') }}" class="img-fluid floating-image" alt="Featured Menu">
            </div>
        </div>
    </div>
</div>

<!-- Badge Highlights Section -->
<div class="bg-light py-4 shadow-sm">
    <div class="container">
        <div class="row g-3 text-center">
            <div class="col-6 col-md-3 animate__animated animate__fadeIn">
                <div class="d-flex align-items-center justify-content-center flex-column">
                    <i class="bi bi-award text-warning fs-1"></i>
                    <p class="mb-0 mt-2 fw-bold">Kualitas Premium</p>
                </div>
            </div>
            <div class="col-6 col-md-3 animate__animated animate__fadeIn" style="animation-delay: 0.2s;">
                <div class="d-flex align-items-center justify-content-center flex-column">
                    <i class="bi bi-truck text-warning fs-1"></i>
                    <p class="mb-0 mt-2 fw-bold">Pengiriman Cepat</p>
                </div>
            </div>
            <div class="col-6 col-md-3 animate__animated animate__fadeIn" style="animation-delay: 0.4s;">
                <div class="d-flex align-items-center justify-content-center flex-column">
                    <i class="bi bi-cash-coin text-warning fs-1"></i>
                    <p class="mb-0 mt-2 fw-bold">Harga Terjangkau</p>
                </div>
            </div>
            <div class="col-6 col-md-3 animate__animated animate__fadeIn" style="animation-delay: 0.6s;">
                <div class="d-flex align-items-center justify-content-center flex-column">
                    <i class="bi bi-heart text-warning fs-1"></i>
                    <p class="mb-0 mt-2 fw-bold">Pelanggan Puas</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- About Us Section -->
<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0 animate__animated animate__fadeInLeft">
            <div class="position-relative">
                <img src="{{ asset('images/ayam-goreng.jpg') }}" class="img-fluid rounded-lg shadow-lg" alt="Ayam Goreng">
                <div class="experience-badge position-absolute bg-warning text-dark fw-bold rounded-circle p-4 d-flex flex-column align-items-center justify-content-center">
                    <span class="h2 mb-0">15</span>
                    <span class="small">Tahun</span>
                </div>
            </div>
        </div>
        <div class="col-lg-6 animate__animated animate__fadeInRight">
            <div class="about-content">
                <span class="text-warning text-uppercase fw-bold">Tentang Kami</span>
                <h2 class="display-6 fw-bold mt-2 mb-4">Pengalaman Kuliner Ayam Goreng Terbaik di Kota</h2>
                <p class="lead">Ayam Goreng Joss Gandos hadir dengan tekad memberikan pengalaman kuliner terbaik dengan resep rahasia turun temurun.</p>
                <p>Sejak 2010, kami konsisten menyajikan ayam goreng dengan kualitas premium, menggunakan bahan-bahan segar pilihan dan bumbu rahasia yang meresap hingga ke tulang. Kerenyahan yang sempurna dan cita rasa autentik menjadi ciri khas yang membuat pelanggan kami selalu kembali.</p>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning p-2 rounded-circle me-3">
                                <i class="bi bi-check2-circle text-dark"></i>
                            </div>
                            <span class="fw-bold">Bahan Pilihan Premium</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning p-2 rounded-circle me-3">
                                <i class="bi bi-check2-circle text-dark"></i>
                            </div>
                            <span class="fw-bold">Resep Rahasia</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning p-2 rounded-circle me-3">
                                <i class="bi bi-check2-circle text-dark"></i>
                            </div>
                            <span class="fw-bold">Chef Berpengalaman</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning p-2 rounded-circle me-3">
                                <i class="bi bi-check2-circle text-dark"></i>
                            </div>
                            <span class="fw-bold">Cita Rasa Konsisten</span>
                        </div>
                    </div>
                </div>

                {{-- <a href="{{ route('about') }}" class="btn btn-dark mt-4">Pelajari Lebih Lanjut <i class="bi bi-arrow-right ms-2"></i></?a> --}}
            </div>
        </div>
    </div>
</div>

<!-- Featured Menu Section -->
<div class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-warning text-uppercase fw-bold">Menu Favorit</span>
            <h2 class="display-6 fw-bold mt-2">Hidangan Best Seller Kami</h2>
            <div class="border-bottom border-3 w-25 mx-auto my-3" style="border-color: #ffc107 !important;"></div>
        </div>

        <div class="row g-4">
            <div class="col-md-4 animate__animated animate__fadeInUp">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="position-relative">
                        <img src="{{ asset('images/ayam-original.jpg') }}" class="card-img-top" alt="Ayam Original" style="height: 220px; object-fit: cover;">
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-3">Best Seller</span>
                    </div>
                    <div class="card-body text-center">
                        <h4 class="card-title fw-bold">Ayam Goreng Original</h4>
                        <div class="my-3">
                            <span class="text-warning">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </span>
                            <span class="ms-2 small text-muted">(128 ulasan)</span>
                        </div>
                        <p class="card-text">Ayam goreng dengan bumbu tradisional khas yang meresap hingga ke tulang.</p>
                        <h5 class="text-warning fw-bold mt-3">Rp 25.000</h5>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="{{ route('menu') }}" class="btn btn-outline-warning w-100">Pesan Sekarang</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="position-relative">
                        <img src="{{ asset('images/ayam-spicy.jpg') }}" class="card-img-top" alt="Ayam Spicy" style="height: 220px; object-fit: cover;">
                        <span class="badge bg-danger position-absolute top-0 end-0 m-3">Hot</span>
                    </div>
                    <div class="card-body text-center">
                        <h4 class="card-title fw-bold">Ayam Goreng Spicy</h4>
                        <div class="my-3">
                            <span class="text-warning">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                            </span>
                            <span class="ms-2 small text-muted">(96 ulasan)</span>
                        </div>
                        <p class="card-text">Sensasi pedas yang menggugah selera dengan bumbu cabai pilihan.</p>
                        <h5 class="text-warning fw-bold mt-3">Rp 28.000</h5>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="{{ route('menu') }}" class="btn btn-outline-warning w-100">Pesan Sekarang</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="position-relative">
                        <img src="{{ asset('images/paket-nasi.jpg') }}" class="card-img-top" alt="Paket Nasi" style="height: 220px; object-fit: cover;">
                        <span class="badge bg-success position-absolute top-0 end-0 m-3">Value</span>
                    </div>
                    <div class="card-body text-center">
                        <h4 class="card-title fw-bold">Paket Nasi Ayam</h4>
                        <div class="my-3">
                            <span class="text-warning">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </span>
                            <span class="ms-2 small text-muted">(145 ulasan)</span>
                        </div>
                        <p class="card-text">Kombinasi sempurna nasi pulen, ayam goreng dan sambal spesial.</p>
                        <h5 class="text-warning fw-bold mt-3">Rp 35.000</h5>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="{{ route('menu') }}" class="btn btn-outline-warning w-100">Pesan Sekarang</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('menu') }}" class="btn btn-warning btn-lg">Lihat Seluruh Menu <i class="bi bi-arrow-right ms-2"></i></a>
        </div>
    </div>
</div>

<!-- Testimonial Section -->
<div class="container py-5">
    <div class="text-center mb-5">
        <span class="text-warning text-uppercase fw-bold">Testimonial</span>
        <h2 class="display-6 fw-bold mt-2">Apa Kata Pelanggan Kami</h2>
        <div class="border-bottom border-3 w-25 mx-auto my-3" style="border-color: #ffc107 !important;"></div>
    </div>

    <div class="row g-4">
        <div class="col-md-4 animate__animated animate__fadeInUp">
            <div class="card border-0 shadow-sm h-100 p-4 testimonial-card">
                <div class="text-warning mb-3">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                </div>
                <p class="card-text fst-italic">"Ayam goreng terenak yang pernah saya coba! Bumbunya meresap sampai ke dalam dan kulitnya renyah sempurna. Saya sudah jadi pelanggan tetap di sini."</p>
                <div class="d-flex align-items-center mt-3">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('images/testimonial-1.jpg') }}" class="rounded-circle" width="50" height="50" alt="Customer">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0 fw-bold">Budi Santoso</h6>
                        <p class="small text-muted mb-0">Jakarta</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="card border-0 shadow-sm h-100 p-4 testimonial-card">
                <div class="text-warning mb-3">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                </div>
                <p class="card-text fst-italic">"Paket nasi ayam mereka adalah comfort food favorite saya. Porsinya pas, rasanya konsisten, dan harganya sangat terjangkau untuk kualitas sebagus ini."</p>
                <div class="d-flex align-items-center mt-3">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('images/testimonial-2.jpg') }}" class="rounded-circle" width="50" height="50" alt="Customer">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0 fw-bold">Siti Nurhaliza</h6>
                        <p class="small text-muted mb-0">Bandung</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div class="card border-0 shadow-sm h-100 p-4 testimonial-card">
                <div class="text-warning mb-3">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-half"></i>
                </div>
                <p class="card-text fst-italic">"Ayam Goreng Spicy mereka benar-benar pedas yang nikmat, tidak asal pedas! Sambalnya juga enak banget. Pengiriman selalu tepat waktu dan makanan masih hangat."</p>
                <div class="d-flex align-items-center mt-3">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('images/testimonial-3.jpg') }}" class="rounded-circle" width="50" height="50" alt="Customer">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0 fw-bold">Agus Dermawan</h6>
                        <p class="small text-muted mb-0">Surabaya</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="bg-dark text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 animate__animated animate__fadeInLeft">
                <h2 class="fw-bold mb-3">Pesan Sekarang dan Dapatkan Diskon 10%</h2>
                <p class="lead mb-0">Untuk pemesanan pertama Anda melalui aplikasi atau website kami.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0 animate__animated animate__fadeInRight">
                <a href="{{ route('order') }}" class="btn btn-warning btn-lg fw-bold">Pesan Sekarang <i class="bi bi-arrow-right ms-2"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- CSS tambahan -->
<style>
    /* Hero Section */
    .hero-section {
        /* background: url('{{ asset('images/hero-bg.jpg') }}') no-repeat center center */
        background-color: #fff;
        background-size: cover;
        color: white;
        padding: 120px 0;
        position: relative;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #fff;
    }

    .min-vh-80 {
        min-height: 80vh;
    }

    /* Animation */
    .floating-image {
        animation: floating 3s ease-in-out infinite;
    }

    @keyframes floating {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }

    /* Experience Badge */
    .experience-badge {
        width: 120px;
        height: 120px;
        bottom: -30px;
        right: 30px;
    }

    /* Menu & Testimonial Cards */
    .menu-card, .testimonial-card {
        transition: all 0.3s ease;
        border-radius: 10px;
    }

    .menu-card:hover, .testimonial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    }

    /* Bootstrap Icons */
    @import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css");

    /* Rounded utility */
    .rounded-lg {
        border-radius: 1rem!important;
    }
</style>

@endsection
