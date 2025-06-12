<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\OpenPoModel;
use App\Models\TrackingPoCovering;

class TrackingPoCoveringController extends BaseController
{
    protected $openPoModel;
    protected $trackingPoCoveringModel;
    protected $role;

    public function __construct()
    {
        $this->openPoModel = new OpenPoModel();
        $this->trackingPoCoveringModel = new TrackingPoCovering();
        $this->role = session()->get('role');
    }

    public function listTrackingPo()
    {
        $trackingData = $this->trackingPoCoveringModel->trackingData();

        $data = [
            'title' => 'Tracking PO Covering',
            'role' => $this->role,
            'active' => 'trackingPoCovering',
            'trackingPoCovering' => $trackingData,
        ];

        return view($this->role . '/po/listPo', $data);
    }
    public function TrackingPo($date)
    {
        $tgl_po = urldecode($date);
        $tgl_po = date('Y-m-d', strtotime($tgl_po));
        $trackingData = $this->trackingPoCoveringModel->trackingDataDaily($tgl_po);
        // dd($trackingData);
        $data = [
            'title' => 'Tracking PO Covering',
            'role' => $this->role,
            'active' => 'trackingPoCovering',
            'trackingPoCovering' => $trackingData,
        ];
        $role = $this->role;
        if ($role == 'covering') {
            return view($this->role . '/po/listPo', $data);
        } else {
            return view($this->role . '/pocovering/listPo', $data);
        }
    }

    public function updateListTrackingPo($id)
    {
        $dataPost = $this->request->getPost();
        $date = date('Y-m-d H:i:s');
        $tglPo = $dataPost['created_at'];
        // Ambil data lama
        $find = $this->trackingPoCoveringModel->find($id);
        $oldKeterangan = $find['keterangan'] ?? '';

        // Tambahkan keterangan baru
        $newKeterangan = $dataPost['status'] . ' (' . $date . ')';

        // Gabungkan dengan keterangan lama (pakai baris baru biar rapi)
        $keteranganGabung = trim($oldKeterangan . ",\n" . $newKeterangan);
        // dd ($keteranganGabung);
        $data = [
            'status' => $dataPost['status'],
            'keterangan' => $keteranganGabung,
            'admin' => session()->get('username'),
            'updated_at' => $date,
        ];

        if ($this->trackingPoCoveringModel->update($id, $data)) {
            return redirect()->to(base_url($this->role . '/po/listTrackingPo/' . $tglPo))->with('success', 'Data updated successfully');
        } else {
            return redirect()->to(base_url($this->role . '/po/listTrackingPo/' . $tglPo))->with('error', 'Failed to update data');
        }
    }
}
