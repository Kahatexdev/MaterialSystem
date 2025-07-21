<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TambahClusterNylon2 extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_cluster' => 'L.01.C',
                'kapasitas' => '100',
                'group' => 'NYLON',
            ],
            [
                'nama_cluster' => 'L.02.C',
                'kapasitas' => '100',
                'group' => 'NYLON',
            ],
            [
                'nama_cluster' => 'L.03.C',
                'kapasitas' => '100',
                'group' => 'NYLON',
            ],
            [
                'nama_cluster' => 'L.04.C',
                'kapasitas' => '100',
                'group' => 'NYLON',
            ],
            [
                'nama_cluster' => 'L.05.C',
                'kapasitas' => '100',
                'group' => 'NYLON',
            ],
            [
                'nama_cluster' => 'L.01.D',
                'kapasitas' => '100',
                'group' => 'NYLON',
            ],
            [
                'nama_cluster' => 'L.02.D',
                'kapasitas' => '100',
                'group' => 'NYLON',
            ],
            [
                'nama_cluster' => 'L.03.D',
                'kapasitas' => '100',
                'group' => 'NYLON',
            ],
            [
                'nama_cluster' => 'L.04.D',
                'kapasitas' => '100',
                'group' => 'NYLON',
            ],
            [
                'nama_cluster' => 'L.05.D',
                'kapasitas' => '100',
                'group' => 'NYLON',
            ],
        ];

        $this->db->table('cluster')->insertBatch($data);
    }
}
