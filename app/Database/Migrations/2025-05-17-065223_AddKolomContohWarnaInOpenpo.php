<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKolomContohWarnaInOpenpo extends Migration
{
    public function up()
    {
        $this->forge->addColumn('open_po', [
            'contoh_warna' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null'  => true,
                'after' => 'jenis_produksi', // Ditempatkan setelah 'kg_po_tambahan'
            ],
        ]);
    }

    public function down()
    {
        // Hapus kolom yang ditambahkan di metode up()
        $this->forge->dropColumn('open_po', 'contoh_warna');
    }
}
