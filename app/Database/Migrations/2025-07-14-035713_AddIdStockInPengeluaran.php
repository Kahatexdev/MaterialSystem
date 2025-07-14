<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdStockInPengeluaran extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pengeluaran', [
            'id_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'id_total_pemesanan',
            ]
        ]);
        $this->db->query('ALTER TABLE pengeluaran ADD CONSTRAINT pengeluaran_id_stock_foreign FOREIGN KEY (id_stock) REFERENCES stock(id_stock) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // Hapus foreign key 'id_total_pemesanan'
        $this->forge->dropForeignKey('pemesanan', 'pengeluaran_id_stock_foreign');

        // Hapus kolom 'id_total_pemesanan'
        $this->forge->dropColumn('pengeluaran', 'id_stock');
    }
}
