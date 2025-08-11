<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganIndanOut extends Migration
{
    public function up()
    {
        // Tambah kolom 'keterangan_gbn' ke tabel 'pengeluaran'
        $this->forge->modifyColumn('schedule_celup', [
            'po_plus' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1'],
                'default' => '0',
                'null' => false
            ]
        ]);
    }

    public function down() {}
}
