<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $primaryKey = 'idcart';

    protected $fillable = [
        'idpelanggan',
        'idmenu',
        'qty'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'idmenu', 'idmenu');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'idpelanggan', 'idpelanggan');
    }
}
