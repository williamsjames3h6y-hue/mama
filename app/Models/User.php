<?php

namespace App\Models;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'email',
        'password_hash',
        'full_name',
        'role',
        'vip_level',
        'balance',
        'total_earned',
        'referral_code',
        'referred_by',
        'last_login'
    ];

    public function findByEmail($email)
    {
        $results = $this->supabase->query($this->table, [
            'eq' => ['email' => $email],
            'limit' => 1
        ]);

        return !empty($results) ? $results[0] : null;
    }

    public function getUserProjects($userId)
    {
        return $this->supabase->query('user_projects', [
            'eq' => ['user_id' => $userId],
            'order' => 'submitted_at.desc'
        ]);
    }

    public function getPayments($userId)
    {
        return $this->supabase->query('payments', [
            'eq' => ['user_id' => $userId],
            'order' => 'created_at.desc'
        ]);
    }

    public function getWithdrawals($userId)
    {
        return $this->supabase->query('withdrawals', [
            'eq' => ['user_id' => $userId],
            'order' => 'created_at.desc'
        ]);
    }
}
