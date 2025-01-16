<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelMasterMaterial extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'item_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'deskripsi' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'jenis' => [
                'type' => 'ENUM',
                'constraint' => ['BENANG', 'NYLON', 'SPANDEX', 'KARET'],
            ],  
        ]);
        $this->forge->addKey('item_type', true);
        $this->forge->createTable('master_material');
    }

    public function down()
    {
        $this->forge->dropTable('master_material');
    }
}
