<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DeleteColumnInOtherBon extends Migration
{
    public function up()
    {
        // Hapus kolom `lmd` dan `harga` dari tabel `other_bon`
        $this->forge->dropColumn('other_bon', ['l_m_d', 'harga']);
    }

    public function down()
    {
        // Tambahkan kembali kolom `lmd` dan `harga` jika migration di-rollback
        $fields = [
            'l_m_d' => [
                'type' => 'ENUM',
                'constraint' => ['L', 'M', 'D'],
            ],
            'harga' => [
                'type' => 'FLOAT',
            ],
        ];

        $this->forge->addColumn('other_bon', $fields);
    }
}
