<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class KeteranganTambahanWaktu extends Migration
{
    public function up()
    {
        $fields = [
            'alasan_tambahan_waktu' => [
                'type' => 'varchar',
                'null' => true,
                'after' => 'additional_time',
                'constraint' => 255,
            ],
        ];

        $this->forge->addColumn('pemesanan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pemesanan', 'alasan_tambahan_waktu');
    }
}
