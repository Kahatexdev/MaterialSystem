<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateFieldKeteranganIn extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('pemasukan', 'keterangan');

        // Tambah kolom 'keterangan' ke tabel 'bon_celup'
        $this->forge->addColumn('bon_celup', [
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'detail_sj'
            ]
        ]);
        // Tambah kolom 'keterangan' ke tabel 'other_bon'
        $this->forge->addColumn('other_bon', [
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'detail_sj'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->addColumn('pemasukan', [
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'out_jalur'
            ]
        ]);

        $this->forge->dropColumn('bon_celup', 'keterangan');
        $this->forge->dropColumn('other_bon', 'keterangan');
    }
}
