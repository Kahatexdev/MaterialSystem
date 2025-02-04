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
        $this->forge->dropColumn('bon_celup', ['id_celup', 'gw', 'nw', 'cones', 'karung', 'ganti_retur', 'l_m_d', 'harga']);


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
            'no_karung' => [
                'type' => 'INT',
                'after' => 'harga',
            ],
        ]);
    }

    public function down()
    {
        // Hapus kolom 'id_bon' dan foreign key 'fk_bon' dari tabel 'schedule_celup'
        $this->db->query("ALTER TABLE schedule_celup DROP FOREIGN KEY fk_bon");
        $this->forge->dropColumn('schedule_celup', 'id_bon');

        // Tambahkan kembali kolom yang sebelumnya dihapus dari tabel 'bon_celup'
        $this->forge->addColumn('bon_celup', [
            'id_celup' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'id_bon',
            ],
            'gw' => [
                'type'       => 'FLOAT',
                'null'       => false,
                'after'      => 'id_celup',
            ],
            'nw' => [
                'type'       => 'FLOAT',
                'null'       => false,
                'after'      => 'gw',
            ],
            'cones' => [
                'type'       => 'INT',
                'null'       => false,
                'after'      => 'nw',
            ],
            'karung' => [
                'type'       => 'INT',
                'null'       => false,
                'after'      => 'cones',
            ],
            'ganti_retur' => [
                'type'       => 'TINYINT',
                'null'       => false,
                'after'      => 'karung',
            ],
            'l_m_d' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'M', 'D'],
                'null'       => false,
                'after'      => 'ganti_retur',
            ],
            'harga' => [
                'type'       => 'FLOAT',
                'null'       => false,
                'after'      => 'l_m_d',
            ],
        ]);
        // Tambahkan kembali foreign key yang dihapus dari tabel 'bon_celup'
        $this->db->query("ALTER TABLE bon_celup ADD CONSTRAINT bon_celup_id_celup_foreign FOREIGN KEY (id_celup) REFERENCES celup(id_celup) ON DELETE CASCADE ON UPDATE CASCADE");

        // Hapus kolom-kolom baru yang ditambahkan pada tabel 'out_celup'
        $this->forge->dropColumn('out_celup', ['l_m_d', 'harga', 'no_karung']);
    }
}
