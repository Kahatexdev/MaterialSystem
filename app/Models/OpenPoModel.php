<?php

namespace App\Models;

use CodeIgniter\Model;

class OpenPoModel extends Model
{
    protected $table            = 'open_po';
    protected $primaryKey       = 'id_po';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_model',
        'item_type',
        'kode_warna',
        'color',
        'kg_po',
        'keterangan',
        'penerima',
        'penanggung_jawab',
        'admin',
        'created_at',
        'updated_at',
        'id_induk',
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

    public function getData($no_model, $jenis, $jenis2)
    {
        return $this->select('open_po.no_model, open_po.item_type, open_po.kode_warna, open_po.color, open_po.kg_po, open_po.keterangan, open_po.penanggung_jawab, DATE(open_po.created_at) AS tgl_po, master_material.jenis, master_order.buyer, master_order.no_order, master_order.delivery_awal')
            ->where(['open_po.no_model' => $no_model])
            ->groupStart() // Mulai grup untuk kondisi OR
            ->where('master_material.jenis', $jenis)
            ->orWhere('master_material.jenis', $jenis2)
            ->groupEnd() // Akhiri grup
            ->join('master_material', 'master_material.item_type=open_po.item_type', 'left')
            ->join('master_order', 'master_order.no_model=open_po.no_model', 'left')
            ->findAll();
    }

    public function getWarnabyItemTypeandKodeWarna($item_type, $kode_warna)
    {
        return $this->select('color')
            ->where('item_type', $item_type)
            ->where('kode_warna', $kode_warna)
            ->first();
    }

    public function getNomorModel()
    {
        return $this->select('open_po.no_model, master_order.id_order')
            ->join('master_order', 'master_order.no_model=open_po.no_model', 'left')
            ->distinct()
            ->findAll();
    }

