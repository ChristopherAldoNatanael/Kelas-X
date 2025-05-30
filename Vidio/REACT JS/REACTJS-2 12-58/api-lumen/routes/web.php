<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan semua rute untuk aplikasi Anda.
| Rute telah dikelompokkan berdasarkan fungsionalitas untuk menjaga kerapihan.
|
*/

// Route utama
$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Route untuk autentikasi
$router->group(['prefix' => 'api'], function () use ($router) {
    // Autentikasi pelanggan
    $router->post('/register-customer', 'RegisterCustomerController@register');
    $router->post('/login-customer', 'LoginCustomerController@login');

    // Autentikasi umum
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@loginApi');
    $router->group(['middleware' => 'auth:api'], function () use ($router) {
        $router->post('logout', 'AuthController@logoutApi');
        $router->get('me', 'AuthController@me');
        $router->post('refresh', 'AuthController@refresh');
    });
});

// Route untuk menu
$router->group(['prefix' => 'api/menu'], function () use ($router) {
    $router->get('/', 'MenuController@index');
    $router->get('/{id}', 'MenuController@show');
    $router->post('/', 'MenuController@create');
    $router->put('/{id}', 'MenuController@update');
    $router->delete('/{id}', 'MenuController@destroy');
});

// Route untuk kategori
$router->group(['prefix' => 'api/kategori'], function () use ($router) {
    $router->get('/', 'KategoriController@index');
    $router->get('/{id}', 'KategoriController@show');
    $router->post('/', 'KategoriController@create');
    $router->put('/{id}', 'KategoriController@update');
    $router->delete('/{id}', 'KategoriController@destroy');
});

// Route untuk pelanggan
$router->group(['prefix' => 'api/pelanggan'], function () use ($router) {
    $router->get('/', 'PelangganController@index'); // Ambil semua pelanggan
    $router->get('/email/{email}', 'PelangganController@getByEmail'); // Ambil pelanggan berdasarkan email
    $router->get('/{id}', 'PelangganController@show'); // Ambil pelanggan berdasarkan ID
    $router->post('/', 'PelangganController@create'); // Tambah pelanggan baru
    $router->put('/{id}', 'PelangganController@update'); // Update pelanggan
    $router->delete('/{id}', 'PelangganController@destroy'); // Hapus pelanggan
});

// Route untuk keranjang belanja (cart)
$router->group(['prefix' => 'api/cart', 'middleware' => 'auth'], function () use ($router) {
    $router->post('/', 'CartController@store'); // Tambah item ke cart
    $router->get('/{idpelanggan}', 'CartController@index'); // Ambil cart berdasarkan ID pelanggan
    $router->delete('/item/{idcart}', 'CartController@destroy'); // Hapus item dari cart
    $router->delete('/clear/{idpelanggan}', 'CartController@clear'); // Kosongkan cart
});

// Route untuk order
$router->group(['prefix' => 'api/order'], function () use ($router) {
    $router->get('/', 'OrderController@index');
    $router->get('/filter', 'OrderController@getByDate');
    $router->put('/{id}', 'OrderController@update');
});

// Route untuk detail
$router->group(['prefix' => 'api/detail'], function () use ($router) {
    $router->get('/', 'DetailController@index');
    $router->get('/filter', 'DetailController@getByDate');
});

// Route untuk user
$router->group(['prefix' => 'api/user'], function () use ($router) {
    $router->get('/', 'LoginController@index');
    $router->post('/register', 'LoginController@register');
    $router->post('/login', 'LoginController@login');
    $router->put('/{id}', 'LoginController@update');
    $router->delete('/{id}', 'LoginController@destroy');
});

// Route untuk admin
$router->group(['prefix' => 'admin', 'middleware' => 'admin'], function () use ($router) {
    $router->get('/dashboard', function () {
        return 'Halaman Admin Dashboard';
    });
    $router->get('/profile', function () {
        return 'Halaman Admin Profile';
    });
    $router->get('/settings', function () {
        return 'Halaman Admin Settings';
    });
});

// Route untuk melayani file statis dari direktori 'upload'
$router->get('/upload/{filename}', function ($filename) {
    $path = base_path('public/upload/' . $filename);
    if (!file_exists($path)) {
        return response()->json(['message' => 'Image not found'], 404);
    }
    return response()->file($path);
});

// Route untuk menangani preflight request (CORS)
$router->options('{any:.*}', function () {
    return response('', 200);
});
