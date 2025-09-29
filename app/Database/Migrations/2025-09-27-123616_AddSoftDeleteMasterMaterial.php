<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class AddSoftDeleteMasterMaterial extends Migration
{
    protected string $table = 'master_material';

    public function up()
    {
        $existing = array_map('strtolower', $this->db->getFieldNames($this->table));
        $fields   = [];

        if (!in_array('created_at', $existing, true)) {
            $fields['created_at'] = [
                'type'    => 'DATETIME',
                'null'    => true, // biar fleksibel
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ];
        }

        if (!in_array('updated_at', $existing, true)) {
            $fields['updated_at'] = [
                'type'      => 'DATETIME',
                'null'      => true,
                // CI4 >=4.4: aktifkan baris di bawah (jika tidak tersedia, gunakan fallback raw SQL di bawah)
                // 'default'   => null,
                // 'on_update' => new RawSql('CURRENT_TIMESTAMP'),
            ];
        }

        if (!in_array('deleted_at', $existing, true)) {
            $fields['deleted_at'] = [
                'type' => 'DATETIME',
                'null' => true,
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn($this->table, $fields);
        }

        // ---- Fallback untuk ON UPDATE (jika butuh & Forge belum support) ----
        // Uncomment kalau ingin updated_at auto-update di level DB.
        // $this->db->query("ALTER TABLE {$this->table}
        //     MODIFY `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP");
    }

    public function down()
    {
        $current = array_map('strtolower', $this->db->getFieldNames($this->table));
        foreach (['created_at', 'updated_at', 'deleted_at'] as $col) {
            if (in_array($col, $current, true)) {
                $this->forge->dropColumn($this->table, $col);
            }
        }
    }
}
