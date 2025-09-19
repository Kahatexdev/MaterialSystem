<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStartMcMasterOrder extends Migration
{
    public function up()
    {
        $fields = [
            'start_mc' => [
                'type'       => 'datetime',
                'null'       => true,
                'after'      => 'delivery_akhir',
            ],
        ];
        $this->forge->addColumn('master_order', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('master_order', ['start_mc']);
    }
}
