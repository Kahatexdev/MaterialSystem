<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusInBonCelup extends Migration
{
    public function up()
    {
        // Tambah field id_out_celup
        $this->forge->addColumn('bon_celup', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true, // bisa diubah jadi false kalau wajib diisi
                'after'      => 'keterangan', // letakkan setelah kolom tertentu
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('bon_celup', 'status');
    }
}
