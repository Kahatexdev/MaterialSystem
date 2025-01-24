<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelPemasukan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pemasukan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_out_celup' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_retur' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
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
            'kgs_masuk' => [
                'type' => 'FLOAT',
            ],
            'cns_masuk' => [
                'type' => 'FLOAT',
            ],
            'tgl_masuk' => [
                'type' => 'DATE',
            ],
            'nama_cluster' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'history_order' => [
                'type' => 'TEXT',
            ],
            'history_jalur' => [
                'type' => 'TEXT',
            ],
            'out_jalur' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1'],
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
        $this->forge->addKey('id_pemasukan', true);
        $this->forge->addForeignKey('id_out_celup', 'out_celup', 'id_out_celup', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_retur', 'retur', 'id_retur', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('nama_cluster', 'cluster', 'nama_cluster', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pemasukan');
    }

    public function down()
    {
        $this->forge->dropTable('pemasukan');
    }
}
