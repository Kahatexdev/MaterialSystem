<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterMaterialTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'material_type' => 'OCS BLENDED',
                'admin' => 'monitoring',
            ],
            [
                'material_type' => 'GOTS',
                'admin' => 'monitoring',
            ],
            [
                'material_type' => 'RCS BLENDED POST-CONSUMER',
                'admin' => 'monitoring',
            ],
            [
                'material_type' => 'BCI',
                'admin' => 'monitoring',
            ],
            [
                'material_type' => 'BCI-7',
                'admin' => 'monitoring',
            ],
            [
                'material_type' => 'BCI, ALOEVERA',
                'admin' => 'monitoring',
            ],
            [
                'material_type' => 'OCS BLENDED, ALOEVERA',
                'admin' => 'monitoring',
            ],
            [
                'material_type' => 'GRS BLENDED POST-CONSUMER',
                'admin' => 'monitoring',
            ],
            [
                'material_type' => 'ORGANIC IC2',
                'admin' => 'monitoring',
            ],
            [
                'material_type' => 'RCS BLENDED PRE-CONSUMER',
                'admin' => 'monitoring',
            ],
            [
                'material_type' => 'GRS BLENDED PRE-CONSUMER',
                'admin' => 'monitoring',
            ],
        ];

        $this->db->table('master_material_type')->insertBatch($data);
    }
}
