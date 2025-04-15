<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateFKOutCelup extends Migration
{
    public function up()
    {
        // Hapus foreign key yang salah
        $this->forge->dropForeignKey('out_celup', 'out_celup_id_celup_foreign');
        // Tambahkan foreign key yang benar
        $this->forge->addForeignKey('id_celup', 'schedule_celup', 'id_celup', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        // Hapus foreign key yang baru dibuat
        $this->forge->dropForeignKey('out_celup', 'out_celup_id_celup_foreign'); // Pastikan menggunakan nama foreign key yang tepat
        // Tambahkan kembali foreign key yang lama mengacu pada tabel 'mesin_celup'
        $this->forge->addForeignKey('id_celup', 'mesin_celup', 'id_mesin', 'CASCADE', 'CASCADE');
    }
}
