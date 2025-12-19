<?php

use CodeIgniter\I18n\Time;

if (!function_exists('log_audit')) {
    /**
     * Simpan audit log ke tabel sp_audit_logs
     */
    function log_audit(
        string $module,
        string $action,
        ?string $refType = null,
        $refId = null,
        ?string $message = null,
        $payloadOld = null,
        $payloadNew = null,
        array $actor = []
    ): bool {
        $db = db_connect();

        // actor fallback dari session
        $actorName = $actor['name'] ?? (session()->get('username') ?? session()->get('name') ?? null);
        $actorRole = $actor['role'] ?? (session()->get('role') ?? null);

        // request info
        $req = service('request');

        // normalize payload -> JSON string
        $oldJson = normalize_audit_payload($payloadOld);
        $newJson = normalize_audit_payload($payloadNew);

        $data = [
            'log_time'    => Time::now()->toDateTimeString(),
            'actor_name'  => $actorName,
            'actor_role'  => $actorRole,
            'action'      => strtoupper($action),
            'module'      => strtoupper($module),
            'ref_type'    => $refType ? strtoupper($refType) : null,
            'ref_id'      => $refId !== null ? (string) $refId : null,
            'message'     => $message,
            'payload_old' => $oldJson,
            'payload_new' => $newJson,
            'ip_address'  => $req->getIPAddress(),
            'user_agent'  => substr((string) $req->getUserAgent(), 0, 200),
        ];

        return (bool) $db->table('audit_logs')->insert($data);
    }
}

if (!function_exists('normalize_audit_payload')) {
    function normalize_audit_payload($payload): ?string
    {
        if ($payload === null) return null;

        // kalau sudah string JSON valid
        if (is_string($payload)) {
            $trim = trim($payload);
            if ($trim === '') return null;

            json_decode($trim, true);
            if (json_last_error() === JSON_ERROR_NONE) return $trim;

            // bukan json -> simpan sebagai json string
            return json_encode(['raw' => $trim], JSON_UNESCAPED_UNICODE);
        }

        // array/object
        return json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}
