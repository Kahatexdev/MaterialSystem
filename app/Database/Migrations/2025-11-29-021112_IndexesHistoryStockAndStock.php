<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class IndexesHistoryStockAndStock extends Migration
{
    public function up()
    {
        // History_stock indexes
        $this->db->query(
            "CREATE INDEX idx_hist_ket_created
             ON history_stock (keterangan, created_at)"
        );

        $this->db->query(
            "CREATE INDEX idx_hist_id_stock_old
             ON history_stock (id_stock_old)"
        );

        $this->db->query(
            "CREATE INDEX idx_hist_id_stock_new
             ON history_stock (id_stock_new)"
        );

        $this->db->query(
            "CREATE INDEX idx_stock_kode_warna
             ON stock (kode_warna)"
        );
    }

    public function down()
    {
         // Drop index kalau rollback
        $this->db->query("DROP INDEX idx_hist_ket_created ON history_stock");
        $this->db->query("DROP INDEX idx_hist_id_stock_old ON history_stock");
        $this->db->query("DROP INDEX idx_hist_id_stock_new ON history_stock");
        $this->db->query("DROP INDEX idx_stock_kode_warna ON stock");
    }
}
