<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FieldTambahanWaktuArea extends Migration
{
    public function up()
    {
        $fields = [
            'additional_time' => [
                'type'       => 'TIME',
                'null'       => true,  // Kolom boleh kosong
                'default'    => null,  // Default value jika tidak diisi
                'after'      => 'admin',  // Tambahkan setelah kolom `admin`
            ],
            'hak_akses' => [
                'type'       => 'VARCHAR(32)',
                'null'       => true,  // Kolom boleh kosong
                'default'    => null,  // Default value jika tidak diisi
                'after'      => 'additional_time',  // Tambahkan setelah kolom `additional_time`
            ],
        ];

        // Menambahkan kolom `additional_time` dan `hak_akses` ke tabel `pemesanan`
        $this->forge->addColumn('pemesanan', $fields);
    }

    public function down()
    {
        // Menghapus kolom `additional_time` dan `hak_akses` jika rollback
        $this->forge->dropColumn('pemesanan', 'additional_time');
        $this->forge->dropColumn('pemesanan', 'hak_akses');
    }
}
