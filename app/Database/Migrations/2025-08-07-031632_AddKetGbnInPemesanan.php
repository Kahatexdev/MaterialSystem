<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKetGbnInPemesanan extends Migration
{
    public function up()
    {
        // Tambah kolom 'keterangan_gbn' ke tabel 'pengeluaran'
        $this->forge->addColumn('pemesanan', [
            'keterangan_gbn' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'additional_time'
            ]
        ]);
    }

    public function down()
    {
        // Hapus kolom 'keterangan_gbn' dari tabel 'pemesanan'
        $this->forge->dropColumn('pemesanan', 'keterangan_gbn');
    }
}
