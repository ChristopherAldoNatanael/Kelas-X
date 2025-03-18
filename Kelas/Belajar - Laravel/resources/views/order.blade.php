@extends('layouts.app')
@section('title', 'Order')
@section('content')
<div class="container">
    <h1 class="text-center mb-5 animate__animated animate__bounceIn">Pesan Sekarang</h1>
    <div class="row justify-content-center">
        <div class="col-md-6 animate__animated animate__fadeIn">
            <div class="card shadow-lg p-4">
                <form>
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nama</label>
                        <input type="text" class="form-control" id="name" placeholder="Masukkan nama Anda">
                    </div>
                    <div class="mb-3">
                        <label for="menu" class="form-label fw-bold">Pilih Menu</label>
                        <select class="form-select" id="menu">
                            <option>Ayam Goreng Original - Rp 25.000</option>
                            <option>Ayam Goreng Spicy - Rp 28.000</option>
                            <option>Paket Nasi Ayam - Rp 35.000</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label fw-bold">Jumlah</label>
                        <input type="number" class="form-control" id="quantity" min="1" value="1">
                    </div>
                    <button type="submit" class="btn btn-warning w-100 animate__animated animate__pulse animate__infinite">Pesan Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
