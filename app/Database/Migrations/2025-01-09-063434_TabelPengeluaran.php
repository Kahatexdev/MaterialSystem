<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelPengeluaran extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pengeluaran' => [
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
            'area_out' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'tgl_out' => [
                'type' => 'DATE',
            ],
            'kgs_out' => [
                'type' => 'FLOAT',
            ],
            'cns_out' => [
                'type' => 'FLOAT',
            ],
            'krg_out' => [
                'type' => 'FLOAT',
            ],
            'lot_out' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'nama_cluster' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
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
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id_pengeluaran', true);
        $this->forge->addForeignKey('id_out_celup', 'out_celup', 'id_out_celup', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('nama_cluster', 'cluster', 'nama_cluster', 'CASCADE', 'CASCADE'); 
        $this->forge->createTable('pengeluaran');
    }

    public function down()
    {
        $this->forge->dropTable('pengeluaran');
    }
}
