<?php

namespace App\Models;

class Withdrawal extends Model
{
    protected $table = 'withdrawals';

    protected $fillable = [
        'user_id',
        'amount',
        'method',
        'account_details',
        'status',
        'processed_at'
    ];

    public function getByUser($userId)
    {
        return $this->supabase->query($this->table, [
            'eq' => ['user_id' => $userId],
            'order' => 'created_at.desc'
        ]);
    }

    public function getPending()
    {
        return $this->supabase->query($this->table, [
            'eq' => ['status' => 'pending'],
            'order' => 'created_at.asc'
        ]);
    }

    public function processWithdrawal($id, $status)
    {
        $withdrawal = $this->find($id);
        if (!$withdrawal) {
            throw new \Exception('Withdrawal not found');
        }

        $updateData = [
            'status' => $status,
            'processed_at' => date('Y-m-d H:i:s')
        ];

        if ($status === 'completed') {
            $user = $this->supabase->query('users', [
                'eq' => ['id' => $withdrawal['user_id']],
                'limit' => 1
            ]);

            if (!empty($user)) {
                $userData = $user[0];
                $newBalance = $userData['balance'] - $withdrawal['amount'];

                if ($newBalance < 0) {
                    throw new \Exception('Insufficient balance');
                }

                $this->supabase->update('users', [
                    'balance' => $newBalance
                ], ['id' => $withdrawal['user_id']]);
            }
        }

        return $this->update($id, $updateData);
    }
}
