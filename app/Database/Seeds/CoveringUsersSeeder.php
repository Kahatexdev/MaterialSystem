<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CoveringUsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'Paryanti',
                'password' =>  password_hash('cov591', PASSWORD_BCRYPT),
                'role'     => 'covering',
                'area'     => NULL,
            ], // password: sock807
        ];
        $this->db->table('user')->insertBatch($data);
    }
}
