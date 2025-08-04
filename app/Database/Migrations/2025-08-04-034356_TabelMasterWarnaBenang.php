<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelMasterWarnaBenang extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'kode_warna' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'warna' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'warna_dasar' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('kode_warna', true); // true untuk primary key
        $this->forge->createTable('master_warna_benang');
    }

    public function down()
    {
        $this->forge->dropTable('master_warna_benang');
    }
}
