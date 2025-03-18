@extends('layouts.app')
@section('title', 'Contact')
@section('content')
<div class="container">
    <h1 class="text-center mb-5 animate__animated animate__bounceIn">Hubungi Kami</h1>
    <div class="row animate__animated animate__fadeIn">
        <div class="col-md-6">
            <ul class="list-unstyled">
                <li class="mb-3"><i class="fas fa-phone me-2 text-warning"></i> 0812-3456-7890</li>
                <li class="mb-3"><i class="fas fa-envelope me-2 text-warning"></i> info@jossgandos.com</li>
                <li class="mb-3"><i class="fas fa-map-marker-alt me-2 text-warning"></i> Jl. Rasa No. 123, Jakarta</li>
            </ul>
        </div>
        <div class="col-md-6">
            <iframe src="https://www.google.com/maps/embed?pb=..." class="w-100 rounded shadow-lg" height="300" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</div>
@endsection
