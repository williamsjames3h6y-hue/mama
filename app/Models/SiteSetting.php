<?php

namespace App\Models;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'key',
        'value',
        'type'
    ];

    public function getByKey($key)
    {
        $results = $this->supabase->query($this->table, [
            'eq' => ['key' => $key],
            'limit' => 1
        ]);

        return !empty($results) ? $results[0] : null;
    }

    public function getAllSettings()
    {
        $settings = $this->all();
        $formatted = [];

        foreach ($settings as $setting) {
            $formatted[$setting['key']] = $setting['value'];
        }

        return $formatted;
    }

    public function updateSetting($key, $value)
    {
        $setting = $this->getByKey($key);

        if ($setting) {
            return $this->supabase->update($this->table, [
                'value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['key' => $key]);
        }

        return $this->create([
            'key' => $key,
            'value' => $value,
            'type' => 'string'
        ]);
    }
}
