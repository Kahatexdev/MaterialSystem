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

        $lmdValues = $this->request->getPost('lmd') ?? []; // Ambil nilai checkbox

        // Validasi aturan kombinasi
        $allowedLMD = ['L', 'M', 'D'];
        $allowedSingle = ['WHITE', 'BLACK'];

        $selectedLMD = array_intersect($lmdValues, $allowedLMD); // Pilihan LMD
        $selectedSingle = array_intersect($lmdValues, $allowedSingle); // Pilihan WHITE/BLACK

        if (count($selectedSingle) > 1 || (count($selectedSingle) > 0 && count($selectedLMD) > 0)) {
            return redirect()->back()->with('error', 'Pilih kombinasi LMD atau pilih salah satu antara WHITE dan BLACK.');
        }

        $lmdData = implode('', $lmdValues);

        $saveData = [
            'no_mesin' => $data['no_mesin'],
            'min_caps' => $data['min_caps'],
            'max_caps' => $data['max_caps'],
            'jml_lot' => $data['jml_lot'],
            'lmd' => $lmdData,
            'ket_mesin' => $data['ket_mesin'],
        ];

        if ($this->mesinCelupModel->save($saveData)) {
            return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))->with('success', 'Data berhasil disimpan.');
        } else {
            return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))->with('error', 'Data gagal disimpan.');
        }
    }

    public function getMesinDetails($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->mesinCelupModel->find($id);

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
        $no_mesin = $this->request->getPost('no_mesin');
        $lmdValues = $this->request->getPost('lmd') ?? [];

        $cekNoMesin = $this->mesinCelupModel
            ->where('no_mesin', $no_mesin)
            ->where('id_mesin !=', $id_mesin) // Pastikan tidak mengecek diri sendiri
            ->first();

        if ($cekNoMesin) {
            // Jika duplikat ditemukan, kembalikan pesan error
            return redirect()->to(base_url($this->role . '/mesin/mesinCelup'))
                ->withInput()
                ->with('error', 'No Mesin sudah ada di database.');
        }

        // Validasi kombinasi
        $allowedLMD = ['L', 'M', 'D'];
        $allowedSingle = ['WHITE', 'BLACK'];

        $selectedLMD = array_intersect($lmdValues, $allowedLMD); // Pilihan LMD
        $selectedSingle = array_intersect($lmdValues, $allowedSingle); // Pilihan WHITE/BLACK

        if (count($selectedSingle) > 1 || (count($selectedSingle) > 0 && count($selectedLMD) > 0)) {
            return redirect()->back()->withInput()->with('error', 'Kombinasi pilihan tidak valid. Pilih kombinasi LMD atau salah satu antara WHITE dan BLACK.');
        }

        $lmdData = implode(',', $lmdValues);

        $data = [
            'no_mesin' => $this->request->getPost('no_mesin'),
            'min_caps' => $this->request->getPost('min_caps'),
            'max_caps' => $this->request->getPost('max_caps'),
            'jml_lot' => $this->request->getPost('jml_lot'),
            'lmd' => $lmdData,
            'ket_mesin' => $this->request->getPost('ket_mesin'),
        ];

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
