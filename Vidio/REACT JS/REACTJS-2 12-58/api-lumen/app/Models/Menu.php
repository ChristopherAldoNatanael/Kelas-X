<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $primaryKey = 'idmenu'; // Tambahkan ini
    protected $fillable = [
        'idkategori',
        'menu',
        'gambar',
        'harga',
        'deskripsi'
    ];

    // Jika idmenu bukan auto-incrementing, tambahkan ini:
    // public $incrementing = false;

    // Jika idmenu bukan integer, tambahkan ini:
    // protected $keyType = 'string';
}
