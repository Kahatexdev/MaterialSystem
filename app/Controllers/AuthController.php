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
    // public function login()
    // {
    //     helper('audit');

    //     // $authService = service('authService');

    //     $username = $this->request->getPost('username');
    //     $password = $this->request->getPost('password');
    //     $ip       = $this->request->getIPAddress();

    //     $result = $this->authService->attemptLogin($username, $password, $ip);

    //     if (!$result['status']) {

    //         log_audit(
    //             module: 'AUTH',
    //             action: $result['locked'] ?? false ? 'LOGIN_BLOCKED' : 'LOGIN_FAIL',
    //             refType: 'USER',
    //             refId: $username,
    //             message: 'Login gagal',
    //             payloadOld: null,
    //             payloadNew: [
    //                 'username' => $username,
    //                 'ip' => $ip,
    //                 'detail' => $result
    //             ]
    //         );

       
    //         // pesan default
    //         $errorMessage = 'Invalid username or password';

    //         if (($result['locked'] ?? false) === true) {
    //             $errorMessage = 'Akun terkunci sementara';
    //         }

    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'Akun terkunci sementara')
    //             ->with('login_info', [
    //                 'locked' => $result['locked'] ?? false,
    //                 'locked_until' => $result['locked_until'] ?? null,
    //                 'failed' => $result['failed'] ?? 0,
    //                 'max' => $result['max_attempt'] ?? 3,
    //             ]);
    //     }

    //     $user = $result['user'];

    //     session()->set([
    //         'id_user'  => $user['id_user'],
    //         'username' => $user['username'],
    //         'role'     => $user['role'],
    //     ]);

    //     log_audit(
    //         module: 'AUTH',
    //         action: 'LOGIN',
    //         refType: 'USER',
    //         refId: $user['id_user'],
    //         message: 'Login berhasil',
    //         payloadOld: null,
    //         payloadNew: [
    //             'username' => $user['username'],
    //             'ip' => $ip
    //         ]
    //     );

    //     return redirect()->to(base_url('/' . $user['role']));
    // }


    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $ip       = $this->request->getIPAddress();

        $authService = service('authService');
        $client = service('curlrequest');

        $response = $client->post(
            'http://192.168.1.5/ComplaintSystem/public/api/login',
            [
                'http_errors' => false, // 🔥 PENTING
                'form_params' => [
                    'username' => $username,
                    'password' => $password
                ]
            ]
        );

        $data = json_decode($response->getBody(), true);

        if (!$data || $data['success'] !== true) {
            $result = $this->authService->attemptLogin($username, $password, $ip);

            if (!$result['status']) {
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
        }


        session()->set([
            'auth_token' => $data['token'],
            'id_user'    => $data['user']['id_user'],
            'username'   => $data['user']['username'],
            'role'       => $data['user']['role'],
            'logged_in'  => true
        ]);

        return redirect()->to(base_url('/' . session()->get('role')));
    }

    public function logout()
    {
        $token = session()->get('auth_token');

        // 🔥 Revoke token ke ComplaintSystem (optional tapi recommended)
        if ($token) {
            try {
                $client = service('curlrequest');
                $client->post(
                    'http://192.168.1.5/ComplaintSystem/public/api/logout',
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token
                        ]
                    ]
                );
            } catch (\Throwable $e) {
                // kalau auth server down, tetap lanjut logout lokal
                log_message('error', 'Logout revoke failed: ' . $e->getMessage());
            }
        }

        // 🔥 HAPUS SESSION LOKAL
        session()->destroy();

        return redirect()->to('/login');
    }


    // public function logout()
    // {
    //     helper('audit');
    //     $idUser   = session()->get('id_user');
    //     $username = session()->get('username');
    //     $role     = session()->get('role');

    //     // audit logout
    //     log_audit(
    //         module: 'AUTH',
    //         action: 'LOGOUT',
    //         refType: 'USER',
    //         refId: $idUser ?? $username,
    //         message: 'User logout',
    //         payloadOld: [
    //             'id_user'  => $idUser,
    //             'username' => $username,
    //             'role'     => $role,
    //         ],
    //         payloadNew: null,
    //         actor: [
    //             'name' => $username,
    //             'role' => $role,
    //         ]
    //     );

    //     session()->destroy();
    //     return redirect()->to(base_url('/login'));
    // }

    public function lockedUsers()
    {
        $client = service('curlrequest');

        $response = $client->get(
            'http://192.168.1.5/ComplaintSystem/public/api/MS/user',
            ['http_errors' => false]
        );

        $userdata = json_decode($response->getBody(), true);

        if (!isset($userdata['userData'])) {
            dd('Data user dari API tidak valid', $userdata);
        }

        // Ambil login attempt yang terkunci
        $loginAttempts = $this->db->table('login_attempts')
            ->where('locked_until IS NOT NULL')
            ->orderBy('locked_until', 'DESC')
            ->get()
            ->getResultArray();

        // Map login_attempt berdasarkan user_id
        $loginMap = [];
        foreach ($loginAttempts as $la) {
            $loginMap[$la['user_id']] = $la;
        }

        // Gabungkan data user + login_attempt
        $lockedUsers = [];
        foreach ($userdata['userData'] as $user) {
            if (isset($loginMap[$user['id_user']])) {
                $lockedUsers[] = array_merge($user, [
                    'failed_attempt' => $loginMap[$user['id_user']]['failed_attempt'],
                    'locked_until'   => $loginMap[$user['id_user']]['locked_until'],
                    'ip_address'     => $loginMap[$user['id_user']]['ip_address'],
                ]);
            }
        }

        $data = [
            'active'   => $this->active,
            'title'    => 'Monitoring',
            'role'     => $this->role,
            'dataUser' => $lockedUsers,
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
