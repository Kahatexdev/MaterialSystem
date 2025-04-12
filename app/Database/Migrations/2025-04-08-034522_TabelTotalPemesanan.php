<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelTotalPemesanan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_total_pemesanan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ttl_jl_mc' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'ttl_kg' => [
                'type' => 'FLOAT',
            ],
            'ttl_cns' => [
                'type' => 'FLOAT',
            ],
        ]);
        $this->forge->addKey('id_total_pemesanan', true);
        $this->forge->createTable('total_pemesanan');
    }

    public function down()
    {
        $this->forge->dropTable('total_pemesanan');
    }
}
