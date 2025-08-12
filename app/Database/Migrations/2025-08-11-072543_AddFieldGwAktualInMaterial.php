<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldGwAktualInMaterial extends Migration
{
    public function up()
    {
        $this->forge->addColumn('material', [
            'gw_aktual' => [
                'type' => 'float',
                'after' => 'gw'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('material', 'gw_aktual');
    }
}
