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
                'type' => 'INT',
                'constraint' => 11,
            ],
            'min_caps' => [
                'type' => 'FLOAT',
            ],
            'max_caps' => [
                'type' => 'FLOAT',
            ],
            'jml_lot' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'lmd' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'ket_mesin' => [
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
        $this->forge->addKey('id_mesin', true);
        $this->forge->createTable('mesin_celup');
    }

    public function down()
    {
        $this->forge->dropTable('mesin_celup');
    }
}
