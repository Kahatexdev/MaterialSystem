<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'monitoring',
                'password' => 'monitoring',
                'role'    => 'monitoring',
                'area'    => NULL,
            ],
            [
                'username' => 'gbn',
                'password' => 'gbn',
                'role'    => 'gbn',
                'area'    => NULL,
            ],
            [
                'username' => 'celup',
                'password' => 'celup',
                'role'    => 'celup',
                'area'    => NULL,
            ],
            [
                'username' => 'covering',
                'password' => 'covering',
                'role'    => 'covering',
                'area'    => NULL,
            ],
            [
                'username' => 'area',
                'password' => 'area',
                'role'    => 'area',
                'area'    => 'area',
            ],
        ];
        $this->db->table('user')->insertBatch($data);
    }
}
