<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddtesLabStatus extends Migration
{
    public function up()
    {
        $this->forge->addColumn('schedule_celup', [
            'tanggal_teslab' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('schedule_celup', 'tanggal_teslab'); // Menghapus kolom no_model dari tabel out_celup
    }
}
