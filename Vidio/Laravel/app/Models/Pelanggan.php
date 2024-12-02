<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pelanggan extends Model
{
    use HasFactory;
    protected $fillable = [
        'pelanggan',
        'email',
        'password',
        'alamat',
        'telp',
    ];
}