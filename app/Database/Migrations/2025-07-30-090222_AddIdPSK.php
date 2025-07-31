<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdPSK extends Migration
{
    public function up()
    {
        // 1) Modify id_out_celup to be NULLable
        $this->forge->modifyColumn('pengeluaran', [
            'id_out_celup' => [
                'name'       => 'id_out_celup',
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nama_cluster' => [
                'name'       => 'nama_cluster',
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => true, // Make sure this is nullable as well
            ],
        ]);

        // 2) Add new nullable id_psk column after id_out_celup
        $this->forge->addColumn('pengeluaran', [
            'id_psk' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_out_celup',
            ],
        ]);

        // 3) Add self-referencing foreign key on id_psk
        //
        //    This assumes that `pemesanan_spandex_karet.id_psk` references
        //    `pemesanan_spandex_karet.id_psk` (self‑FK). If you actually
        //    meant to link to a different table, replace the table/name below.
        $this->forge->addForeignKey(
            'id_psk',                     // this table’s column
            'pengeluaran',    // foreign table
            'id_psk',                     // foreign column
            'CASCADE',                    // ON DELETE
            'CASCADE'                     // ON UPDATE
        );
    }

    public function down()
    {
        // 1) Drop the FK constraint
        //    Note: CodeIgniter auto‑names the FK like:
        //     {table}_{column}_foreign
        $this->forge->dropForeignKey(
            'pengeluaran',
            'pengeluaran_id_psk_foreign'
        );

        // 2) Drop the id_psk column
        $this->forge->dropColumn('pengeluaran', 'id_psk');

        // 3) Revert id_out_celup back to NOT NULL
        $this->forge->modifyColumn('pengeluaran', [
            'id_out_celup' => [
                'name'       => 'id_out_celup',
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
    }
}
