<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelBonJs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_other_bon' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'no_model' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'item_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'kode_warna' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'warna' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'tgl_datang' => [
                'type' => 'DATE',
            ],
            'l_m_d' => [
                'type' => 'ENUM',
                'constraint' => ['L', 'M', 'D'],
            ],
            'harga' => [
                'type' => 'FLOAT',
            ],
            'no_surat_jalan' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'detail_sj' => [
                'type' => 'TEXT',
            ],
            'ganti_retur' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1'],
            ],
            'admin' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id_other_bon', true);
        $this->forge->createTable('other_bon');

        // Menambahkan kolom id_other_bon di tabel out_celup
        $this->forge->addColumn('out_celup', [
            'id_other_bon' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id_bon'
            ]
        ]);

        // Menambahkan foreign key
        $this->db->query("
            ALTER TABLE `out_celup` 
            ADD CONSTRAINT `fk_out_celup_id_other_bon` 
            FOREIGN KEY (`id_other_bon`) 
            REFERENCES `other_bon`(`id_other_bon`) 
            ON DELETE CASCADE 
            ON UPDATE CASCADE;
        ");
    }

    public function down()
    {
        // Menghapus foreign key dari tabel out_celup
        $this->db->query("ALTER TABLE `out_celup` DROP FOREIGN KEY `fk_out_celup_id_other_bon`;");

        // Menghapus kolom id_other_bon dari tabel out_celup
        $this->forge->dropColumn('out_celup', 'id_other_bon');

        // Menghapus tabel other_bon
        $this->forge->dropTable('other_bon');
    }
}
