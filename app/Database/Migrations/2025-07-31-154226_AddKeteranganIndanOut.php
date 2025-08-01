<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganIndanOut extends Migration
{
    public function up()
    {
        // Tambah kolom 'keterangan_gbn' ke tabel 'pengeluaran'
        $this->forge->addColumn('pengeluaran', [
            'keterangan_gbn' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status'
            ]
        ]);

        // Tambah kolom 'keterangan' ke tabel 'pemasukan'
        $this->forge->addColumn('pemasukan', [
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'out_jalur'
            ]
        ]);
    }

    public function down()
    {
        // Hapus kolom jika rollback
        $this->forge->dropColumn('pengeluaran', 'keterangan_gbn');
        $this->forge->dropColumn('pemasukan', 'keterangan');
    }
}
