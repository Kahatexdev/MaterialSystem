<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelPoTambahan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_po_tambahan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'no_model' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'item_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'kode_warna' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'kg_po_tambahan' => [
                'type' => 'FLOAT',
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'admin' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                null => true,
            ],
        ]);
        $this->forge->addKey('id_po_tambahan', true);
        $this->forge->createTable('po_tambahan');
    }

    public function down()
    {
        $this->forge->dropTable('po_tambahan');
    }
}
