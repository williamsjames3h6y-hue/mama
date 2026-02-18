<?php

class Database {
    private $supabaseUrl;
    private $supabaseKey;

    public function __construct() {
        $this->supabaseUrl = Config::get('SUPABASE_URL');
        $this->supabaseKey = Config::get('SUPABASE_KEY');
    }

    private function request($method, $table, $data = null, $params = []) {
        $url = $this->supabaseUrl . '/rest/v1/' . $table;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        $headers = [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        return false;
    }

    public function select($table, $conditions = [], $columns = '*') {
        $params = ['select' => $columns];

        foreach ($conditions as $key => $value) {
            $params[$key] = 'eq.' . $value;
        }

        return $this->request('GET', $table, null, $params);
    }

    public function insert($table, $data) {
        return $this->request('POST', $table, $data);
    }

    public function update($table, $data, $conditions) {
        $params = [];
        foreach ($conditions as $key => $value) {
            $params[$key] = 'eq.' . $value;
        }

        return $this->request('PATCH', $table, $data, $params);
    }

    public function delete($table, $conditions) {
        $params = [];
        foreach ($conditions as $key => $value) {
            $params[$key] = 'eq.' . $value;
        }

        return $this->request('DELETE', $table, null, $params);
    }

    public function query($table, $filters = []) {
        return $this->request('GET', $table, null, $filters);
    }
}
