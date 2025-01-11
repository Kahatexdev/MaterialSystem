<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelOutCelup extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_out_celup' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_bon' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_celup' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'kgs_kirim' => [
                'type' => 'FLOAT',
            ],
            'cones_kirim' => [
                'type' => 'FLOAT',
            ],
            'lot_kirim' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addKey('id_out_celup', true);
        $this->forge->addForeignKey('id_bon', 'bon_celup', 'id_bon', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_celup', 'mesin_celup', 'id_mesin', 'CASCADE', 'CASCADE');
        $this->forge->createTable('out_celup');
    }

    public function down()
    {
        $this->forge->dropTable('out_celup');
    }
}
