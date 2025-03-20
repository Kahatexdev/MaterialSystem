<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CoveringStockSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'jenis' => 'Nylon 70D/24F',
                'color' => 'Red',
                'code' => 'RD-001',
                'lmd' => 'LMD',
                'ttl_cns' => 500,
                'ttl_kg' => 250.5,
                'box' => 10,
                'no_rak' => 3,
                'posisi_rak' => 'Atas, Kiri',
                'no_palet' => 12,
                'admin' => 'John Doe',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'jenis' => 'Polyester 150D/48F',
                'color' => 'Blue',
                'code' => 'BL-002',
                'lmd' => 'LMD',
                'ttl_cns' => 400,
                'ttl_kg' => 200.8,
                'box' => 8,
                'no_rak' => 5,
                'posisi_rak' => 'Bawah, Kanan',
                'no_palet' => 9,
                'admin' => 'Jane Smith',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'jenis' => 'Cotton 40s',
                'color' => 'White',
                'code' => 'WH-003',
                'lmd' => 'LMD',
                'ttl_cns' => 600,
                'ttl_kg' => 300.2,
                'box' => 12,
                'no_rak' => 7,
                'posisi_rak' => 'Kanan, Atas',
                'no_palet' => 15,
                'admin' => 'Michael Johnson',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
        ];

        $this->db->table('stock_covering')->insertBatch($data);
    }
}
