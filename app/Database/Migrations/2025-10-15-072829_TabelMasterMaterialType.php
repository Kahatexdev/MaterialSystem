<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelMasterMaterialType extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'material_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'admin' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('material_type', true);
        $this->forge->createTable('master_material_type');
    }

    public function down()
    {
        $this->forge->dropTable('master_material_type');
    }
}
