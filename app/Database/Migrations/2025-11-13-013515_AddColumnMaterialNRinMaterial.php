<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnMaterialNRinMaterial extends Migration
{
    public function up()
    {
        $fields = [
            'material_nr' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'after'      => 'id_order', // specify the column after which to add the new column
                'null'       => true,
            ],
        ];
        $this->forge->addColumn('material', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('material', 'material_nr');
    }
}
