<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterJenis extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_jenis' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'jenis' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'kode' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id_jenis', true);
        $this->forge->createTable('master_jenis');
    }

    public function down()
    {
        //
    }
}
