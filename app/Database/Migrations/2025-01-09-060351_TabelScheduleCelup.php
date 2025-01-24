<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelScheduleCelup extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_celup' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_mesin' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'no_po' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'no_model' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'item_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'kode_warna' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'warna' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'start_mc' => [
                'type' => 'DATE',
            ],
            'kg_celup' => [
                'type' => 'FLOAT',
            ],
            'lot_urut' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'lot_celup' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => true,
            ],
            'tanggal_schedule' => [
                'type' => 'DATE',
            ],
            'tanggal_bon' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_celup' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_bongkar' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_press' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_oven' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_rajut_pagi' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_kelos' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_acc' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_reject' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_perbaikan' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'last_status' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'ket_daily_cek' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'po_plus' => [
                'type' => 'ENUM',
                'constraint' => ['1', '0'],
            ],
            'user_cek_status' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id_celup', true);
        $this->forge->addForeignKey('id_mesin', 'mesin_celup', 'id_mesin', 'CASCADE', 'CASCADE');
        $this->forge->createTable('schedule_celup');
    }

    public function down()
    {
        $this->forge->dropTable('schedule_celup');
    }
}
