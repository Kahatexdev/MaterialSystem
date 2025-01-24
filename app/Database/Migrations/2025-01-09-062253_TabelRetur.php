<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelRetur extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_retur' => [
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
            'area_retur' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'tgl_retur' => [
                'type' => 'DATE',
            ],
            'kgs_retur' => [
                'type' => 'FLOAT',
            ],
            'cns_retur' => [
                'type' => 'FLOAT',
            ],
            'krg_retur' => [
                'type' => 'FLOAT',
            ],
            'lot_retur' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'keterangan_gbn' => [
                'type' => 'TEXT',
            ],
            'waktu_acc_retur' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('id_retur', true);
        $this->forge->addForeignKey('kategori', 'kategori_retur', 'nama_kategori', 'CASCADE', 'CASCADE');
        $this->forge->createTable('retur');
    }

    public function down()
    {
        $this->forge->dropTable('retur');
    }
}
