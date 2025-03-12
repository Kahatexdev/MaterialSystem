<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateEnumJenisMasterMaterial extends Migration
{
    public function up()
    {
        $fields = [
            'jenis' => [
                'type' => 'ENUM',
                'constraint' => ['BENANG', 'KARET', 'NYLON', 'SPANDEX'],
            ],
        ];

        $this->forge->modifyColumn('master_material', $fields);
    }

    public function down()
    {
        $fields = [
            'jenis' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'], // Kembali ke enum lama
                'default' => 'active',
            ],
        ];

        $this->forge->modifyColumn('master_material', $fields);
    }
}
