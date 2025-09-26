<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnTabelTotalPoTambahan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('total_potambahan', [
            'loss_aktual' => [
                'type' => 'FLOAT',
                'after' => 'ttl_sisa_bb_dimc',
            ],
            'loss_tambahan' => [
                'type' => 'FLOAT',
                'after' => 'loss_aktual',
            ]
        ]);
    }

    public function down()
    {
        // Hapus kolom 'id_total_pemesanan'
        $this->forge->dropColumn('total_potambahan', ['loss_aktual', 'loss_tambahan']);
    }
}
