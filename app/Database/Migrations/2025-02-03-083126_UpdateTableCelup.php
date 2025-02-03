<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTableCelup extends Migration
{
    public function up()
    {
        // Tambah kolom baru di tabel 'schedule_celup'
        $this->forge->addColumn('schedule_celup', [
            'id_bon' => [
                'type'       => 'INT',
                'unsigned'   => true,  // Pastikan unsigned jika PK pada tabel referensi juga unsigned
                'null'       => true,
                'after'      => 'id_mesin',
            ],
        ]);
        // Tambah Foreign Key dengan nama yang eksplisit
        $this->db->query("ALTER TABLE schedule_celup ADD CONSTRAINT fk_bon FOREIGN KEY (id_bon) REFERENCES bon_celup(id_bon) ON DELETE CASCADE ON UPDATE CASCADE");


        // Hapus foreign key constraint terlebih dahulu
        $this->db->query("ALTER TABLE `bon_celup` DROP FOREIGN KEY `bon_celup_id_celup_foreign`");
        // Hapus field dari tabel bon_celup
        $this->forge->dropColumn('bon_celup', ['id_celup', 'gw', 'nw', 'cones', 'karung', 'ganti_retur']);


        // Tambah field baru di out celup
        $this->forge->addColumn('out_celup', [
            'l_m_d' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'M', 'D'],
                'null'       => false,
                'after'      => 'id_celup',
            ],
            'harga' => [
                'type' => 'FLOAT',
                'after' => 'l_m_d',
            ],
        ]);
    }

    public function down()
    {
        // Ambil foreign key spesifik 'fk_bon' dari tabel 'schedule_celup'
        $query = $this->db->query("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_NAME = 'schedule_celup' 
            AND TABLE_SCHEMA = DATABASE() 
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND CONSTRAINT_NAME = 'fk_bon'
        ")->getRow();
        // Jika foreign key ditemukan, hapus
        if ($query) {
            $fkName = $query->CONSTRAINT_NAME;
            $this->db->query("ALTER TABLE schedule_celup DROP FOREIGN KEY $fkName");
        }
        // Hapus kolom id_bon
        $this->forge->dropColumn('schedule_celup', 'id_bon');


        // Tambahkan kembali field di bon_celup
        $this->forge->addColumn('bon_celup', [
            'id_celup' => [
                'type' => 'INT',
                'after' => 'detail_sj',
            ],
            'l_m_d' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'M', 'D'],
                'null'       => false,
                'after'      => 'id_celup',
            ],
            'harga' => [
                'type' => 'FLOAT',
                'after' => 'l_m_d',
            ],
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
                'after' => 'karung',
            ],
        ]);
        // Tambahkan kembali foreign key di bon_celup
        $this->db->query("ALTER TABLE bon_celup ADD CONSTRAINT bon_celup_id_celup_foreign FOREIGN KEY (id_celup) REFERENCES schedule_celup(id_celup) ON DELETE CASCADE ON UPDATE CASCADE");


        // Hapus kolom l_m_d dan harga dari out_celup
        $this->forge->dropColumn('out_celup', ['l_m_d', 'harga']);
    }
}
