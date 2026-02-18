<?php

namespace App\Services;

use PDO;
use PDOException;

class MySQLService
{
    private $pdo;

    public function __construct()
    {
        $config = require BASE_PATH . '/config/database.php';
        $mysql = $config['mysql'];

        try {
            $dsn = "mysql:host={$mysql['host']};port={$mysql['port']};dbname={$mysql['database']};charset={$mysql['charset']}";
            $this->pdo = new PDO($dsn, $mysql['username'], $mysql['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function query($table, $options = [])
    {
        $sql = "SELECT * FROM `{$table}`";
        $params = [];

        if (isset($options['select'])) {
            $sql = "SELECT {$options['select']} FROM `{$table}`";
        }

        if (isset($options['eq'])) {
            $conditions = [];
            foreach ($options['eq'] as $key => $value) {
                $conditions[] = "`{$key}` = ?";
                $params[] = $value;
            }
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }
        }

        if (isset($options['order'])) {
            $order = str_replace('.', ' ', $options['order']);
            $sql .= " ORDER BY {$order}";
        }

        if (isset($options['limit'])) {
            $sql .= " LIMIT " . intval($options['limit']);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function insert($table, $data)
    {
        if (!isset($data['id']) || empty($data['id'])) {
            $data['id'] = $this->generateUUID();
        }

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`)
                VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));

        return [$this->find($table, $data['id'])];
    }

    public function update($table, $data, $match)
    {
        $setClauses = [];
        $params = [];

        foreach ($data as $key => $value) {
            $setClauses[] = "`{$key}` = ?";
            $params[] = $value;
        }

        $whereClauses = [];
        foreach ($match as $key => $value) {
            $whereClauses[] = "`{$key}` = ?";
            $params[] = $value;
        }

        $sql = "UPDATE `{$table}` SET " . implode(', ', $setClauses) .
               " WHERE " . implode(' AND ', $whereClauses);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $id = isset($match['id']) ? $match['id'] : null;
        if ($id) {
            return [$this->find($table, $id)];
        }

        return [array_merge($data, $match)];
    }

    public function delete($table, $match)
    {
        $whereClauses = [];
        $params = [];

        foreach ($match as $key => $value) {
            $whereClauses[] = "`{$key}` = ?";
            $params[] = $value;
        }

        $sql = "DELETE FROM `{$table}` WHERE " . implode(' AND ', $whereClauses);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return true;
    }

    public function find($table, $id)
    {
        $sql = "SELECT * FROM `{$table}` WHERE `id` = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    private function generateUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}
