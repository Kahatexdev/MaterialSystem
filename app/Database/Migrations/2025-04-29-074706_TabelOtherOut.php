<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelOtherOut extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_other_out' => [
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
            'kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'tgl_other_out' => [
                'type' => 'DATE',
            ],
            'kgs_other_out' => [
                'type' => 'FLOAT',
            ],
            'cns_other_out' => [
                'type' => 'FLOAT',
            ],
            'krg_other_out' => [
                'type' => 'FLOAT',
            ],
            'lot_other_out' => [
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
        $this->forge->addKey('id_other_out', true);
        $this->forge->addForeignKey('id_out_celup', 'out_celup', 'id_out_celup', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('nama_cluster', 'cluster', 'nama_cluster', 'CASCADE', 'CASCADE');
        $this->forge->createTable('other_out');
    }

    public function down()
    {
        $this->forge->dropTable('other_out');
    }
}
