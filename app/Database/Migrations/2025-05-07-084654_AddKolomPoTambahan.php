<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKolomPoTambahan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('po_tambahan', [
            'style_size' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => true,
                'after'      => 'no_model', // Posisi setelah 'ket_celup'
            ],
            'pcs_po_tambahan' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'after'      => 'color', // Posisi setelah 'bentuk_celup'
            ],
        ]);
    }

    public function down()
    {
        // Hapus kolom yang ditambahkan di metode up()
        $columns = ['style_size', 'pcs_po_tambahan'];
        foreach ($columns as $column) {
            $this->forge->dropColumn('po_tambahan', $column);
        }
    }
}
