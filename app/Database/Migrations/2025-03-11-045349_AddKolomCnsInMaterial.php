<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKolomCnsInMaterial extends Migration
{
    public function up()
    {
        $this->forge->addColumn('material', [
            'qty_cns' => [
                'type' => 'float',
                'after' => 'admin', // Tambahkan setelah kolom 'admin'
            ],
            'qty_berat_cns' => [
                'type' => 'float',
                'after' => 'qty_cns', // Tambahkan setelah kolom 'column1'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('material', ['qty_cns', 'qty_berat_cns']);
    }
}
