<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Config\Database;

class RemoveFieldInPemasukan extends Migration
{
    public function up()
    {
        $db = Database::connect();

        // Periksa apakah tabel 'pemasukan' ada sebelum menghapus kolom
        if ($db->tableExists('pemasukan')) {
            if ($db->fieldExists('id_retur', 'pemasukan')) {
                $this->forge->dropForeignKey('pemasukan', 'pemasukan_id_retur_foreign');
                $this->forge->dropColumn('pemasukan', 'id_retur');
            }

            if ($db->fieldExists('history_order', 'pemasukan')) {
                $this->forge->dropColumn('pemasukan', 'history_order');
            }

            if ($db->fieldExists('history_jalur', 'pemasukan')) {
                $this->forge->dropColumn('pemasukan', 'history_jalur');
            }
        }
    }

    public function down()
    {
        $db = Database::connect();

        if ($db->tableExists('pemasukan')) {
            if (!$db->fieldExists('id_retur', 'pemasukan')) {
                $this->forge->addColumn('pemasukan', [
                    'id_retur' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'null'       => true,
                        'after'      => 'id_stock',
                    ],
                ]);
            }

            $this->forge->addForeignKey('id_retur', 'retur', 'id_retur', 'CASCADE', 'CASCADE');

            if (!$db->fieldExists('history_order', 'pemasukan')) {
                $this->forge->addColumn('pemasukan', [
                    'history_order' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 35,
                        'null'       => true,
                        'after'      => 'nama_cluster',
                    ],
                ]);
            }

            if (!$db->fieldExists('history_jalur', 'pemasukan')) {
                $this->forge->addColumn('pemasukan', [
                    'history_jalur' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 35,
                        'null'       => true,
                        'after'      => 'history_order',
                    ],
                ]);
            }
        }
    }
}
