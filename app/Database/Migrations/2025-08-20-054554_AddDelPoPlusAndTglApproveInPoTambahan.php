<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDelPoPlusAndTglApproveInPoTambahan extends Migration
{
    public function up()
    {
        $fields = [
            'delivery_po_plus' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'lebih_pakai_kg',
            ],
            'tanggal_approve' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'delivery_po_plus',
            ],
        ];

        $this->forge->addColumn('po_tambahan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('po_tambahan', ['delivery_po_plus', 'tanggal_approve']);
    }
}
