<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MesinCovering extends Seeder
{
    public function run()
    {
        $data = [
            [
                'no_mesin' => 1,
                'nama' => 'SINGLE COVERING',
                'jenis' => 'SINGLE COVERING',
                'buatan' => 'ITALY',
                'merk' => 'RPR',
                'type' => 'GSP 10/2',
                'jml_spindle' => 80,
                'tahun' => '1994',
                'jml_unit' => 1,
            ],
        ];

        // Using Query Builder
        $this->db->table('mesin_covering')->insertBatch($data);
    }
}
