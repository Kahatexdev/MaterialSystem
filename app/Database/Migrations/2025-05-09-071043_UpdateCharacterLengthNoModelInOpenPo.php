<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCharacterLengthNoModelInOpenPo extends Migration
{
    public function up()
    {
        // Mengubah panjang kolom no_model menjadi VARCHAR(100)
        $this->forge->modifyColumn('open_po', [
            'no_model' => [
                'name' => 'no_model', // Nama kolom yang ingin diubah
                'type' => 'VARCHAR',
                'constraint' => 100, // Panjang baru
            ],
        ]);
    }

    public function down()
    {
        // Mengembalikan panjang kolom no_model ke VARCHAR(32)
        $this->forge->modifyColumn('open_po', [
            'no_model' => [
                'name' => 'no_model', // Nama kolom yang ingin diubah
                'type' => 'VARCHAR',
                'constraint' => 32, // Panjang sebelumnya
            ],
        ]);
    }
}
