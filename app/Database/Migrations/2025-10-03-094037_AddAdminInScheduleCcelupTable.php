<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdminInScheduleCcelupTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('schedule_celup', [
            'admin' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'after'      => 'user_cek_status', // letakkan setelah kolom 'user_cek_status'
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('schedule_celup', 'admin');
    }
}
