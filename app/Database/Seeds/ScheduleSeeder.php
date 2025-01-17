<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run()
    {
        $data = [
                    [
                        'id_mesin' => 13,
                        'no_po' => 'PO-2025-01-14-1',
                        'no_model' => 'LS0222',
                        'item_type' => 'CTN CD 20S ORG 100%',
                        'kode_warna' => 'KP.2710',
                        'warna' => 'WHITE',
                        'start_mc' => '2025-01-14 07:00:00',
                        'kg_celup' => 3.6,
                        'lot_celup' => '25AJKH',
                        'last_status' => 'ok',
                        'ket_daily_cek' => 'ok',
                        'user_cek_status' => 'gbn',
                        'created_at' => '2025-01-14 07:00:00',
                    ],
                ];

        // Using Query Builder
        $this->db->table('schedule_celup')->insertBatch($data);
    }
}
