<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnInStockCov extends Migration
{
    public function up()
    {
        $this->forge->addColumn('stock_covering', [
            'jenis_benang' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'after' => 'jenis'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('stock_covering', 'jenis_benang');
    }
}
