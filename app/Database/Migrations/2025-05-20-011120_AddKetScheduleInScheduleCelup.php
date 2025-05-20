<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKetScheduleInScheduleCelup extends Migration
{
    public function up()
    {
        $this->forge->addColumn('schedule_celup', [
            'ket_schedule' => [
                'type' => 'TEXT',
                'after' => 'po_plus',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('schedule_celup', 'ket_schedule');
    }
}
