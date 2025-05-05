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
                'password' =>  password_hash('sock807', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: sock807
            [
                'username' => 'ASTRI',
                'password' =>  password_hash('mtrl775', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: mtrl775
            [
                'username' => 'YANI YOLANDA',
                'password' =>  password_hash('gbnmtrl225', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: gbnmtrl225
            [
                'username' => 'NIA KURNIA',
                'password' =>  password_hash('kaoskakigb', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: kaoskakigb
            [
                'username' => 'NOPI TALUPI',
                'password' =>  password_hash('kahatex917', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: kahatex917
            [
                'username' => 'RENI ANGGRAENI',
                'password' =>  password_hash('kahagbnt87', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: kahagbnt87
            [
                'username' => 'Ardtharia Pertiwi',
                'password' =>  password_hash('mtrlkaos6', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: mtrlkaos6
            [
                'username' => 'AYU',
                'password' =>  password_hash('gbn7321', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: gbn7321
            [
                'username' => 'DEDAH',
                'password' =>  password_hash('sockmtrl4', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: sockmtrl4
            [
                'username' => 'RIA',
                'password' =>  password_hash('kahatex23', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: kahatex23
            [
                'username' => 'SITI NUR KHASANAH',
                'password' =>  password_hash('kaha9182', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: kaha9182
            [
                'username' => 'TINA MARTIANA',
                'password' =>  password_hash('sockmtrl7', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: sockmtrl7
            [
                'username' => 'ALIKA',
                'password' =>  password_hash('gbnkaha53', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: gbnkaha53
            [
                'username' => 'SITI ATIKAH',
                'password' =>  password_hash('mtrlkaha1', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: mtrlkaha1
            [
                'username' => 'RIA APRILIANI',
                'password' =>  password_hash('gbnmtrl20', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: gbnmtrl20
            [
                'username' => 'NADYA NURUL',
                'password' =>  password_hash('kaha4312', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: kaha4312
            [
                'username' => 'NENG SRI',
                'password' =>  password_hash('mtrlgbn93', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: mtrlgbn93
            [
                'username' => 'PUPU SF',
                'password' =>  password_hash('sockkaha4', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: sockkaha4
            [
                'username' => 'MIRA',
                'password' =>  password_hash('kahatex10', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: kahatex10
            [
                'username' => 'NENG ROSITA',
                'password' =>  password_hash('gbnmtrl1', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: gbnmtrl1
            [
                'username' => 'WULANIA ROSMAYA',
                'password' =>  password_hash('sock972', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: sock972
            [
                'username' => 'NADELA VIRGA',
                'password' =>  password_hash('gbn2463', PASSWORD_BCRYPT),
                'role'     => 'gbn',
                'area'     => NULL,
            ], // password: gbn2463


        ];
        $this->db->table('user')->insertBatch($data);
    }
}
