<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelMaterial extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_material' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'style_size' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'inisial' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'item_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'kode_warna' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'composition' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'gw' => [
                'type' => 'FLOAT',
            ],
            'qty_pcs' => [
                'type' => 'FLOAT',
            ],
            'loss' => [
                'type' => 'FLOAT',
            ],
            'kgs' => [
                'type' => 'FLOAT',
            ],
            'admin' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id_material', true);
        $this->forge->addForeignKey('id_order', 'master_order', 'id_order', 'CASCADE', 'CASCADE');
        $this->forge->createTable('material');
    }

    public function down()
    {
        $this->forge->dropTable('material');
    }
}
