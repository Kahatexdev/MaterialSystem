<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SetNullFieldcovering extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('stock_covering', [
            'lmd' => [
                'name' => 'lmd',
                'type' => 'VARCHAR',
                'null' => true,
            ],
            'box' => [
                'name' => 'box',
                'type' => 'INT',
                'null' => true,
            ],
            'no_palet' => [
                'name' => 'no_palet',
                'type' => 'INT',
                'null' => true,
            ]
        ]);

        $this->forge->modifyColumn('history_stock_covering', [
            'lmd' => [
                'name' => 'lmd',
                'type' => 'VARCHAR',
                'null' => true,
            ],
            'box' => [
                'name' => 'box',
                'type' => 'INT',
                'null' => true,
            ],
            'no_palet' => [
                'name' => 'no_palet',
                'type' => 'INT',
                'null' => true,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('stock_covering', [
            'lmd' => [
                'name' => 'lmd',
                'type' => 'VARCHAR',
                'null' => false,
            ],
            'box' => [
                'name' => 'box',
                'type' => 'INT',
                'null' => false,
            ],
            'no_palet' => [
                'name' => 'no_palet',
                'type' => 'INT',
                'null' => false,
            ]
        ]);

        $this->forge->modifyColumn('history_stock_covering', [
            'lmd' => [
                'name' => 'lmd',
                'type' => 'VARCHAR',
                'null' => false,
            ],
            'box' => [
                'name' => 'box',
                'type' => 'INT',
                'null' => false,
            ],
            'no_palet' => [
                'name' => 'no_palet',
                'type' => 'INT',
                'null' => false,
            ]
        ]);
    }
}
