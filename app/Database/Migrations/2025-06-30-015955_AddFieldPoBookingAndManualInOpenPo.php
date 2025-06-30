<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldPoBookingAndManualInOpenPo extends Migration
{
    public function up()
    {
        // Tambahkan po_booking
        $this->forge->addColumn('open_po', [
            'po_booking' => [
                'type'       => 'ENUM',
                'constraint' => ['1', '0'],
                'default'    => '0',
                'after'      => 'po_plus',
            ],
        ]);

        // Tambahkan po_manual
        $this->forge->addColumn('open_po', [
            'po_manual' => [
                'type'       => 'ENUM',
                'constraint' => ['1', '0'],
                'default'    => '0',
                'after'      => 'po_booking',
            ],
        ]);
    }

    public function down()
    {
        // Hapus po_booking dan po_manual
        $this->forge->dropColumn('open_po', 'po_booking');
        $this->forge->dropColumn('open_po', 'po_manual');
    }
}
