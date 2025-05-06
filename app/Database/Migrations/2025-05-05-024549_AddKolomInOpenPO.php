<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKolomInOpenPO extends Migration
{
    public function up()
    {
        $this->forge->addColumn('open_po', [
            'bentuk_celup' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'keterangan', // Specifies the position of the column
            ],
            'kg_percones' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'after'      => 'bentuk_celup', // Specifies the position of the column
            ],
            'jumlah_cones' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'after'      => 'kg_percones', // Specifies the position of the column
            ],
            'jenis_produksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'jumlah_cones', // Specifies the position of the column
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('open_po', 'bentuk_celup');
        $this->forge->dropColumn('open_po', 'kg_percones');
        $this->forge->dropColumn('open_po', 'jumlah_cones');
        $this->forge->dropColumn('open_po', 'jenis_produksi');
    }
}
