<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropSomeFieldAtPemasukan extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('pemasukan', 'no_model');
        $this->forge->dropColumn('pemasukan', 'item_type');
        $this->forge->dropColumn('pemasukan', 'kode_warna');
        $this->forge->dropColumn('pemasukan', 'warna');
        $this->forge->dropColumn('pemasukan', 'kgs_masuk');
        $this->forge->dropColumn('pemasukan', 'cns_masuk');
    }

    public function down()
    {
        $this->forge->addColumn('pemasukan', [
            'no_model' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'item_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'kode_warna' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'warna' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'kgs_masuk' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'cns_masuk' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
        ]);
    }
}
