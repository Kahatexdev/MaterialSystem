<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelEstimasiStok extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sm' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'no_model_old' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'no_model_new' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'item_type' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'kode_warna' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'lot' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'kg_stock' => [
                'type' => 'FLOAT',
            ],
            'karung_stock' => [
                'type' => 'FLOAT',
            ],
            'cones_stock' => [
                'type' => 'FLOAT',
            ],
            'kg_aktual' => [
                'type' => 'FLOAT',
            ],
            'krg_aktual' => [
                'type' => 'FLOAT',
            ],
            'cns_aktual' => [
                'type' => 'FLOAT',
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
        $this->forge->addKey('id_sm', true);
        $this->forge->createTable('estimasi_stok');
    }

    public function down()
    {
        $this->forge->dropTable('estimasi_stok');
    }
}
