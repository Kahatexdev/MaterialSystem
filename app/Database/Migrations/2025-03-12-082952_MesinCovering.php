<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MesinCovering extends Migration
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
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'jenis' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'buatan' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'merk' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'jml_spindle' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'tahun' => [
                'type' => 'VARCHAR',
                'constraint' => 4,
            ],
            'jml_unit' => [
                'type' => 'INT',
                'constraint' => 11,
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
        $this->forge->createTable('mesin_covering');
    }

    public function down()
    {
        $this->forge->dropTable('mesin_covering');
    }
}
