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
        // dd ($trackingData);
        $data = [
            'title' => 'Tracking PO Covering',
            'role' => $this->role,
            'active' => 'trackingPoCovering',
            'trackingPoCovering' => $trackingData,
        ];

        return view($this->role . '/po/listPo', $data);
    }

    public function updateListTrackingPo($id)
    {
        $dataPost = $this->request->getPost();
        $date = date('Y-m-d H:i:s');
        $keterangan = $dataPost['status'] . '(' . $date . ')';
        $data = [
            'status' => $dataPost['status'],
            'keterangan' => $keterangan,
            'admin' => session()->get('username'),
            'updated_at' => $date,
        ];

        // dd ($data);

        if ($this->trackingPoCoveringModel->update($id, $data)) {
            return redirect()->to(base_url($this->role . '/po/listTrackingPo'))->with('success', 'Data updated successfully');
        } else {
            return redirect()->to(base_url($this->role . '/po/listTrackingPo'))->with('error', 'Failed to update data');
        }
    }
}
