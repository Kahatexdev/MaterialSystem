<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnPoTambahanOtherIn extends Migration
{
    public function up()
    {
        $fields = [
            'po_tambahan' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],   // ENUM hanya boleh 0 atau 1
                'null'       => true,
                'after'      => 'ganti_retur',
            ],
        ];

        $this->forge->addColumn('other_bon', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('other_bon', ['po_tambahan']);
    }
}
