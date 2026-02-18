<?php

namespace App\Models;

use App\Services\SupabaseService;

abstract class Model
{
    protected $table;
    protected $supabase;
    protected $fillable = [];

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function all($options = [])
    {
        return $this->supabase->query($this->table, $options);
    }

    public function find($id)
    {
        $results = $this->supabase->query($this->table, [
            'eq' => ['id' => $id],
            'limit' => 1
        ]);

        return !empty($results) ? $results[0] : null;
    }

    public function where($conditions, $options = [])
    {
        $options['eq'] = $conditions;
        return $this->supabase->query($this->table, $options);
    }

    public function create($data)
    {
        $filtered = $this->filterFillable($data);
        $result = $this->supabase->insert($this->table, $filtered);
        return !empty($result) ? $result[0] : null;
    }

    public function update($id, $data)
    {
        $filtered = $this->filterFillable($data);
        $result = $this->supabase->update($this->table, $filtered, ['id' => $id]);
        return !empty($result) ? $result[0] : null;
    }

    public function delete($id)
    {
        return $this->supabase->delete($this->table, ['id' => $id]);
    }

    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }

        $filtered = [];
        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $filtered[$field] = $data[$field];
            }
        }

        return $filtered;
    }
}
