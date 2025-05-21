<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPoPlusInOpenPo extends Migration
{
    public function up()
    {
        $this->forge->addColumn('open_po', [
            'po_plus' => [
                'type' => 'ENUM',
                'constraint' => ['1', '0'],
                'after' => 'penanggung_jawab',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('open_po', 'po_plus');
    }
}
