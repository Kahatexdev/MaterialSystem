<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoryStockCoveringModel extends Model
{
    protected $table            = 'history_stock_covering';
    protected $primaryKey       = 'id_history_covering_stock';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_total_pemesanan',
        'no_model',
        'jenis',
        'jenis_benang',
        'jenis_cover',
        'color',
        'code',
        'lmd',
        'ttl_cns',
        'ttl_kg',
        'admin',
        'keterangan',
        'created_at',
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

    public function getPemasukan()
    {
        return $this->db->table('history_stock_covering')
            ->select('*')
            ->where('ttl_kg >=', 0)
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days'))) // Filter 7 hari terakhir
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getPengeluaran()
    {
        return $this->db->table('history_stock_covering')
            ->select('*')
            ->where('ttl_kg <=', 0)
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days'))) // Filter 7 hari terakhir
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getPemasukanByDate($date, $date2)
    {
        return $this->db->table('history_stock_covering')
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
        return $this->db->table('history_stock_covering')
            ->select('*')
            ->where('DATE(created_at) >=', $date)
            ->where('DATE(created_at) <=', $date2) // Filter berdasarkan tanggal
            ->where('ttl_kg <=', 0)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Mengambil pemasukan hari ini
    public function getIncomeToday()
    {
        return $this->selectSum('ttl_kg')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->where('ttl_kg >=', 0)
            ->get()
            ->getRow()
            ->ttl_kg ?? 0;
    }

    public function getExpenseToday()
    {
        return $this->selectSum('ttl_kg')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->where('ttl_kg <=', 0)
            ->get()
            ->getRow()
            ->ttl_kg ?? 0;
    }
}
