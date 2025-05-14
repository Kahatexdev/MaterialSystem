<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableTrackingPoCovering extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_tpc' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'id_po_gbn' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
            'admin' => [
                'type' => 'varchar',
                'constraint' => 50,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => date('Y-m-d H:i:s'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'default' => date('Y-m-d H:i:s'),
            ],
        ]);

        $this->forge->addKey('id_tpc', true);
        $this->forge->addForeignKey('id_po_gbn', 'open_po', 'id_po', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tracking_po_covering');
    }

    public function down()
    {
        $this->forge->dropTable('tracking_po_covering');
    }
}
