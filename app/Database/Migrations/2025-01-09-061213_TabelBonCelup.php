<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelBonCelup extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bon' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_celup' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tgl_datang' => [
                'type' => 'DATE',
            ],
            'l_m_d' => [
                'type' => 'ENUM',
                'constraint' => ['L', 'M', 'D'],
            ],
            'harga' => [
                'type' => 'FLOAT',
            ],
            'gw' => [
                'type' => 'FLOAT',
            ],
            'nw' => [
                'type' => 'FLOAT',
            ],
            'no_surat_jalan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'detail_sj' => [
                'type' => 'TEXT',
            ],
            'ganti_retur' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1'],
            ],
            'admin' => [
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
        $this->forge->addKey('id_bon', true);
        $this->forge->addForeignKey('id_celup', 'schedule_celup', 'id_celup', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bon_celup');
    }

    public function down()
    {
        $this->forge->dropTable('bon_celup');
    }
}
