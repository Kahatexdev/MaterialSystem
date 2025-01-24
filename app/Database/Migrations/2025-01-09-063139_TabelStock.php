<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelStock extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
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
            'warna' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'kgs_stock_awal' => [
                'type' => 'FLOAT',
            ],
            'cns_stock_awal' => [
                'type' => 'FLOAT',
            ],
            'lot_awal' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'kgs_in_out' => [
                'type' => 'FLOAT',
            ],
            'cns_in_out' => [
                'type' => 'FLOAT',
            ],
            'krg_in_out' => [
                'type' => 'FLOAT',
            ],
            'lot_stock' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'nama_cluster' => [
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
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id_stock', true);
        $this->forge->addForeignKey('nama_cluster', 'cluster', 'nama_cluster', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock');
    }

    public function down()
    {
        $this->forge->dropTable('stock');
    }
}
