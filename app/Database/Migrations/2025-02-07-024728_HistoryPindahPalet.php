<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HistoryPindahPalet extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_history_pindah_palet' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_stock_old' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_stock_new' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'cluster_old' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'cluster_new' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'kgs' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'cns' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'lot' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_history_pindah_palet', true);
        $this->forge->createTable('history_pindah_palet');
    }

    public function down()
    {
        $this->forge->dropTable('history_pindah_palet');
    }
}
