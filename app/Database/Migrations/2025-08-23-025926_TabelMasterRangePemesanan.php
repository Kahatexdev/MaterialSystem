<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelMasterRangePemesanan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'days' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'range_spandex' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'range_karet' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'range_benang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'range_nylon' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
        ]);

        // Primary key
        $this->forge->addKey('days', true);

        // Create table
        $this->forge->createTable('master_range_pemesanan');
    }

    public function down()
    {
        $this->forge->dropTable('master_range_pemesanan');
    }
}
