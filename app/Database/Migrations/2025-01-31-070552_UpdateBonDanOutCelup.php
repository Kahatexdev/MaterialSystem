<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateBonDanOutCelup extends Migration
{
    public function up()
    {
        // Hapus field dari tabel bon_celup
        $this->forge->dropColumn('bon_celup', ['gw', 'nw', 'cones', 'karung', 'ganti_retur']);
    }

    public function down()
    {
        // Tambahkan kembali field yang dihapus dari bon_celup
        $this->forge->addColumn('bon_celup', [
            'gw' => [
                'type' => 'FLOAT',
                'after' => 'harga',
            ],
            'nw' => [
                'type' => 'FLOAT',
                'after' => 'gw',
            ],
            'cones' => [
                'type' => 'INT',
                'after' => 'nw',
            ],
            'karung' => [
                'type' => 'INT',
                'after' => 'cones',
            ],
            'ganti_retur' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1'],
                'after' => 'detail_sj',
            ],
        ]);
    }
}
