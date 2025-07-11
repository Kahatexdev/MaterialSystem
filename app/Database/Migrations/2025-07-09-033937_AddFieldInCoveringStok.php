<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldInCoveringStok extends Migration
{
    public function up()
    {
        $fields = [
            'jenis_mesin' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'dr' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
        ];

        $this->forge->addColumn('stock_covering', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('stock_covering', 'jenis_mesin');
        $this->forge->dropColumn('stock_covering', 'dr');
    }
}
