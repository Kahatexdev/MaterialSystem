<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesForSisaPakai extends Migration
{
    public function up()
    {
        // master_order
        $this->db->query("CREATE INDEX idx_master_order_delivery_awal ON master_order (delivery_awal)");
        $this->db->query("CREATE INDEX idx_master_order_no_model ON master_order (no_model)");

        // stock
        $this->db->query("CREATE INDEX idx_stock_no_model ON stock (no_model)");
        $this->db->query("CREATE INDEX idx_stock_item_type ON stock (item_type)");
        $this->db->query("CREATE INDEX idx_stock_kombinasi ON stock (no_model, item_type, kode_warna)");
        $this->db->query("CREATE INDEX idx_stock_cluster_lot ON stock (nama_cluster, lot_stock, lot_awal)");

        // master_material
        $this->db->query("CREATE INDEX idx_mm_item_type ON master_material (item_type)");
        $this->db->query("CREATE INDEX idx_mm_jenis ON master_material (jenis)");

        // pengeluaran
        $this->db->query("CREATE INDEX idx_pengeluaran_lot_cluster ON pengeluaran (lot_out, nama_cluster)");

        // retur
        $this->db->query("CREATE INDEX idx_retur_kombinasi ON retur (no_model, item_type, kode_warna)");
    }

    public function down()
    {
        // master_order
        $this->db->query("DROP INDEX idx_master_order_delivery_awal ON master_order");
        $this->db->query("DROP INDEX idx_master_order_no_model ON master_order");

        // stock
        $this->db->query("DROP INDEX idx_stock_no_model ON stock");
        $this->db->query("DROP INDEX idx_stock_item_type ON stock");
        $this->db->query("DROP INDEX idx_stock_kombinasi ON stock");
        $this->db->query("DROP INDEX idx_stock_cluster_lot ON stock");

        // master_material
        $this->db->query("DROP INDEX idx_mm_item_type ON master_material");
        $this->db->query("DROP INDEX idx_mm_jenis ON master_material");

        // pengeluaran
        $this->db->query("DROP INDEX idx_pengeluaran_lot_cluster ON pengeluaran");

        // retur
        $this->db->query("DROP INDEX idx_retur_kombinasi ON retur");
    }
}
