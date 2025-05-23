<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSpesifikasiBenangInOpenPo extends Migration
{
    public function up()
    {
        $this->forge->addColumn('open_po', [
            'spesifikasi_benang' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null'  => true,
                'after' => 'color', // Ditempatkan setelah 'kg_po_tambahan'
            ],
        ]);
    }

    public function down()
    {
        // Hapus kolom yang ditambahkan di metode up()
        $this->forge->dropColumn('open_po', 'spesifikasi_benang');
    }
}
