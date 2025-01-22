<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MesinCelupModel;

class MesinCelupController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $mesinCelupModel;

    public function __construct()
    {
        $this->mesinCelupModel = new MesinCelupModel();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
        if ($this->filters   = ['role' => ['gbn']] != session()->get('role')) {
            return redirect()->to(base_url('/login'));
        }
        $this->isLogedin();
    }
    protected function isLogedin()
    {
        if (!session()->get('id_user')) {
            return redirect()->to(base_url('/login'));
        }
    }

    public function mesinCelup()
    {
        $mesinCelup = $this->mesinCelupModel->findAll();
        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'mesinCelup' => $mesinCelup,
        ];

        return view($this->role . '/schedule/datamesin', $data);
    }

    public function cekNoMesin()
    {
        if ($this->request->isAJAX()) {
            $noMesin = $this->request->getPost('no_mesin');
            $cekMesin = $this->mesinCelupModel->where('no_mesin', $noMesin)->first();

            if ($cekMesin) {
                return $this->response->setJSON(['success' => true]);
            } else {
                return $this->response->setJSON(['error' => true]);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }


    public function saveDataMesin()
    {
        $data = $this->request->getPost();
        // $noMesin = $data['no_mesin'];
        // $cekMesin = $this->mesinCelupModel->where('no_mesin', $noMesin)->first();

        // if ($cekMesin) {
        //     return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))->with('error', 'No Mesin sudah ada.');
        // }

        if ($this->mesinCelupModel->save($data)) {
            return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))->with('success', 'Data berhasil disimpan.');
        } else {
            return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))->with('error', 'Data gagal disimpan.');
        }
    }

    // public function saveDataMesin()
    // {
    //     $data = $this->request->getPost();

    //     $rules = [
    //         'no_mesin' => 'required|is_unique[mesin_celup.no_mesin]',
    //     ];

    //     if (!$this->validate($rules)) {
    //         // Kirim error validasi ke view
    //         return redirect()->back()->withInput()->with('validation', \Config\Services::validation()->listErrors());
    //     }

    //     if ($this->mesinCelupModel->save($data)) {
    //         return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))->with('success', 'Data berhasil disimpan.');
    //     } else {
    //         return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))->with('error', 'Data gagal disimpan.');
    //     }
    // }

    public function getMesinDetails($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->mesinCelupModel->find($id);
            log_message('info', 'Data mesin: ' . print_r($data, true));
            if ($data) {
                return $this->response->setJSON([
                    'id_mesin' => $data['id_mesin'],
                    'no_mesin' => $data['no_mesin'],
                    'min_caps' => $data['min_caps'],
                    'max_caps' => $data['max_caps'],
                    'jml_lot' => $data['jml_lot'],
                    'lmd' => $data['lmd'],
                    'ket_mesin' => $data['ket_mesin'],
                ]);
            } else {
                return $this->response->setJSON(['error' => 'Data tidak ditemukan.']);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function updateDataMesin()
    {
        $id_mesin = $this->request->getPost('id_mesin');

        $data = [
            'no_mesin' => $this->request->getPost('no_mesin'),
            'min_caps' => $this->request->getPost('min_caps'),
            'max_caps' => $this->request->getPost('max_caps'),
            'jml_lot' => $this->request->getPost('jml_lot'),
            'lmd' => $this->request->getPost('lmd'),
            'ket_mesin' => $this->request->getPost('ket_mesin'),
        ];

        // $noMesin = $data['no_mesin'];
        // $cekMesin = $this->mesinCelupModel->where('no_mesin', $noMesin)->first();
        // if ($cekMesin) {
        //     return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))->with('error', 'No Mesin sudah ada.');
        // }

        if ($this->mesinCelupModel->update($id_mesin, $data)) {
            return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))->with('success', 'Data berhasil diubah.');
        } else {
            return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))->with('error', 'Data gagal diubah.');
        }
    }

    public function deleteDataMesin($id)
    {
        if ($this->mesinCelupModel->delete($id)) {
            return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))->with('success', 'Data berhasil dihapus.');
        } else {
            return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))->with('error', 'Data gagal dihapus.');
        }
    }
}
