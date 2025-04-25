<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTabelHistoryPindah extends Migration
{
    public function up()
    {
        // Ubah nama tabel 'history_pindah_palet' menjadi 'history_stock'
        $this->forge->renameTable('history_pindah_palet', 'history_stock');

        // Tambahkan kolom baru ke tabel 'history_stock'
        $fields = [
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true, // Kolom bisa null
                'after'      => 'krg', // Sesuaikan dengan kolom terakhir
            ],
            'admin' => [
                'type'       => 'VARCHAR',
                'constraint' => 35,
                'null'       => true, // Kolom bisa null
                'after'      => 'keterangan', // Ditambahkan setelah 'keterangan'
            ],
        ];
        $this->forge->addColumn('history_stock', $fields);

        // Ubah nama kolom id_history_pindah_palet menjadi id_history_pindah
        $this->forge->modifyColumn('history_stock', [
            'id_history_pindah_palet' => [
                'name'       => 'id_history_pindah',
                'type'       => 'INT',
                'unsigned'   => true,
                'auto_increment' => true,
            ],
        ]);

        // Drop tabel 'history_pindah_order' jika ada
        $this->forge->dropTable('history_pindah_order', true); // true untuk IF EXISTS
    }

    public function down()
    {
        // Ubah nama tabel kembali menjadi 'history_pindah_palet'
        $this->forge->renameTable('history_stock', 'history_pindah_palet');

        // Hapus kolom baru yang ditambahkan
        $this->forge->dropColumn('history_pindah_palet', 'keterangan');
        $this->forge->dropColumn('history_pindah_palet', 'admin');

        // Ubah nama kolom id_history_pindah kembali ke id_history_pindah_palet
        $this->forge->modifyColumn('history_pindah_palet', [
            'id_history_pindah' => [
                'name'       => 'id_history_pindah_palet',
                'type'       => 'INT',
                'unsigned'   => true,
                'auto_increment' => true,
            ],
        ]);

        // Recreate tabel 'history_pindah_order'
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            // Tambahkan field lain sesuai dengan struktur tabel awal
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('history_pindah_order', true); // true untuk IF NOT EXISTS
    }
}
