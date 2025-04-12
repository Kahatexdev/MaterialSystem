<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdReturAtPemasukan extends Migration
{
    public function up()
    {
        // Cek dulu apakah kolom sudah ada agar tidak error
        if (!$this->db->fieldExists('id_retur', 'pemasukan')) {
            $this->forge->addColumn('pemasukan', [
                'id_retur' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'id_out_celup',
                ],
            ]);
        }

        // Tambahkan FK dengan nama unik
        $this->forge->addForeignKey('id_retur', 'retur', 'id_retur', 'CASCADE', 'CASCADE', 'fk_pemasukan_id_retur');
    }

    public function down()
    {
        $this->forge->dropForeignKey('pemasukan', 'fk_pemasukan_id_retur');
        $this->forge->dropColumn('pemasukan', 'id_retur');
    }
}
