<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HistoryStockBBCovering extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_history_stockbb' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'denier' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'jenis' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'jenis_benang' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'ttl_cns' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'ttl_kg' => [
                'type'       => 'FLOAT',
                'constraint' => 10, // Adjusted to allow decimal values
            ],
            'admin' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'keterangan' => [
                'type'       => 'TEXT',
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

        $this->forge->addKey('id_history_stockbb', true);
        $this->forge->createTable('history_stockbb');
    }

    public function down()
    {
        $this->forge->dropTable('history_stockbb', true);
    }
}
