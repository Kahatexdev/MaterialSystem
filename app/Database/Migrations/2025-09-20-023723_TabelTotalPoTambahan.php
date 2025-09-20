<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelTotalPoTambahan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_total_potambahan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ttl_tambahan_kg' => [
                'type' => 'FLOAT',
            ],
            'ttl_tambahan_cns' => [
                'type' => 'FLOAT',
            ],
            'ttl_terima_kg' => [
                'type' => 'FLOAT',
            ],
            'ttl_sisa_jatah' => [
                'type' => 'FLOAT',
            ],
            'ttl_sisa_bb_dimc' => [
                'type' => 'FLOAT',
            ],
            'keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);
        $this->forge->addKey('id_total_potambahan', true);
        $this->forge->createTable('total_potambahan');
    }

    public function down()
    {
        $this->forge->dropTable('total_potambahan');
    }
}
