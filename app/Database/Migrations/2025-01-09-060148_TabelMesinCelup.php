<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelMesinCelup extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_mesin' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'no_mesin' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'min_caps' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'max_caps' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'ket_mesin' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'desc' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('id_mesin', true);
        $this->forge->createTable('mesin_celup');
    }

    public function down()
    {
        //
    }
}
