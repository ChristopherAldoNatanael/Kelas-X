<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggans';
    protected $primaryKey = 'idpelanggan';

    protected $fillable = [
        'pelanggan',
        'email',
        'password',
        'api_token',
        'alamat',
        'telp'
    ];

    protected $hidden = [
        'password',
        'api_token'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
