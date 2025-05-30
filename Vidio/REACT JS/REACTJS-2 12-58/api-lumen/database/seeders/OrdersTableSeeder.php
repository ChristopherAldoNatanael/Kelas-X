<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('orders')->insert([
            [
                'idpelanggan' => 40,
                'tglorder'    => '2025-05-27',
                'total'       => 20000,
                'bayar'       => 25000,
                'kembali'     => 5000,
                'status'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'idpelanggan' => 50,
                'tglorder'    => '2025-05-26',
                'total'       => 15000,
                'bayar'       => 15000,
                'kembali'     => 0,
                'status'      => 0,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'idpelanggan' => 3,
                'tglorder'    => '2025-05-25',
                'total'       => 30000,
                'bayar'       => 30000,
                'kembali'     => 0,
                'status'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]
        ]);
    }
}
