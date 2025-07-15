<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFKidStokInPemasukan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('po_tambahan', [
            'ket_gbn' => [
                'type' => 'TEXT',
                'after' => 'keterangan'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('po_tambahan', 'ket_gbn');
    }
}
