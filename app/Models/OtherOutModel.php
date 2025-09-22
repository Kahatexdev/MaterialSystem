<?php

namespace App\Models;

use CodeIgniter\Model;

class OtherOutModel extends Model
{
    protected $table            = 'other_out';
    protected $primaryKey       = 'id_other_out';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_other_out',
        'id_out_celup',
        'kategori',
        'tgl_other_out',
        'kgs_other_out',
        'cns_other_out',
        'krg_other_out',
        'lot_other_out',
        'nama_cluster',
        'admin',
        'created_at',
        'updated_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
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

    public function getQty($id, $namaCluster = null)
    {
        return $this->select('SUM(kgs_other_out) AS kgs_other_out, SUM(cns_other_out) AS cns_other_out, SUM(krg_other_out) AS krg_other_out')
            ->where('id_out_celup', $id)
            // ->where('nama_cluster', $namaCluster)
            ->first();
    }
    public function getPakaiLain($key, $jenis = null)
    {
        $builder = $this->db->table('other_out oo')
            ->select('oo.*, oc.no_model, sc.item_type, sc.kode_warna, sc.warna, mm.jenis, sc.po_plus, p.area_out, p.tgl_out, p.kgs_out, p.cns_out, p.krg_out, p.lot_out')
            ->join('out_celup oc', 'oc.id_out_celup = oo.id_out_celup', 'left')
            ->join('schedule_celup sc', 'oc.id_celup = sc.id_celup', 'left')
            ->join('master_material mm', 'sc.item_type = mm.item_type', 'left')
            ->join('pengeluaran p', 'p.id_out_celup = oo.id_out_celup', 'left')
            ->where('oc.no_model', $key);

        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder
            ->groupBy('oo.id_other_out')
            ->orderBy('oo.tgl_other_out, sc.item_type, sc.kode_warna', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getFilterOtherOut($key, $tanggalAwal, $tanggalAkhir): array
    {
        // Normalisasi parameter
        $key          = trim((string)($key ?? ''));
        $tanggalAwal  = $tanggalAwal ? date('Y-m-d', strtotime($tanggalAwal)) : null;
        $tanggalAkhir = $tanggalAkhir ? date('Y-m-d', strtotime($tanggalAkhir)) : null;

        $builder = $this->db->table('other_out');

        // Pilih kolom-kolom yang diminta.
        // item_type/kode_warna/warna diambil dari schedule_celup jika ada,
        // fallback ke retur/other_bon, dan terakhir ke out_celup.
        $builder->select("
        COALESCE(sc.no_model, r.no_model, ob.no_model, oc.no_model)                    AS no_model,
        COALESCE(sc.item_type, r.item_type, ob.item_type)                               AS item_type,
        COALESCE(sc.kode_warna, r.kode_warna, ob.kode_warna)                            AS kode_warna,
        COALESCE(sc.warna, r.warna, ob.warna)                                           AS warna,

        other_out.kategori                                                              AS kategori,
        other_out.tgl_other_out                                                         AS tgl_otherout,
        other_out.cns_other_out                                                         AS cns_otherout,
        other_out.kgs_other_out                                                         AS kgs_otherout,
        other_out.krg_other_out                                                         AS krg_otherout,
        other_out.lot_other_out                                                         AS lot,
        other_out.nama_cluster                                                          AS cluster,
        other_out.admin                                                                 AS admin,
        other_out.created_at                                                            AS created_at_otherout
    ");

        // Join
        $builder->join('out_celup oc',       'oc.id_out_celup   = other_out.id_out_celup', 'left');
        $builder->join('schedule_celup sc',  'sc.id_celup       = oc.id_celup',            'left');
        $builder->join('retur r',            'r.id_retur        = oc.id_retur',            'left');
        $builder->join('other_bon ob',       'ob.id_other_bon   = oc.id_other_bon',        'left');

        // Filter tanggal (jika keduanya valid)
        if ($tanggalAwal && $tanggalAkhir) {
            $builder->where('other_out.tgl_other_out >=', $tanggalAwal)
                ->where('other_out.tgl_other_out <=', $tanggalAkhir);
        } elseif ($tanggalAwal) {
            $builder->where('other_out.tgl_other_out >=', $tanggalAwal);
        } elseif ($tanggalAkhir) {
            $builder->where('other_out.tgl_other_out <=', $tanggalAkhir);
        }

        // Pencarian bebas ($key) di beberapa kolom umum
        if ($key !== '') {
            $builder->groupStart()
                ->like('sc.no_model', $key)
                ->orLike('r.no_model', $key)
                ->orLike('ob.no_model', $key)
                ->orLike('oc.no_model', $key)

                ->orLike('sc.item_type', $key)
                ->orLike('r.item_type', $key)
                ->orLike('ob.item_type', $key)

                ->orLike('sc.kode_warna', $key)
                ->orLike('r.kode_warna', $key)
                ->orLike('ob.kode_warna', $key)

                ->orLike('sc.warna', $key)
                ->orLike('r.warna', $key)
                ->orLike('ob.warna', $key)

                ->orLike('other_out.lot_other_out', $key)
                ->orLike('other_out.nama_cluster', $key)
                ->orLike('other_out.kategori', $key)
                ->groupEnd();
        }

        // Urutan terbaru dulu
        $builder->orderBy('other_out.tgl_other_out', 'DESC')
            ->orderBy('other_out.created_at',    'DESC');

        return $builder->get()->getResultArray();
    }
}
