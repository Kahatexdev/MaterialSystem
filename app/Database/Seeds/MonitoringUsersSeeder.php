<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MonitoringUsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'AZIZAH',
                'password' => 'BPAZIZAH123',
                'role'     => 'monitoring',
                'area'     => NULL,
            ],
            [
                'username' => 'AGNES',
                'password' => 'BPAGNES123',
                'role'     => 'monitoring',
                'area'     => NULL,
            ],
            [
                'username' => 'INDRI',
                'password' => 'BPINDRI123',
                'role'     => 'monitoring',
                'area'     => NULL,
            ],
            [
                'username' => 'NURUL',
                'password' => 'BPNURUL123',
                'role'     => 'monitoring',
                'area'     => NULL,
            ],
            [
                'username' => 'TITA',
                'password' => 'BPTITA123',
                'role'     => 'monitoring',
                'area'     => NULL,
            ],
            [
                'username' => 'AYU',
                'password' => 'BPAYU123',
                'role'     => 'monitoring',
                'area'     => NULL,
            ],
            [
                'username' => 'INAY',
                'password' => 'BPINAY123',
                'role'     => 'monitoring',
                'area'     => NULL,
            ]
        ];
        $this->db->table('user')->insertBatch($data);
    }
}
