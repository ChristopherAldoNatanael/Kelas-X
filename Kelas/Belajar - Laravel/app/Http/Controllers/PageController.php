<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function menu()
    {
        return view('menu');
    }

    public function order()
    {
        return view('order');
    }

    public function contact()
    {
        return view('contact');
    }

    public function chat()
    {
        return view('chat');
    }
}
