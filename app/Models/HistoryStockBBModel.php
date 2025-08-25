<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoryStockBBModel extends Model
{
    protected $table            = 'history_stockbb';
    protected $primaryKey       = 'id_history_stockbb';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'denier',
        'jenis',
        'jenis_benang',
        'color',
        'code',
        'ttl_cns',
        'ttl_kg',
        'admin',
        'keterangan'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getPemasukanByDate($date, $date2)
    {
        return $this->db->table('history_stockbb')
            ->select('*')
            ->where('DATE(created_at) >=', $date) // Filter berdasarkan tanggal
            ->where('DATE(created_at) <=', $date2) // Filter berdasarkan tanggal
            ->where('ttl_kg >=', 0)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }


    public function getPengeluaranByDate($date, $date2)
    {
        return $this->db->table('history_stockbb')
            ->select('*')
            ->where('DATE(created_at) >=', $date)
            ->where('DATE(created_at) <=', $date2) // Filter berdasarkan tanggal
            ->where('ttl_kg <=', 0)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
