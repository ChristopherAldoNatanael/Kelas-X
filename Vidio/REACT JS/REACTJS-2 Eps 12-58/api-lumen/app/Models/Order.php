<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'idorder';
    protected $guarded = ['idorder'];

    protected $fillable = [
        'idpelanggan',
        'tglorder',
        'total',
        'bayar',
        'kembali',
        'status',
        'payment_method'
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'idorder', 'idorder')
            ->from('details'); // Explicitly specify the table name
    }
}
