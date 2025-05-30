<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategoris';
    protected $primaryKey = 'idkategori';
    public $timestamps = false;
    protected $guarded = [];
}
