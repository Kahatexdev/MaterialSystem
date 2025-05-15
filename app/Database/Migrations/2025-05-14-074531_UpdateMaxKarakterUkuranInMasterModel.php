<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateMaxKarakterUkuranInMasterModel extends Migration
{
    public function up()
    {
        // Modify the "ukuran" field in the "master_material" table
        $this->forge->modifyColumn('master_material', [
            'ukuran' => [
                'type' => 'VARCHAR',
                'constraint' => 100, // Update the length to 100
            ],
        ]);
    }

    public function down()
    {
        // Revert the "ukuran" field in the "master_material" table
        $this->forge->modifyColumn('master_material', [
            'ukuran' => [
                'type' => 'VARCHAR',
                'constraint' => 5, // Revert the length to 5
            ],
        ]);
    }
}
