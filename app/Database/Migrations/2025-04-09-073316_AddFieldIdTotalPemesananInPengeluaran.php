<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldIdTotalPemesananInPengeluaran extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pengeluaran', [
            'id_total_pemesanan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id_out_celup',
            ]
        ]);
        $this->db->query('ALTER TABLE pengeluaran ADD CONSTRAINT pengeluaran_id_total_pemesanan_foreign FOREIGN KEY (id_total_pemesanan) REFERENCES total_pemesanan(id_total_pemesanan) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // Hapus foreign key 'id_total_pemesanan'
        $this->forge->dropForeignKey('pengeluaran', 'pengeluaran_id_total_pemesanan_foreign');

        // Hapus kolom 'id_total_pemesanan'
        $this->forge->dropColumn('pengeluaran', 'id_total_pemesanan');
    }
}
