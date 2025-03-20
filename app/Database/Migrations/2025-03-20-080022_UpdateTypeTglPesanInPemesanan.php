<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTypeTglPesanInPemesanan extends Migration
{
    public function up()
    {
        // Mendefinisikan tabel
        $this->forge->modifyColumn('pemesanan', [
            'tgl_pesan' => [
                'type' => 'DATETIME',
                'null' => true, // Jika kolom sebelumnya mengizinkan NULL
            ],
        ]);
    }

    public function down()
    {
        // Mengembalikan perubahan ke tipe DATE jika di-rollback
        $this->forge->modifyColumn('pemesanan', [
            'tgl_pesan' => [
                'type' => 'DATE',
                'null' => true, // Pastikan sama dengan pengaturan awal
            ],
        ]);
    }
}
