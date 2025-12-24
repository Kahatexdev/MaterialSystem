<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesPengeluaranManual extends Migration
{
    public function up()
    {
        // pengeluaran
        $this->forge->addKey(
            ['status', 'area_out', 'id_out_celup', 'id_total_pemesanan'],
            false,
            false,
            'idx_pengeluaran_filter'
        );
        $this->forge->processIndexes('pengeluaran');

        // pemesanan
        $this->forge->addKey(
            ['id_total_pemesanan', 'tgl_pakai', 'id_material'],
            false,
            false,
            'idx_pemesanan_total'
        );
        $this->forge->processIndexes('pemesanan');

        // material
        $this->forge->addKey(
            ['id_material', 'item_type', 'kode_warna'],
            false,
            false,
            'idx_material_item'
        );
        $this->forge->processIndexes('material');
    }

    public function down()
    {
        // pengeluaran
        $this->forge->dropKey('pengeluaran', 'idx_pengeluaran_filter');

        // pemesanan
        $this->forge->dropKey('pemesanan', 'idx_pemesanan_total');

        // material
        $this->forge->dropKey('material', 'idx_material_item');
    }
}
