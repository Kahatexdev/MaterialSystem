<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTabelPemesanan extends Migration
{
    public function up()
    {
        $this->forge->dropForeignKey('pemesanan', 'pemesanan_id_pengeluaran_foreign');
        $this->forge->dropColumn('pemesanan', 'id_pengeluaran');

        $this->forge->addColumn('pemesanan', [
            'id_total_pemesanan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'po_tambahan',
            ]
        ]);
        $this->db->query('ALTER TABLE pemesanan ADD CONSTRAINT pemesanan_id_total_pemesanan_foreign FOREIGN KEY (id_total_pemesanan) REFERENCES total_pemesanan(id_total_pemesanan) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // Hapus foreign key 'id_total_pemesanan'
        $this->forge->dropForeignKey('pemesanan', 'pemesanan_id_total_pemesanan_foreign');

        // Hapus kolom 'id_total_pemesanan'
        $this->forge->dropColumn('pemesanan', 'id_total_pemesanan');

        // Tambahkan kembali kolom 'id_pengeluaran'
        $this->forge->addColumn('pemesanan', [
            'id_pengeluaran' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'po_tambahan',
            ],
        ]);

        // Tambahkan kembali foreign key untuk 'id_pengeluaran'
        $this->db->query('ALTER TABLE pemesanan ADD CONSTRAINT pemesanan_id_pengeluaran_foreign FOREIGN KEY (id_pengeluaran) REFERENCES pengeluaran(id_pengeluaran) ON DELETE CASCADE ON UPDATE CASCADE');
    }
}
