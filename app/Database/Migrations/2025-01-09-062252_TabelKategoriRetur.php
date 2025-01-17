<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelKategoriRetur extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'nama_kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'tipe_kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'role' => [
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
        $this->forge->addKey('nama_kategori', true);
        $this->forge->createTable('kategori_retur');
    }

    public function down()
    {
        $this->forge->dropTable('kategori_retur');
    }
}
