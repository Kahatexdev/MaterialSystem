<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBuyerFieldInOpenPo extends Migration
{
    public function up()
    {
        $fields = [
            'buyer' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'after'      => 'id_po', // letakkan setelah id_po
                'null'       => true,    // bisa disesuaikan, true jika boleh null
            ],
        ];

        $this->forge->addColumn('open_po', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('open_po', 'buyer');
    }
}
