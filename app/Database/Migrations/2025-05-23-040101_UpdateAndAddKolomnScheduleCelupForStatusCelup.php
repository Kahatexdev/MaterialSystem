<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAndAddKolomnScheduleCelupForStatusCelup extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('schedule_celup', [
            'tanggal_press' => [
                'name' => 'tanggal_press_oven',
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);

        $this->forge->addColumn('schedule_celup', [
            'serah_terima_acc' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'tanggal_kelos',
            ],
            'tanggal_matching' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'tanggal_reject',
            ],
        ]);

        $this->forge->dropColumn('schedule_celup', 'tanggal_oven');
    }

    public function down()
    {
        $this->forge->modifyColumn('schedule_celup', [
            'tanggal_press_oven' => [
                'name' => 'tanggal_press',
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->dropColumn('schedule_celup', 'serah_terima_acc');
        $this->forge->dropColumn('schedule_celup', 'tanggal_matching');

        $this->forge->addColumn('schedule_celup', [
            'tanggal_oven' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }
}
