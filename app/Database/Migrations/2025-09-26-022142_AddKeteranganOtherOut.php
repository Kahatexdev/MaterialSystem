<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganOtherOut extends Migration
{
    public function up()
    {
        $this->forge->addColumn('other_out', [
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'nama_cluster'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('other_out', 'keterangan');
    }
}
