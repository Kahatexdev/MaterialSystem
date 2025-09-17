<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdOutCelupToHistoryStock extends Migration
{
    public function up()
    {
        // Tambah field id_out_celup
        $this->forge->addColumn('history_stock', [
            'id_out_celup' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true, // bisa diubah jadi false kalau wajib diisi
                'after'      => 'id_stock_new', // letakkan setelah kolom tertentu
                'unsigned'    => true,
            ],
        ]);

        // Tambah foreign key
        $this->db->query("
            ALTER TABLE history_stock
            ADD CONSTRAINT fk_history_outcelup
            FOREIGN KEY (id_out_celup) REFERENCES out_celup(id_out_celup)
            ON DELETE RESTRICT
            ON UPDATE RESTRICT
        ");
    }

    public function down()
    {
        // Hapus foreign key dulu
        $this->db->query("ALTER TABLE history_stock DROP FOREIGN KEY fk_history_outcelup");

        // Hapus kolom
        $this->forge->dropColumn('history_stock', 'id_out_celup');
    }
}
