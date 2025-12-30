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

        // $authService = service('authService');

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $ip       = $this->request->getIPAddress();

        $result = $this->authService->attemptLogin($username, $password, $ip);

        if (!$result['status']) {

            log_audit(
                module: 'AUTH',
                action: $result['locked'] ?? false ? 'LOGIN_BLOCKED' : 'LOGIN_FAIL',
                refType: 'USER',
                refId: $username,
                message: 'Login gagal',
                payloadOld: null,
                payloadNew: [
                    'username' => $username,
                    'ip' => $ip,
                    'detail' => $result
                ]
            );

       
            // pesan default
            $errorMessage = 'Invalid username or password';

            if (($result['locked'] ?? false) === true) {
                $errorMessage = 'Akun terkunci sementara';
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Akun terkunci sementara')
                ->with('login_info', [
                    'locked' => $result['locked'] ?? false,
                    'locked_until' => $result['locked_until'] ?? null,
                    'failed' => $result['failed'] ?? 0,
                    'max' => $result['max_attempt'] ?? 3,
                ]);
        }

        $user = $result['user'];

        session()->set([
            'id_user'  => $user['id_user'],
            'username' => $user['username'],
            'role'     => $user['role'],
        ]);

        log_audit(
            module: 'AUTH',
            action: 'LOGIN',
            refType: 'USER',
            refId: $user['id_user'],
            message: 'Login berhasil',
            payloadOld: null,
            payloadNew: [
                'username' => $user['username'],
                'ip' => $ip
            ]
        );

        return redirect()->to(base_url('/' . $user['role']));
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

    public function lockedUsers()
    {
        $users = $this->LoginAttemptModel->getDataUser();
        // dd ($users);
        $data = [
            'active' => $this->active,
            'title' => 'Monitoring',
            'role' => $this->role,
            'dataUser' => $users,
        ];
        return view($this->role . '/user/locked_users', $data);
    }


    public function unlockUser($idUser)
    {
        $this->authService->unlockUser($idUser);

        log_audit(
            module: 'AUTH',
            action: 'UNLOCK_USER',
            refType: 'USER',
            refId: $idUser,
            message: 'Admin unlock akun',
            payloadOld: null,
            payloadNew: ['id_user' => $idUser]
        );

        return redirect()->back()->with('success', 'Akun berhasil di-unlock');
    }

}
