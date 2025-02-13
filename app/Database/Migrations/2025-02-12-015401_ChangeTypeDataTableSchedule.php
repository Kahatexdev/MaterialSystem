<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeTypeDataTableSchedule extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('schedule_celup', [
            'tanggal_bon' => [
                'name' => 'tanggal_bon',
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_celup' => [
                'name' => 'tanggal_celup',
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_bongkar' => [
                'name' => 'tanggal_bongkar',
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_press' => [
                'name' => 'tanggal_press',
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_oven' => [
                'name' => 'tanggal_oven',
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_tl' => [
                'name' => 'tanggal_tl',
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_rajut_pagi' => [
                'name' => 'tanggal_rajut_pagi',
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_kelos' => [
                'name' => 'tanggal_kelos',
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_acc' => [
                'name' => 'tanggal_acc',
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_reject' => [
                'name' => 'tanggal_reject',
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_perbaikan' => [
                'name' => 'tanggal_perbaikan',
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('schedule_celup', [
            'tanggal_bon' => [
                'name' => 'tanggal_bon',
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_celup' => [
                'name' => 'tanggal_celup',
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_bongkar' => [
                'name' => 'tanggal_bongkar',
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_press' => [
                'name' => 'tanggal_press',
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_oven' => [
                'name' => 'tanggal_oven',
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_tl' => [
                'name' => 'tanggal_tl',
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_rajut_pagi' => [
                'name' => 'tanggal_rajut_pagi',
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_kelos' => [
                'name' => 'tanggal_kelos',
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_acc' => [
                'name' => 'tanggal_acc',
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_reject' => [
                'name' => 'tanggal_reject',
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_perbaikan' => [
                'name' => 'tanggal_perbaikan',
                'type' => 'DATE',
                'null' => true,
            ]
        ]);
    }
}
