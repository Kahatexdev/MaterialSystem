<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PemesananSpandex extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_psk' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'id_total_pemesanan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'admin' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addForeignKey('id_total_pemesanan', 'total_pemesanan', 'id_total_pemesanan', 'CASCADE', 'CASCADE');
        $this->forge->addPrimaryKey('id_psk');
        $this->forge->createTable('pemesanan_spandex_karet');
    }

    public function down()
    {
        $this->forge->dropTable('pemesanan_spandex_karet');
    }
}
