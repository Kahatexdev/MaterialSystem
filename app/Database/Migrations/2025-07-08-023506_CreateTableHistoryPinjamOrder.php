<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableHistoryPinjamOrder extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_history' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_total_pemesanan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'no_model_pinjam' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'kgs_pinjam' => [
                'type' => 'FLOAT',
            ],
            'cns_pinjam' => [
                'type' => 'FLOAT',
            ],
            'admin' => [
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
        $this->forge->addKey('id_history', true);
        $this->forge->addForeignKey('id_stock', 'stock', 'id_stock', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_total_pemesanan', 'total_pemesanan', 'id_total_pemesanan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('history_pinjam_order');
    }

    public function down()
    {
        $this->forge->dropTable('history_pinjam_order');
    }
}
