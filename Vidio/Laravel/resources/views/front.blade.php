<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Aplikasi Restoran SMK</title>
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
</head>
<body>
    <div class="container">
        <div class="mt-3">
            <nav class="navbar navbar-expand-lg bg-light">
                <div class="container-fluid">
                    <a href="/"><img style="width: 80px" src="{{ asset('gambar/LOGO.png') }}" alt=""></a>
                    <ul class="navbar-nav gap-5">
                        <li class="nav-item">Cart</li>
                        @if (session()->missing('idpelanggan'))
                            <li class="nav-item"><a href="{{ url('register') }}">Register</a></li>
                            <li class="nav-item"><a href="{{ url('login') }}">Login</a></li>
                        @endif
                       
                        @if (session()->has('idpelanggan'))
                            <li class="nav-item">    {{ session('idpelanggan')['email'] }}  </li>  
                            <li class="nav-item"><a href="{{ url('logout') }}">Logout</a></li>
                        @endif
                        
                    </ul>
                </div>
            </nav>
        </div>
        <div class="row mt-4">
           <div class="col-2">
            <ul class="list-group">
            @foreach ($kategoris as $kategori )
                <li class="list-group-item"><a href="{{ url('show/'.$kategori->idkategori) }}">{{ $kategori -> kategori }}</a></li>
            @endforeach
            </ul>
            
           </div>
           <div class="col-10">
            @yield('content')
            @yield('isi')
           </div>
        </div>
        <div>
            footer
        </div>
    </div>

    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>