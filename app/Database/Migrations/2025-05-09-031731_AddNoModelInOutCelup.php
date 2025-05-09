<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNoModelInOutCelup extends Migration
{
    public function up()
    {
        $this->forge->addColumn('out_celup', [
            'no_model' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'after' => 'id_celup'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('out_celup', 'no_model'); // Menghapus kolom no_model dari tabel out_celup
    }
}
