<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldInOpenPO extends Migration
{
    public function up()
    {
        $this->forge->addColumn('open_po', [
            'id_induk' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('open_po', 'id_induk');
    }
}
