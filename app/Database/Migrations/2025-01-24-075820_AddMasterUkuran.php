<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMasterUkuran extends Migration
{
    public function up()
    {
        $this->forge->addColumn('master_material', [
            'ukuran' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => true,  // Jika kamu ingin kolom ini boleh kosong
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('mastermaterial', 'ukuran');
    }
}
