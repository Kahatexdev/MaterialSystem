<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelMasterBuyer extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_buyer' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_buyer' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'nama_buyer' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
        ]);

        // Primary key
        $this->forge->addKey('id_buyer', true);

        // Create table
        $this->forge->createTable('master_buyer');
    }

    public function down()
    {
        $this->forge->dropTable('master_buyer');
    }
}
