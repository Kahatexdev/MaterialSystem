<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGwCnsKarung extends Migration
{
    public function up()
    {
        // Menambahkan kolom ke tabel bon_celup
        $this->forge->addColumn('bon_celup', [
            'cones' => [
                'type' => 'INT',
                'after' => 'nw',
            ],
            'karung' => [
                'type' => 'INT',
                'after' => 'cones',
            ],
        ]);

        // Menambahkan kolom ke tabel out_celup
        $this->forge->addColumn('out_celup', [
            'gw_kirim' => [
                'type' => 'FLOAT',
                'after' => 'id_celup',
            ],
        ]);
    }

    public function down()
    {
        // Menghapus kolom dari tabel bon_celup
        $this->forge->dropColumn('bon_celup', 'cones');
        $this->forge->dropColumn('bon_celup', 'karung');

        // Menghapus kolom dari tabel out_celup
        $this->forge->dropColumn('out_celup', 'gw_kirim');
    }
}
