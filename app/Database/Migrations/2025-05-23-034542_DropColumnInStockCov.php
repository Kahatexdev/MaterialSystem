<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropColumnInStockCov extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('stock_covering', ['box', 'no_rak', 'posisi_rak', 'no_palet']);
        $this->forge->dropColumn('history_stock_covering', ['box', 'no_rak', 'posisi_rak', 'no_palet']);
    }

    public function down()
    {
        $fields = [
            'box' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'no_rak' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'posisi_rak' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'no_palet' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
        ];

        $this->forge->addColumn('stock_covering', $fields);

        $this->forge->addColumn('history_stock_covering', [
            'box' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'no_rak' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'posisi_rak' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'no_palet' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
        ]);

    }
}
