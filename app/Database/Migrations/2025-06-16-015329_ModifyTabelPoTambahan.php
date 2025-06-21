<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTabelPoTambahan extends Migration
{
    public function up()
    {
        // Hapus kolom-kolom lama
        $this->forge->dropColumn('po_tambahan', [
            'area',
            'no_model',
            'style_size',
            'item_type',
            'kode_warna',
            'color',
            'pcs_po_tambahan',
            'kg_po_tambahan',
            'cns_po_tambahan'
        ]);

        // Tambahkan kolom-kolom baru
        $this->forge->addColumn('po_tambahan', [
            'id_material' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'id_po_tambahan',
            ],
            'terima_kg' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'id_material',
            ],
            'sisa_bb_mc' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'terima_kg',
            ],
            'sisa_order_pcs' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'sisa_bb_mc',
            ],
            'bs_mesin_kg' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'sisa_order_pcs',
            ],
            'bs_st_pcs' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'bs_mesin_kg',
            ],
            'poplus_mc_kg' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'bs_st_pcs',
            ],
            'poplus_mc_cns' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'poplus_mc_kg',
            ],
            'plus_pck_pcs' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'poplus_mc_cns',
            ],
            'plus_pck_kg' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'plus_pck_pcs',
            ],
            'plus_pck_cns' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'plus_pck_kg',
            ],
            'lebih_pakai_kg' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'plus_pck_cns',
            ],
        ]);

        // Tambahkan foreign key constraint
        $this->db->query('ALTER TABLE po_tambahan ADD CONSTRAINT fk_po_tambahan_material FOREIGN KEY (id_material) REFERENCES material(id_material) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Kembalikan kolom-kolom yang dihapus
        $this->forge->addColumn('po_tambahan', [
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'after' => 'id_po_tambahan',
            ],
            'no_model' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'after' => 'area',
            ],
            'style_size' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'after' => 'no_model',
            ],
            'item_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'after' => 'style_size',
            ],
            'kode_warna' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'after' => 'item_type',
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'after' => 'kode_warna',
            ],
            'pcs_po_tambahan' => [
                'type' => 'FLOAT',
                'after' => 'color',
            ],
            'kg_po_tambahan' => [
                'type' => 'FLOAT',
                'after' => 'pcs_po_tambahan',
            ],
            'cns_po_tambahan' => [
                'type' => 'FLOAT',
                'after' => 'kg_po_tambahan',
            ],
        ]);

        // Hapus foreign key constraint
        $this->db->query('ALTER TABLE po_tambahan DROP FOREIGN KEY fk_po_tambahan_material');

        // Hapus kolom-kolom yang baru ditambahkan
        $this->forge->dropColumn('po_tambahan', [
            'id_material',
            'terima_kg',
            'sisa_bb_mc',
            'sisa_order_pcs',
            'sisa_order_kg',
            'sisa_order_cns',
            'plus_pck_pcs',
            'plus_pck_kg',
            'plus_pck_cns',
            'lebih_pakai_kg',
        ]);
    }
}
