<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\Forge;
use CodeIgniter\Database\ConnectionInterface;

class AddIdReturAtOutCelup extends Migration
{
    public function up()
    {
        // Cek apakah kolom id_retur sudah ada di out_celup
        $fields = $this->db->getFieldNames('out_celup');
        if (!in_array('id_retur', $fields)) {
            $this->forge->addColumn('out_celup', [
                'id_retur' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'after' => 'id_out_celup',
                    'null' => true,
                ],
            ]);
            // Tambahkan FK dengan nama khusus agar mudah di-drop nanti
            $this->forge->addForeignKey('id_retur', 'retur', 'id_retur', 'SET NULL', 'CASCADE', 'out_celup_id_retur_foreign');
        }

        // Cek dan hapus kolom id_retur di tabel pemasukan jika ada
        if ($this->db->fieldExists('id_retur', 'pemasukan')) {
            // $this->db->query('ALTER TABLE pemasukan DROP FOREIGN KEY fk_pemasukan_idretur');
            $this->forge->dropColumn('pemasukan', 'id_retur');
        }

        // Cek dan hapus kolom id_retur di tabel pemesanan jika ada
        if ($this->db->fieldExists('id_retur', 'pemesanan')) {
            $this->db->query('ALTER TABLE pemesanan DROP FOREIGN KEY pemesanan_id_retur_foreign');
            $this->forge->dropColumn('pemesanan', 'id_retur');
        }
    }

    public function down()
    {
        // Kembalikan kolom dan FK di tabel pemasukan dan pemesanan
        $this->forge->dropForeignKey('out_celup', 'out_celup_id_retur_foreign');
        $this->forge->dropColumn('out_celup', 'id_retur');

        $this->forge->addColumn('pemasukan', [
            'id_retur' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);
        // $this->forge->addForeignKey('id_retur', 'retur', 'id_retur', 'SET NULL', 'CASCADE', 'fk_pemasukan_idretur');

        $this->forge->addColumn('pemesanan', [
            'id_retur' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);
        $this->forge->addForeignKey('id_retur', 'retur', 'id_retur', 'SET NULL', 'CASCADE', 'pemesanan_id_retur_foreign');
    }
}
