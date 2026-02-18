<?php

class Database {
    private $conn;
    private $host;
    private $dbname;
    private $username;
    private $password;

    public function __construct() {
        $this->host = Config::get('DB_HOST');
        $this->dbname = Config::get('DB_NAME');
        $this->username = Config::get('DB_USER');
        $this->password = Config::get('DB_PASS');

        $this->connect();
    }

    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            return false;
        }
    }

    public function select($table, $where = [], $columns = '*') {
        $sql = "SELECT $columns FROM $table";
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $key => $value) {
                $conditions[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $stmt = $this->executeQuery($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function selectOne($table, $where = [], $columns = '*') {
        $results = $this->select($table, $where, $columns);
        return !empty($results) ? $results[0] : null;
    }

    public function insert($table, $data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->executeQuery($sql, array_values($data));
        return $stmt ? $this->conn->lastInsertId() : false;
    }

    public function update($table, $data, $where) {
        $setClause = [];
        $params = [];

        foreach ($data as $key => $value) {
            $setClause[] = "$key = ?";
            $params[] = $value;
        }

        $whereClause = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "$key = ?";
            $params[] = $value;
        }

        $sql = "UPDATE $table SET " . implode(', ', $setClause) . " WHERE " . implode(' AND ', $whereClause);

        $stmt = $this->executeQuery($sql, $params);
        return $stmt ? $stmt->rowCount() : false;
    }

    public function delete($table, $where) {
        $whereClause = [];
        $params = [];

        foreach ($where as $key => $value) {
            $whereClause[] = "$key = ?";
            $params[] = $value;
        }

        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereClause);

        $stmt = $this->executeQuery($sql, $params);
        return $stmt ? $stmt->rowCount() : false;
    }

    public function query($table, $where = [], $orderBy = 'created_at DESC', $columns = '*') {
        $sql = "SELECT $columns FROM $table";
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $key => $value) {
                $conditions[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY $orderBy";

        $stmt = $this->executeQuery($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function customQuery($sql, $params = []) {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    public function commit() {
        return $this->conn->commit();
    }

    public function rollback() {
        return $this->conn->rollBack();
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}
