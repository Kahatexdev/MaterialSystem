<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterRangePemesanan extends Seeder
{
    public function run()
    {
        $data = [
            [
                'days' => 'Monday',
                'range_spandex' => 1,
                'range_karet' => 1,
                'range_benang' => 1,
                'range_nylon' => 1,
            ],
            [
                'days' => 'Tuesday',
                'range_spandex' => 1,
                'range_karet' => 1,
                'range_benang' => 1,
                'range_nylon' => 1,
            ],
            [
                'days' => 'Wednesday',
                'range_spandex' => 1,
                'range_karet' => 1,
                'range_benang' => 1,
                'range_nylon' => 1,
            ],
            [
                'days' => 'Thursday',
                'range_spandex' => 2,
                'range_karet' => 2,
                'range_benang' => 1,
                'range_nylon' => 1,
            ],
            [
                'days' => 'Friday',
                'range_spandex' => 1,
                'range_karet' => 1,
                'range_benang' => 1,
                'range_nylon' => 1,
            ],
            [
                'days' => 'Saturday',
                'range_spandex' => 1,
                'range_karet' => 1,
                'range_benang' => 2,
                'range_nylon' => 2,
            ],
            [
                'days' => 'Sunday',
                'range_spandex' => 0,
                'range_karet' => 0,
                'range_benang' => 0,
                'range_nylon' => 0,
            ],
        ];

        // Using Query Builder
        $this->db->table('mesin_celup')->insertBatch($data);
    }
}
