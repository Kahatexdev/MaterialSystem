<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumIdTotalPemesananInHistoryStockCovering extends Migration
{
    public function up()
    {
        $this->forge->addColumn('history_stock_covering', [
            'id_total_pemesanan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'id_history_covering_stock'
            ]
        ]);
        $this->forge->addForeignKey('id_total_pemesanan', 'total_pemesanan', 'id_total_pemesanan', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
        $this->forge->dropForeignKey('history_stock_covering', 'history_stock_covering_id_total_pemesanan_foreign');
        $this->forge->dropColumn('history_stock_covering', 'id_total_pemesanan');
    }
}
