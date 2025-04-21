<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NullIdCelupAtOutCelup extends Migration
{
    public function up()
    {
        // Drop FK dulu
        $this->db->query('ALTER TABLE `out_celup` DROP FOREIGN KEY `out_celup_id_celup_foreign`');

        // Modify kolom
        $this->forge->modifyColumn('out_celup', [
            'id_celup' => [
                'name'       => 'id_celup',
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
        ]);

        // Tambahkan FK kembali jika perlu
        $this->forge->addForeignKey('id_celup', 'schedule_celup', 'id_celup', 'SET NULL', 'CASCADE', 'out_celup_id_celup_foreign');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `out_celup` DROP FOREIGN KEY `out_celup_id_celup_foreign`');

        $this->forge->modifyColumn('out_celup', [
            'id_celup' => [
                'name'       => 'id_celup',
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'default'    => '',
            ],
        ]);

        $this->forge->addForeignKey('id_celup', 'schedule_celup', 'id_celup', 'CASCADE', 'CASCADE', 'out_celup_id_celup_foreign');
        

    }
}
