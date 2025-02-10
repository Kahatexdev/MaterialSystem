<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldInTableStock extends Migration
{
    public function up()
    {
        $this->forge->addColumn('stock', [
            'krg_stock_awal' => [
                'type' => 'float',
                'after' => 'cns_stock_awal',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('stock', 'krg_stock_awal');
    }
}
