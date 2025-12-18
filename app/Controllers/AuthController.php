<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;


class AuthController extends BaseController
{

    public function index()
    {

        return view('Auth/index');
    }
    public function login()
    {
        helper('audit');

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $userData = $this->userModel->login($username, $password);
        $ipAddress = $this->request->getIPAddress();

        if (!$userData) {
            // audit login gagal
            log_audit(
                module: 'AUTH',
                action: 'LOGIN_FAIL',
                refType: 'USER',
                refId: $username,
                message: 'Login gagal (username/password salah)',
                payloadOld: null,
                payloadNew: ['username' => $username, 'ip_address' => $ipAddress]
            );

            return redirect()->to(base_url('/login'))
                ->withInput()
                ->with('error', 'Invalid username or password');
        }

        session()->set('id_user', $userData['id']);
        session()->set('username', $userData['username']);
        session()->set('role', $userData['role']);

        // audit login sukses (pakai actor dari session, tapi kita juga bisa pakai actor manual)
        log_audit(
            module: 'AUTH',
            action: 'LOGIN',
            refType: 'USER',
            refId: $userData['id'],
            message: 'Login berhasil',
            payloadOld: null,
            payloadNew: [
                'id_user'   => $userData['id'],
                'username'  => $userData['username'],
                'role'      => $userData['role'],
                'ip_address'=> $ipAddress,
            ],
            actor: [
                'name' => $userData['username'],
                'role' => $userData['role'],
            ]
        );

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
        helper('audit');
        $idUser   = session()->get('id_user');
        $username = session()->get('username');
        $role     = session()->get('role');

        // audit logout
        log_audit(
            module: 'AUTH',
            action: 'LOGOUT',
            refType: 'USER',
            refId: $idUser ?? $username,
            message: 'User logout',
            payloadOld: [
                'id_user'  => $idUser,
                'username' => $username,
                'role'     => $role,
            ],
            payloadNew: null,
            actor: [
                'name' => $username,
                'role' => $role,
            ]
        );

        session()->destroy();
        return redirect()->to(base_url('/login'));
    }
}
