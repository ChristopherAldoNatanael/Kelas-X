@extends('layouts.app')
@section('title', 'Menu - Resto Ayam Goreng')
@section('content')
<div class="container py-5">
    <!-- Hero Section -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold animate__animated animate__fadeInDown">Menu Spesial Kami</h1>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <p class="text-muted lead animate__animated animate__fadeIn animate__delay-1s">Nikmati hidangan ayam goreng berkualitas premium dengan racikan bumbu rahasia</p>
            </div>
        </div>
        <div class="border-bottom border-3 w-25 mx-auto my-4" style="border-color: #FF9F1C !important;"></div>
    </div>

    <!-- Filter Options -->
    <div class="d-flex justify-content-center mb-4 animate__animated animate__fadeIn">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-dark active">Semua</button>
            <button type="button" class="btn btn-outline-dark">Original</button>
            <button type="button" class="btn btn-outline-dark">Spicy</button>
            <button type="button" class="btn btn-outline-dark">Paket</button>
        </div>
    </div>

    <!-- Menu Cards -->
    <div class="row g-4">
        <!-- Ayam Goreng Original -->
        <div class="col-md-4 col-sm-6 mb-4 animate__animated animate__fadeInUp">
            <div class="card h-100 border-0 shadow-sm position-relative menu-card">
                <div class="badge bg-danger position-absolute" style="top: 15px; left: 15px;">Bestseller</div>
                <img src="{{ asset('images/ayam-goreng.jpg') }}" class="card-img-top" alt="Ayam Goreng Original" style="height: 220px; object-fit: cover;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title fw-bold mb-0">Ayam Goreng Original</h5>
                        <span class="badge bg-dark rounded-pill">Original</span>
                    </div>
                    <p class="card-text small text-muted">Ayam goreng renyah dengan bumbu tradisional yang kaya rempah</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="price-section">
                            <span class="text-dark fw-bold">Rp 25.000</span>
                        </div>
                        <button class="btn btn-sm btn-primary">+ Keranjang</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ayam Goreng Spicy -->
        <div class="col-md-4 col-sm-6 mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="card h-100 border-0 shadow-sm position-relative menu-card">
                <div class="badge bg-danger position-absolute" style="top: 15px; left: 15px;">Hot</div>
                <img src="{{ asset('images/ayam-spicy.jpg') }}" class="card-img-top" alt="Ayam Spicy" style="height: 220px; object-fit: cover;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title fw-bold mb-0">Ayam Goreng Spicy</h5>
                        <span class="badge bg-danger rounded-pill">Spicy</span>
                    </div>
                    <p class="card-text small text-muted">Sensasi pedas yang menggugah selera dengan rempah pilihan</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="price-section">
                            <span class="text-dark fw-bold">Rp 28.000</span>
                        </div>
                        <button class="btn btn-sm btn-primary">+ Keranjang</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paket Nasi Ayam -->
        <div class="col-md-4 col-sm-6 mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div class="card h-100 border-0 shadow-sm position-relative menu-card">
                <div class="badge bg-success position-absolute" style="top: 15px; left: 15px;">Value</div>
                <img src="{{ asset('images/paket-nasi.jpg') }}" class="card-img-top" alt="Paket Nasi" style="height: 220px; object-fit: cover;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title fw-bold mb-0">Paket Nasi Ayam</h5>
                        <span class="badge bg-success rounded-pill">Paket</span>
                    </div>
                    <p class="card-text small text-muted">Kombo nasi putih pulen dengan ayam goreng dan sambal spesial</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="price-section">
                            <span class="text-dark fw-bold">Rp 35.000</span>
                        </div>
                        <button class="btn btn-sm btn-primary">+ Keranjang</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Promo Banner -->
    <div class="row mt-5 animate__animated animate__fadeIn">
        <div class="col-12">
            <div class="card bg-dark text-white border-0 overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-6">
                        <img src="{{ asset('images/promo-banner.jpg') }}" alt="Promo" class="img-fluid h-100 object-fit-cover">
                    </div>
                    <div class="col-md-6">
                        <div class="card-body p-5">
                            <h3 class="card-title fw-bold mb-3">Promo Akhir Pekan!</h3>
                            <p class="card-text">Dapatkan diskon 15% untuk pembelian paket keluarga setiap hari Sabtu & Minggu.</p>
                            <button class="btn btn-warning mt-3">Lihat Detail</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS tambahan -->
<style>
    .menu-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
    }

    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .btn-primary {
        background-color: #FF9F1C;
        border-color: #FF9F1C;
    }

    .btn-primary:hover {
        background-color: #e89018;
        border-color: #e89018;
    }

    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
    }
</style>
@endsection
