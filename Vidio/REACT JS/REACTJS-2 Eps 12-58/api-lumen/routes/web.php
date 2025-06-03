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
    // Auth routes for admin
    $router->post('/login', 'LoginController@login');
    $router->post('/register', 'LoginController@register');

    // Auth routes for customer
    $router->post('/login-customer', 'LoginCustomerController@login');
    $router->post('/register-customer', 'RegisterCustomerController@register');

    // Cart routes
    $router->put('/cart/item/{idcart}', 'CartController@updateQuantity');
    $router->delete('/cart/item/{idcart}', 'CartController@destroy');
    $router->post('/order', 'OrderController@store');

    // Order Detail Routes
    $router->get('order-details', 'OrderDetailController@index');
    $router->get('order-details/{idorder}', 'OrderDetailController@getByOrderId');
    $router->post('order-details', 'OrderDetailController@store');
    $router->put('order-details/{id}', 'OrderDetailController@update');
    $router->delete('order-details/{id}', 'OrderDetailController@destroy');
});

// Protected routes
$router->group(['middleware' => 'auth'], function () use ($router) {
    // Admin routes
    $router->group(['prefix' => 'admin'], function () use ($router) {
        $router->get('/dashboard', 'AdminController@dashboard');
        // ... other admin routes
    });

    // User management
    $router->get('/user', 'UserController@index');
    $router->post('/user', 'UserController@store');
    $router->put('/user/{id}', 'UserController@update');
    $router->delete('/user/{id}', 'UserController@destroy');
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
    $router->put('/{id}/password', 'PelangganController@updatePassword'); // Add this line
    $router->delete('/{id}', 'PelangganController@destroy'); // Hapus pelanggan
});

// Route untuk keranjang belanja (cart)
$router->group(['prefix' => 'api/cart'], function () use ($router) {
    $router->post('/', 'CartController@store'); // Tambah item ke cart
    $router->get('/{idpelanggan}', 'CartController@index'); // Ambil cart berdasarkan ID pelanggan
    $router->delete('/clear/{idpelanggan}', 'CartController@clear'); // Kosongkan cart
    $router->get('/details/{idpelanggan}', 'CartController@getCartWithDetails');
});

// Route untuk order
$router->group(['prefix' => 'api/order'], function () use ($router) {
    $router->get('/', 'OrderController@index');
    $router->get('/filter', 'OrderController@getByDate');
    $router->put('/{id}', 'OrderController@update');
    $router->get('/pelanggan/{id}', 'OrderController@getByPelanggan');
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
