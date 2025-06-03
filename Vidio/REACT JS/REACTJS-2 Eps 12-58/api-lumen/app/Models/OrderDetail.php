<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'details';
    protected $primaryKey = 'iddetail';
    protected $guarded = ['iddetail'];

    protected $fillable = [
        'idorder',
        'idmenu',
        'jumlah',
        'hargajual'
    ];

    // Relationship with Order
    public function order()
    {
        return $this->belongsTo(Order::class, 'idorder', 'idorder');
    }

    // Relationship with Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'idmenu', 'idmenu');
    }
}
