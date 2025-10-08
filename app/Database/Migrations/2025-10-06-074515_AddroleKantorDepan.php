<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddroleKantorDepan extends Migration
{
    public function up()
    {
        // Tambah opsi 'kantordepan' ke ENUM role
        $fields = [
            'role' => [
                'name'       => 'role',
                'type'       => 'ENUM',
                'constraint' => ['gbn', 'celup', 'covering', 'area', 'monitoring', 'kantordepan'],
                'default'    => 'gbn',
            ],
        ];

        // Gunakan Forge agar portable di MySQL/MariaDB
        $this->forge->modifyColumn('user', $fields);
    }

    public function down()
    {
        // Pastikan tidak ada nilai 'kantordepan' sebelum ENUM dipersempit
        $this->db->table('user')
            ->where('role', 'kantordepan')
            ->set('role', 'gbn')
            ->update();

        // Kembalikan ENUM seperti semula (tanpa 'kantordepan')
        $fields = [
            'role' => [
                'name'       => 'role',
                'type'       => 'ENUM',
                'constraint' => ['gbn', 'celup', 'covering', 'area', 'monitoring'],
                'default'    => 'gbn',
            ],
        ];

        $this->forge->modifyColumn('user', $fields);
    }
}
