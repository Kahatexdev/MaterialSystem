<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTerimaArea extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pengeluaran', [
            'terima_area' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pengeluaran', 'terima_area');
    }
}
