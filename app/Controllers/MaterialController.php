<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class MaterialController extends BaseController
{
    protected $role;
    protected $filters;
    public function __construct()
    {
        $this->role = session()->get('role');
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
    public function index()
    {
        $data = [
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/dashboard/index', $data);
    }
}
