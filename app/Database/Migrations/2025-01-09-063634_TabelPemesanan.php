<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelPemesanan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pemesanan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_material' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tgl_list' => [
                'type' => 'DATE',
            ],
            'tgl_pesan' => [
                'type' => 'DATE',
            ],
            'tgl_pakai' => [
                'type' => 'DATE',
            ],
            'jl_mc' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'ttl_qty_cones' => [
                'type' => 'FLOAT',
            ],
            'ttl_berat_cones' => [
                'type' => 'FLOAT',
            ],
            'sisa_kgs_mc' => [
                'type' => 'FLOAT',
            ],
            'sisa_cones_mc' => [
                'type' => 'FLOAT',
            ],
            'lot' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
            'po_tambahan' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1'],
            ],
            'id_pengeluaran' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_retur' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status_kirim' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'admin' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id_pemesanan', true);
        $this->forge->addForeignKey('id_material', 'material', 'id_material', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_pengeluaran', 'pengeluaran', 'id_pengeluaran', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_retur', 'retur', 'id_retur', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pemesanan');
    }

    public function down()
    {
        //
    }
}
