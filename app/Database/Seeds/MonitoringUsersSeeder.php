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
                'password' =>  password_hash('sockkaha92', PASSWORD_BCRYPT),
                'role'     => 'monitoring',
                'area'     => NULL,
            ], // password: sockkaha92

            [
                'username' => 'INDRI',
                'password' =>  password_hash('mtrlgbn07', PASSWORD_BCRYPT),
                'role'     => 'monitoring',
                'area'     => NULL,
            ], // password: mtrlgbn07

            [
                'username' => 'AGNES',
                'password' =>  password_hash('kaha8842', PASSWORD_BCRYPT),
                'role'     => 'monitoring',
                'area'     => NULL,
            ], // password: kaha8842

            [
                'username' => 'AYU',
                'password' =>  password_hash('AYUCHANG456', PASSWORD_BCRYPT),
                'role'     => 'monitoring',
                'area'     => NULL,
            ], // password: AYUCHANG456

            [
                'username' => 'SUMINAR',
                'password' =>  password_hash('gbnmtrl63', PASSWORD_BCRYPT),
                'role'     => 'monitoring',
                'area'     => NULL,
            ], // password: gbnmtrl63

            [
                'username' => 'TITA',
                'password' =>  password_hash('kahatex02', PASSWORD_BCRYPT),
                'role'     => 'monitoring',
                'area'     => NULL,
            ], // password: kahatex02

            [
                'username' => 'NURUL',
                'password' =>  password_hash('mtrlkaha3', PASSWORD_BCRYPT),
                'role'     => 'monitoring',
                'area'     => NULL,
            ], // password: mtrlkaha3

        ];
        $this->db->table('user')->insertBatch($data);
    }
}
