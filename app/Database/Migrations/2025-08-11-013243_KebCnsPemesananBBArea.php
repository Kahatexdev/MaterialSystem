<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Config\Database;

class KebCnsPemesananBBArea extends Migration
{
    public function up()
    {
        $db = Database::connect();

        $this->forge->addField([
            'id_keb_cns' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_material' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'qty_cns' => [
                'type' => 'FLOAT',
            ],
            'qty_berat_cns' => [
                'type' => 'FLOAT',
            ],
            'area' => [
                'type'       => 'VARCHAR',
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
        $this->forge->addKey('id_keb_cns', true);

        $this->forge->addForeignKey('id_material', 'material', 'id_material', 'CASCADE', 'CASCADE'); // Tambahkan foreign key ke tabel material

        $this->forge->createTable('kebutuhan_cones');

        // hapus kebuthan kones di tabel material
        if ($db->fieldExists('qty_cns', 'material')) {
            $this->forge->dropColumn('material', ['qty_cns', 'qty_berat_cns']);
        }
    }

    public function down()
    {
        $db = Database::connect();

        // hapus tabel (otomatis juga drop foreign key) 
        $this->forge->dropTable('kebutuhan_cones', true);

        // Kembalikan kolom ke tabel material
        if (!$db->fieldExists('qty_cns', 'material')) {
            $this->forge->addColumn('material', [
                'qty_cns' => [
                    'type' => 'FLOAT',
                    'null' => true,
                    'after' => 'admin',
                ],
                'qty_berat_cns' => [
                    'type' => 'FLOAT',
                    'null' => true,
                    'after' => 'qty_cns',
                ]
            ]);
        }
    }
}
