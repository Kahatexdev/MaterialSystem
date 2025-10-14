<?php

namespace App\Models;

use CodeIgniter\Model;

class BonCelupModel extends Model
{
    protected $table            = 'bon_celup';
    protected $primaryKey       = 'id_bon';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bon',
        'tgl_datang',
        'no_surat_jalan',
        'detail_sj',
        'admin',
        'keterangan',
        'status',
        'created_at',
        'updated_at',
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

    public function getData()
    {
        return $this->select('bon_celup.id_bon, bon_celup.tgl_datang, bon_celup.no_surat_jalan, bon_celup.detail_sj')
            ->findAll();
    }

    public function saveBon($saveDataBon, $saveDataOutCelup)
    {
        $this->db->table('bon_celup')->insert($saveDataBon);

        $this->db->table('out_celup')->insert($saveDataOutCelup);
    }

    public function getDataById($id)
    {
        return $this->select('bon_celup.*')
            ->where('bon_celup.id_bon', $id)
            ->first();
        //     return $this->select('bon_celup.*, out_celup.*, scheule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna')
        // ->join('out_celup', 'out_celup.id_bon=bon_celup.id_bon', 'left')
        // ->join('schedule_celup', 'out_celup.id_bon=bon_celup.id_bon')
        //     ->();
    }

    public function getDataComplainRetur($tglSch = null, $tglKirim = null, $noModel = null)
    {
        $builder = $this->select('out_celup.id_celup, schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.lot_celup, schedule_celup.tanggal_schedule, out_celup.id_bon, bon_celup.tgl_datang, bon_celup.no_surat_jalan, history_stock.keterangan, bon_celup.keterangan AS ket_bon')
            ->join('out_celup', 'out_celup.id_bon = bon_celup.id_bon', 'left')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup', 'left')
            ->join('history_stock', 'history_stock.id_out_celup = out_celup.id_out_celup', 'left')
            ->where('bon_celup.status', 'complain');
        if (!empty($tglSch)) {
            $builder = $this->where('schedule_celup.tanggal_schedule', $tglSch);
        }
        if (!empty($tglKirim)) {
            $builder = $this->where('bon_celup.tgl_datang', $tglKirim);
        }
        if (!empty($noModel)) {
            $builder = $builder->groupStart()
                ->where('schedule_celup.no_model', $noModel)
                ->orLike('schedule_celup.kode_warna', $noModel)
                ->groupEnd();
        }
        return $builder->groupBy('bon_celup.id_bon')
            ->orderBy('bon_celup.updated_at', 'DESC')
            ->findAll();
    }

    public function getScheduleBon($idBon)
    {
        return $this->select('bon_celup.id_bon, out_celup.id_celup, schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.lot_celup, schedule_celup.tanggal_schedule, schedule_celup.kg_celup')
            ->join('out_celup', 'out_celup.id_bon = bon_celup.id_bon', 'left')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup', 'left')
            ->where('bon_celup.id_bon', $idBon)
            ->first();
    }
}
