<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggans';
    protected $primaryKey = 'idpelanggan';
    protected $guarded = [];

    // Include timestamps since they exist in your table
    public $timestamps = true;

    protected $fillable = ['pelanggan', 'email', 'alamat', 'telp', 'password', 'api_token'];
    protected $hidden = ['password'];
}
