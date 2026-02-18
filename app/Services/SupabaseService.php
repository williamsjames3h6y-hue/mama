<?php

namespace App\Services;

class SupabaseService
{
    private $url;
    private $apiKey;
    private $serviceKey;

    public function __construct()
    {
        $this->url = getenv('VITE_SUPABASE_URL');
        $this->apiKey = getenv('VITE_SUPABASE_SUPABASE_ANON_KEY');
        $this->serviceKey = getenv('SUPABASE_SERVICE_ROLE_KEY') ?: $this->apiKey;
    }

    public function query($table, $options = [])
    {
        $url = $this->url . '/rest/v1/' . $table;
        $params = [];

        if (isset($options['select'])) {
            $params['select'] = $options['select'];
        }

        if (isset($options['eq'])) {
            foreach ($options['eq'] as $key => $value) {
                $params[$key] = 'eq.' . $value;
            }
        }

        if (isset($options['order'])) {
            $params['order'] = $options['order'];
        }

        if (isset($options['limit'])) {
            $params['limit'] = $options['limit'];
        }

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . ($options['token'] ?? $this->apiKey),
            'Content-Type: application/json'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        throw new \Exception("Supabase query failed: " . $response);
    }

    public function insert($table, $data, $token = null)
    {
        $url = $this->url . '/rest/v1/' . $table;

        $headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . ($token ?? $this->serviceKey),
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        throw new \Exception("Supabase insert failed: " . $response);
    }

    public function update($table, $data, $match, $token = null)
    {
        $url = $this->url . '/rest/v1/' . $table . '?';

        foreach ($match as $key => $value) {
            $url .= $key . '=eq.' . urlencode($value) . '&';
        }
        $url = rtrim($url, '&');

        $headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . ($token ?? $this->serviceKey),
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        throw new \Exception("Supabase update failed: " . $response);
    }

    public function delete($table, $match, $token = null)
    {
        $url = $this->url . '/rest/v1/' . $table . '?';

        foreach ($match as $key => $value) {
            $url .= $key . '=eq.' . urlencode($value) . '&';
        }
        $url = rtrim($url, '&');

        $headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . ($token ?? $this->serviceKey),
            'Content-Type: application/json'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        throw new \Exception("Supabase delete failed: " . $response);
    }

    public function rpc($functionName, $params = [], $token = null)
    {
        $url = $this->url . '/rest/v1/rpc/' . $functionName;

        $headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . ($token ?? $this->serviceKey),
            'Content-Type: application/json'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        throw new \Exception("Supabase RPC failed: " . $response);
    }

    public function signUp($email, $password, $userData = [])
    {
        $url = $this->url . '/auth/v1/signup';

        $data = [
            'email' => $email,
            'password' => $password,
            'data' => $userData
        ];

        $headers = [
            'apikey: ' . $this->apiKey,
            'Content-Type: application/json'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        throw new \Exception("Supabase signup failed: " . $response);
    }

    public function signIn($email, $password)
    {
        $url = $this->url . '/auth/v1/token?grant_type=password';

        $data = [
            'email' => $email,
            'password' => $password
        ];

        $headers = [
            'apikey: ' . $this->apiKey,
            'Content-Type: application/json'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        throw new \Exception("Supabase signin failed: " . $response);
    }

    public function getUser($token)
    {
        $url = $this->url . '/auth/v1/user';

        $headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        return null;
    }
}
