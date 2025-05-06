<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKolomInOpenPO extends Migration
{
    public function up()
    {
        $this->forge->addColumn('open_po', [
            'ket_celup' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'keterangan', // Posisi setelah 'keterangan'
            ],
            'bentuk_celup' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'ket_celup', // Posisi setelah 'ket_celup'
            ],
            'kg_percones' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'after'      => 'bentuk_celup', // Posisi setelah 'bentuk_celup'
            ],
            'jumlah_cones' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'after'      => 'kg_percones', // Posisi setelah 'kg_percones'
            ],
            'jenis_produksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'jumlah_cones', // Posisi setelah 'jumlah_cones'
            ],
        ]);
    }

    public function down()
    {
        // Hapus kolom yang ditambahkan di metode up()
        $columns = ['ket_celup', 'bentuk_celup', 'kg_percones', 'jumlah_cones', 'jenis_produksi'];
        foreach ($columns as $column) {
            $this->forge->dropColumn('open_po', $column);
        }
    }
}
