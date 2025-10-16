<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMaterialTypeInMaterialTable extends Migration
{
    public function up()
    {
        // Tambahkan kolom baru
        $this->forge->addColumn('material', [
            'material_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'keterangan',
            ],
        ]);

        // Tambahkan foreign key
        $this->db->query('
            ALTER TABLE material 
            ADD CONSTRAINT fk_material_type
            FOREIGN KEY (material_type) 
            REFERENCES master_material_type(material_type)
            ON UPDATE CASCADE 
            ON DELETE SET NULL
        ');
    }

    public function down()
    {
        // Hapus foreign key dulu
        $this->db->query('ALTER TABLE material DROP FOREIGN KEY fk_material_type');

        // Baru hapus kolom
        $this->forge->dropColumn('material', 'material_type');
    }
}
