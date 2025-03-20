<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StockCoveringTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_covering_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'jenis' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'lmd' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'ttl_cns' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'ttl_kg' => [
                'type' => 'FLOAT',
            ],
            'box' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'no_rak' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'posisi_rak' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'no_palet' => [
                'type' => 'INT',
                'constraint' => 11
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
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id_covering_stock', true);
        $this->forge->createTable('stock_covering');
    }

    public function down()
    {
        $this->forge->dropTable('stock_covering');
    }
}
