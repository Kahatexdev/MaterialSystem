<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnOperatorAndShiftInOutCelup extends Migration
{
    public function up()
    {
        $this->forge->addColumn('out_celup', [
            'operator_packing' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'ganti_retur'
            ],
            'shift' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'operator_packing'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('out_celup', ['operator_packing', 'shift']);
    }
}
