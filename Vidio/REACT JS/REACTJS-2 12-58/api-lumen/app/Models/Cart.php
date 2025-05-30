<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $primaryKey = 'idcart';
    protected $fillable = ['idpelanggan', 'idmenu', 'qty'];
    public $timestamps = true;

    public function menu()
    {
        return $this->belongsTo(\App\Models\Menu::class, 'idmenu', 'idmenu');
    }
}
