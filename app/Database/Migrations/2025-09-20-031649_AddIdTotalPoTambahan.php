<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdTotalPoTambahan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('po_tambahan', [
            'id_total_potambahan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'tanggal_approve',
            ]
        ]);
        $this->db->query('ALTER TABLE po_tambahan ADD CONSTRAINT po_tambahan_id_total_potambahan_foreign FOREIGN KEY (id_total_potambahan) REFERENCES total_potambahan(id_total_potambahan) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // Hapus foreign key 'id_total_pemesanan'
        $this->forge->dropForeignKey('po_tambahan', 'po_tambahan_id_total_potambahan_foreign');

        // Hapus kolom 'id_total_pemesanan'
        $this->forge->dropColumn('po_tambahan', 'id_total_potambahan');
    }
}
