<?php

namespace App\Models;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = [
        'title',
        'description',
        'rate_min',
        'rate_max',
        'project_type',
        'vip_level_required',
        'status'
    ];

    public function getByVipLevel($vipLevel)
    {
        return $this->supabase->query($this->table, [
            'eq' => ['status' => 'open']
        ]);
    }

    public function getOpen()
    {
        return $this->supabase->query($this->table, [
            'eq' => ['status' => 'open'],
            'order' => 'vip_level_required.asc,created_at.desc'
        ]);
    }
}
