<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class WareouseBBCovering extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idstockbb' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'denier' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'jenis_benang' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'warna' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'kode' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'kg' => [
                'type' => 'FLOAT',
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
            'admin' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('idstockbb', true);
        $this->forge->createTable('stock_bb_covering', true);
    }

    public function down()
    {
        $this->forge->dropTable('stock_bb_covering', true);
    }
}
