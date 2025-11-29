<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesForPphAndPengiriman extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE INDEX idx_material_style_item_color_comp
            ON material (style_size, item_type, kode_warna, composition)
        ");

         // PENGELUARAN
        $this->db->query("
            CREATE INDEX idx_pengeluaran_status
            ON pengeluaran (status)
        ");

        $this->db->query("
            CREATE INDEX idx_pengeluaran_status_total
            ON pengeluaran (status, id_total_pemesanan)
        ");

        $this->db->query("
            CREATE INDEX idx_pengeluaran_status_lot_out
            ON pengeluaran (status, lot_out)
        ");
    }

    public function down()
    {
        $this->db->query("DROP INDEX idx_material_style_item_color_comp ON material");

        // PENGELUARAN
        $this->db->query("DROP INDEX idx_pengeluaran_status ON pengeluaran");
        $this->db->query("DROP INDEX idx_pengeluaran_status_total ON pengeluaran");
        $this->db->query("DROP INDEX idx_pengeluaran_status_lot_out ON pengeluaran");
    }
}
