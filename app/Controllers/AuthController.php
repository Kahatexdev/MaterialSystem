<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\UserModel;

class AuthController extends BaseController
{

    public function index()
    {

        return view('Auth/index');
    }
    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $UserModel = new Usermodel;
        $userData = $UserModel->login($username, $password);
        if (!$userData) {
            return redirect()->to(base_url('/login'))->withInput()->with('error', 'Invalid username or password');
        }
        session()->set('id_user', $userData['id']);
        session()->set('username', $userData['username']);
        session()->set('role', $userData['role']);
        switch ($userData['role']) {
            case 'gbn':
                return redirect()->to(base_url('/gbn'));
                break;
            case 'celup':
                return redirect()->to(base_url('/celup'));
                break;
            case 'covering':
                return redirect()->to(base_url('/covering'));
                break;
            case 'monitoring':
                return redirect()->to(base_url('/monitoring'));
                break;
            case 'area':
                return redirect()->to(base_url('/area'));
                break;
            case 'kantordepan':
                return redirect()->to(base_url('/kantordepan/Report'));
                break;
            default:
                return redirect()->to(base_url('/login'))->withInput()->with('error', 'Invalid username or password');
                break;
        }
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/login'));
    }
}
