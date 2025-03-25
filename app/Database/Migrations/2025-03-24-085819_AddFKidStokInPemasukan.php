<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFKidStokInPemasukan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pemasukan', [
            'id_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ]
        ]);

        $this->db->query('ALTER TABLE pemasukan ADD CONSTRAINT pemasukan_id_stock_foreign FOREIGN KEY (id_stock) REFERENCES stock(id_stock) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('pemasukan', 'pemasukan_id_stock_foreign');
        $this->forge->dropColumn('pemasukan', 'id_stock');
    }
}
