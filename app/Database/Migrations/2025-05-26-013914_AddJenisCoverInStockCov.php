<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJenisCoverInStockCov extends Migration
{
    public function up()
    {
        $this->forge->addColumn('stock_covering', [
            'jenis_cover' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'after' => 'jenis'
            ]
        ]);
        $this->forge->addColumn('history_stock_covering', [
            'jenis_benang' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'after' => 'jenis'
            ]
        ]);
        $this->forge->addColumn('history_stock_covering', [
            'jenis_cover' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'after' => 'jenis_benang'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('stock_covering', 'jenis_cover');
        $this->forge->dropColumn('history_stock_covering', 'jenis_benang');
        $this->forge->dropColumn('history_stock_covering', 'jenis_cover');
    }
}
