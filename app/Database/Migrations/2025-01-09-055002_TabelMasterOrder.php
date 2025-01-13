<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelMasterOrder extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'no_order' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'no_model' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'buyer' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'foll_up' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'lco_date' => [
                'type' => 'DATE',
            ],
            'memo' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'delivery_awal' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'delivery_akhir' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'admin' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id_order', true);
        $this->forge->createTable('master_order');
    }

    public function down()
    {
        $this->forge->dropTable('master_order');
    }
}
