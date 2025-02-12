<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldInTableHistoryPaletAndOrder extends Migration
{
    public function up()
    {
        $this->forge->addColumn('history_pindah_palet', [
            'krg' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => true,
                'after' => 'lot',
            ],
        ]);

        $this->forge->addColumn('history_pindah_order', [
            'krg' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => true,
                'after' => 'lot',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('history_pindah_palet', 'krg');
        $this->forge->dropColumn('history_pindah_order', 'krg');
    }
}
