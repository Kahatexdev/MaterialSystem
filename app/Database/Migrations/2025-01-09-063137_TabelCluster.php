<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelCluster extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'nama_cluster' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'kapasitas' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
            'group' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
        $this->forge->addKey('nama_cluster', true);
        $this->forge->createTable('cluster');
    }

    public function down()
    {
        $this->forge->dropTable('cluster');
    }
}
