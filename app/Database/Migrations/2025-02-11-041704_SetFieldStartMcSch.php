<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SetFieldStartMcSch extends Migration
{
    public function up()
    {
        // Menambahkan kolom baru atau mengubah kolom yang ada menjadi nullable
        $forge = \Config\Database::forge();

        $forge->modifyColumn('schedule_celup', [
            'start_mc' => [
                'type' => 'DATE',  // Ganti tipe data sesuai dengan kolom yang ingin diubah
                'null' => true,  // Membuat kolom bisa NULL
            ]
        ]);
    }

    public function down()
    {
        // Membalikkan perubahan jika perlu, kolom menjadi non-nullable
        $forge = \Config\Database::forge();

        $forge->modifyColumn('schedule_celup', [
            'start_mc' => [
                'type' => 'DATE',
                'null' => false,  // Mengubah kembali menjadi non-nullable
            ]
        ]);
    }
}
