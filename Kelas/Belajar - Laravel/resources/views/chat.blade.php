@extends('layouts.app')
@section('title', 'Chat')
@section('content')
<div class="container">
    <h1 class="text-center mb-5 animate__animated animate__bounceIn">Chat dengan Kami</h1>
    <div class="row justify-content-center">
        <div class="col-md-8 animate__animated animate__fadeIn">
            <div class="card shadow-lg">
                <div class="card-body chat-box" style="height: 400px; overflow-y: auto;">
                    <p class="text-muted text-center">Halo! Ada yang bisa kami bantu?</p>
                </div>
                <div class="card-footer">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Ketik pesan...">
                        <button class="btn btn-warning"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
