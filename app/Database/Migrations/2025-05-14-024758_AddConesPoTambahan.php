<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddConesPoTambahan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('po_tambahan', [
            'cns_po_tambahan' => [
                'type'  => 'FLOAT',
                'null'  => true,
                'after' => 'kg_po_tambahan', // Ditempatkan setelah 'kg_po_tambahan'
            ],
        ]);
    }

    public function down()
    {
        // Hapus kolom yang ditambahkan di metode up()
        $this->forge->dropColumn('po_tambahan', 'cns_po_tambahan');
    }
}
