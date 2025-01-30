<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTLInScheduleCelup extends Migration
{
    public function up()
    {
        $this->forge->addColumn('schedule_celup', [
            'tanggal_tl' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'tanggal_oven',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('schedule_celup', 'tanggal_tl');
    }
}