    public function getFilteredPO($kodeWarna, $warna, $item_type)
    {
        return $this->select('open_po.no_model, open_po.item_type, open_po.kode_warna, open_po.color, open_po.kg_po, master_order.delivery_awal, master_order.delivery_akhir, master_order.id_order')
            ->join('master_order', 'master_order.no_model = open_po.no_model')
            ->where('open_po.kode_warna', $kodeWarna)  // Ganti like dengan where
            ->where('open_po.color', $warna)
            ->where('open_po.item_type', $item_type) // Ganti like dengan where
            ->distinct()
            ->get()
            ->getResultArray();
    }
    public function getFilteredCovering($kodeWarna, $warna, $item_type)
    {
        return $this->select('open_po.no_model, open_po.item_type, open_po.kode_warna, open_po.color, open_po.kg_po')
            ->where('open_po.kode_warna', $kodeWarna)  // Ganti like dengan where
            ->where('open_po.color', $warna)
            ->where('open_po.item_type', $item_type) // Ganti like dengan where
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getKgKebutuhan($noModel, $itemType, $kodeWarna)
    {
        return $this->select('kg_po')
            ->where('no_model', $noModel)
            ->where('item_type', $itemType)
            ->where('kode_warna', $kodeWarna)
            ->first();
    }

    public function getQtyPO($kodeWarna, $warna, $itemTypeEncoded, $idInduk)
    {
        $this->select('kg_po')
            ->where('kode_warna', $kodeWarna)
            ->where('color', $warna)
            ->where('item_type', $itemTypeEncoded);

        // Jika $idInduk null, kita ingin menganggapnya sebagai 0
        $nilai = is_null($idInduk) ? 0 : $idInduk;
        // Menggunakan COALESCE untuk membandingkan id_induk, sehingga NULL dianggap 0
        $this->where("COALESCE(id_induk, 0) = {$nilai}", null, false);

        return $this->first();
    }




    public function getKodeWarna($query)
    {
        return $this->select('kode_warna')
            ->like('kode_warna', $query)
            ->distinct()
            ->findAll();
    }

    public function getWarna($kodeWarna)
    {
        return $this->select('color')
            ->where('kode_warna', $kodeWarna)
            ->distinct()
            ->findAll();
    }

    public function getItemType($kodeWarna, $warna)
    {
        return $this->select('item_type, id_induk')
            ->where('kode_warna', $kodeWarna)
            ->where('color', $warna)
            ->where('penerima !=', 'Paryanti')
            ->distinct()
            ->findAll();
    }

    public function getPOCovering()
    {
        return $this->select('id_po,DATE(open_po.created_at) tgl_po, id_induk')
            ->where('penerima', 'Paryanti')
            ->orderBy('tgl_po', 'DESC')
            ->groupBy('tgl_po')
            ->findAll();
    }

    public function getPODetailCovering($tgl_po)
    {
        return $this->select('open_po.id_po,open_po.no_model, open_po.item_type, open_po.kode_warna, open_po.color, open_po.kg_po, open_po.keterangan,open_po.penerima, open_po.penanggung_jawab,open_po.admin, open_po.created_at,open_po.updated_at')
            ->where('penerima', 'Paryanti')
            ->where('DATE(open_po.created_at)', $tgl_po)
            ->groupBy('open_po.no_model')
            ->findAll();
    }

    public function getDetailByNoModel($tgl_po, $noModel)
    {
        return $this->select('open_po.id_po, open_po.no_model, open_po.item_type, open_po.kode_warna, open_po.color, open_po.kg_po, open_po.keterangan, open_po.penerima, open_po.penanggung_jawab, open_po.admin, open_po.created_at, open_po.updated_at')
            ->where('DATE(open_po.created_at)', $tgl_po)
            ->where('open_po.no_model', $noModel)
            ->where('penerima', 'Paryanti')
            ->groupBy('open_po.no_model')
            ->groupBy('open_po.item_type')
            ->groupBy('open_po.kode_warna')
            ->findAll();
    }

    public function getPoForCelup($tgl_po)
    {
        return $this->select('open_po.*, master_material.jenis')
            ->join('master_material', 'master_material.item_type=open_po.item_type', 'left')
            ->where('open_po.id_induk IS NOT NULL')
            ->where('DATE(open_po.created_at)', $tgl_po)
            ->findAll();
    }
    public function
    getQtyPOForCvr($noModel, $itemType, $kodeWarna)
    {
        return $this->select('sum(kg_po) as qty_po')
            ->where('open_po.no_model', $noModel)
            ->where('open_po.item_type', $itemType)
            ->where('open_po.kode_warna', $kodeWarna)
            ->groupBy('open_po.no_model')
            ->groupBy('open_po.item_type')
            ->groupBy('open_po.kode_warna')
            ->first();
    }
    public function getIdInduk($noModel, $itemType, $kodeWarna)
    {
        return $this->select('id_induk')
            ->where('open_po.no_model', $noModel)
            ->where('open_po.item_type', $itemType)
            ->where('open_po.kode_warna', $kodeWarna)
            ->first();
    }

    public function getDeliveryAwalNoOrderBuyer($tgl_po)
    {
        return $this->select('DISTINCT open_po.id_po, open_po.no_model, master_order.buyer, master_order.delivery_awal, master_order.no_order', false)
            ->join('master_order', 'master_order.no_model = open_po.no_model', 'left') // Join berdasarkan no_model
            ->where('open_po.id_induk', null) // Hanya ambil id_po asli
            ->whereNotIn('open_po.id_po', function ($builder) use ($tgl_po) {
                return $builder->select('id_induk')
                    ->from('open_po')
                    ->where('DATE(created_at) !=', $tgl_po) // Hapus jika id_induk memiliki tanggal berbeda
                    ->where('id_induk IS NOT NULL'); // Pastikan id_induk bukan NULL
            })
            ->where('DATE(open_po.created_at)', $tgl_po) // Hanya ambil sesuai tanggal di routes
            ->findAll();
    }



    // public function getDeliveryAwalNoOrderBuyer()
    // {
    //     return $this->select('open_po.id_po, open_po.no_model, open_po.id_induk, master_order.buyer, master_order.no_order, master_order.delivery_awal')
    //         ->join('master_order', 'TRIM(master_order.no_model) = TRIM(open_po.no_model)', 'left') // Hindari perbedaan format no_model
    //         ->where('open_po.id_induk IS NULL') // Ambil hanya data tanpa id_induk
    //         ->findAll();
    // }

    public function poCoveringCount()
    {
        return $this->where('penerima', 'Paryanti')->countAllResults();
    }

    public function poCoveringQty()
    {
        return $this->selectSum('kg_po')
            ->where('penerima', 'Paryanti')
            ->get()
            ->getRow()
            ->kg_po ?? 0;
    }

    public function getFilterPoBenang($key)
    {
        $this->select('open_po.created_at, DATE(open_po.created_at) as tgl_po, master_order.foll_up, open_po.no_model, master_order.no_order, open_po.keterangan, master_order.buyer, master_order.delivery_awal, master_order.delivery_akhir, master_order.unit, open_po.item_type, master_material.jenis, open_po.kode_warna, open_po.color, open_po.kg_po')
            ->join('master_order', 'master_order.no_model = open_po.no_model')
            ->join('master_material', 'master_material.item_type = open_po.item_type')
            ->where('id_induk', null);

        // Cek apakah ada input key untuk pencarian
        if (!empty($key)) {
            $this->groupStart()
                ->like('open_po.no_model', $key)
                ->orLike('open_po.item_type', $key)
                ->orLike('open_po.kode_warna', $key)
                ->orLike('open_po.color', $key)
                ->groupEnd();
        }

        return $this->findAll();
    }
}
