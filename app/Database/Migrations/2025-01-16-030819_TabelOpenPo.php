<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OpenPo extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_po' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'no_model' => [
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
            'kg_po' => [
                'type' => 'FLOAT',
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
            'penanggung_jawab' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
                null => true,
            ],
        ]);
        $this->forge->addKey('id_po', true);
        $this->forge->createTable('open_po');
    }

    public function down()
    {
        $this->forge->dropTable('open_po');
    }
}
