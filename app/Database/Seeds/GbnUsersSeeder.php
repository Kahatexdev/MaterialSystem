<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GbnUsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'MEGAH',
                'password' => 'KHGBSOCKS4',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'ASTRI',
                'password' => 'GBKHSOCKS7',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'YANI YOLANDA',
                'password' => 'SOCKSKGHB8',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'NIA KURNIA',
                'password' => 'KGSOCKSHB12',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'NOPI TALUPI',
                'password' => 'KBSOCKSHG15',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'RENI ANGGRAENI',
                'password' => 'RENI543',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'rpl_team',
                'password' => 'rpl1234',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'Ardtharia Pertiwi',
                'password' => 'ARDTHARIA34',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'AYU',
                'password' => 'SOCKSGBKH4',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'DEDAH',
                'password' => 'KBSOCKHG16',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'RIA',
                'password' => 'KBSOCKHG18',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'SITI NUR KHASANAH',
                'password' => 'GBN223',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'TINA MARTIANA',
                'password' => 'TINA654',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'ALIKA',
                'password' => 'ALI789',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'SITI ATIKAH',
                'password' => 'ST1234',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'RIA APRILIANI',
                'password' => 'RIA566',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'NADYA NURUL',
                'password' => 'NAD222',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'NENG SRI',
                'password' => 'NS212',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'PUPU SF',
                'password' => 'NENENG',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'MIRA',
                'password' => 'MR248',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'NENG ROSITA',
                'password' => 'NR123',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'WULANIA ROSMAYA',
                'password' => 'WR85246',
                'role'     => 'gbn',
                'area'     => NULL,
            ],
            [
                'username' => 'NADELA VIRGA',
                'password' => 'NADELA4568',
                'role'     => 'gbn',
                'area'     => NULL,
            ],

        ];
        $this->db->table('user')->insertBatch($data);
    }
}
