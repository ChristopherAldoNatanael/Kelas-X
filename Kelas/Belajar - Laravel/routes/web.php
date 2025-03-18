<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController; // Impor PageController

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/menu', [PageController::class, 'menu'])->name('menu');
Route::get('/order', [PageController::class, 'order'])->name('order');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/chat', [PageController::class, 'chat'])->name('chat');
