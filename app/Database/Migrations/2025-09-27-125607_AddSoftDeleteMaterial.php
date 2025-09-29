<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteMaterial extends Migration
{
    public function up()
    {
        $this->forge->addColumn('material', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('material', 'deleted_at');
    }
}
