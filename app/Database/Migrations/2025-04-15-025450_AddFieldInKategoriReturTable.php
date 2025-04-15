<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldInKategoriReturTable extends Migration
{
    public function up()
    {
        $fields = [
            'keterangan_area' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'kategori'
            ]
        ];
        $this->forge->addColumn('retur', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('retur', 'keterangan_area');
    }
}
