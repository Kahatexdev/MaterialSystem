<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganInMaterialTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('material', [
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'kgs'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('material', 'keterangan');
    }
}
