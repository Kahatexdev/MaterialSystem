<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SetNULLAtScheduleField extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('open_po', [
            'item_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'default' => null,
            ],
            'kode_warna' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => true,
                'default' => null,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => true,
                'default' => null,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('open_po', [
            'item_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'default' => '',
            ],
            'kode_warna' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => false,
                'default' => '',
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => false,
                'default' => '',
            ],
        ]);
    }
}
