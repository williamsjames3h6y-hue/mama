<?php

namespace App\Models;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'status',
        'description'
    ];

    public function getByUser($userId)
    {
        return $this->supabase->query($this->table, [
            'eq' => ['user_id' => $userId],
            'order' => 'created_at.desc'
        ]);
    }

    public function getByType($type)
    {
        return $this->supabase->query($this->table, [
            'eq' => ['type' => $type],
            'order' => 'created_at.desc'
        ]);
    }
}
