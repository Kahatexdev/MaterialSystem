<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NullIdBonAtOutCelup extends Migration
{
    public function up()
    {
        // Drop FK dulu
        $this->db->query('ALTER TABLE `out_celup` DROP FOREIGN KEY `out_celup_id_bon_foreign`');

        // Modify kolom
        $this->forge->modifyColumn('out_celup', [
            'id_bon' => [
                'name'       => 'id_bon',
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
        ]);

        // Tambahkan FK kembali jika perlu
        $this->forge->addForeignKey('id_bon', 'bon', 'id_bon', 'SET NULL', 'CASCADE', 'out_celup_id_bon_foreign');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `out_celup` DROP FOREIGN KEY `out_celup_id_bon_foreign`');

        $this->forge->modifyColumn('out_celup', [
            'id_bon' => [
                'name'       => 'id_bon',
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'default'    => '',
            ],
        ]);

        $this->forge->addForeignKey('id_bon', 'bon', 'id_bon', 'CASCADE', 'CASCADE', 'out_celup_id_bon_foreign');
    }
}
