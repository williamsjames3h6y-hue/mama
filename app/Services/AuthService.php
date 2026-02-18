<?php

namespace App\Services;

class AuthService
{
    private $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function register($email, $password, $fullName, $referralCode = null)
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $userReferralCode = strtoupper(substr(md5($email . time()), 0, 8));

        $userData = [
            'email' => $email,
            'password_hash' => $passwordHash,
            'full_name' => $fullName,
            'role' => 'user',
            'vip_level' => 1,
            'balance' => 0,
            'total_earned' => 0,
            'referral_code' => $userReferralCode,
            'referred_by' => $referralCode
        ];

        $user = $this->supabase->insert('users', $userData);

        if ($referralCode) {
            $this->processReferralBonus($referralCode);
        }

        return $user[0];
    }

    public function login($email, $password)
    {
        $users = $this->supabase->query('users', [
            'eq' => ['email' => $email],
            'limit' => 1
        ]);

        if (empty($users)) {
            throw new \Exception('Invalid email or password');
        }

        $user = $users[0];

        if (!password_verify($password, $user['password_hash'])) {
            throw new \Exception('Invalid email or password');
        }

        $this->supabase->update('users',
            ['last_login' => date('Y-m-d H:i:s')],
            ['id' => $user['id']]
        );

        unset($user['password_hash']);

        $token = $this->createToken($user['id']);

        return [
            'token' => $token,
            'user' => $user
        ];
    }

    public function verifyToken($token)
    {
        if (!$token) {
            return null;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        $payload = json_decode(base64_decode($parts[1]), true);
        if (!$payload || !isset($payload['user_id'])) {
            return null;
        }

        $users = $this->supabase->query('users', [
            'eq' => ['id' => $payload['user_id']],
            'limit' => 1
        ]);

        if (empty($users)) {
            return null;
        }

        $user = $users[0];
        unset($user['password_hash']);

        return $user;
    }

    private function createToken($userId)
    {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'user_id' => $userId,
            'iat' => time(),
            'exp' => time() + (7 * 24 * 60 * 60)
        ]));
        $secret = getenv('JWT_SECRET') ?: 'your-secret-key-change-in-production';
        $signature = base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true));

        return "$header.$payload.$signature";
    }

    private function processReferralBonus($referralCode)
    {
        $referrers = $this->supabase->query('users', [
            'eq' => ['referral_code' => $referralCode],
            'limit' => 1
        ]);

        if (!empty($referrers)) {
            $referrer = $referrers[0];

            $settings = $this->supabase->query('site_settings', [
                'eq' => ['key' => 'referral_bonus'],
                'limit' => 1
            ]);

            $bonus = !empty($settings) ? floatval($settings[0]['value']) : 300;

            $this->supabase->update('users',
                ['balance' => $referrer['balance'] + $bonus],
                ['id' => $referrer['id']]
            );

            $this->supabase->insert('payments', [
                'user_id' => $referrer['id'],
                'type' => 'bonus',
                'amount' => $bonus,
                'status' => 'completed',
                'description' => 'Referral bonus'
            ]);
        }
    }
}
