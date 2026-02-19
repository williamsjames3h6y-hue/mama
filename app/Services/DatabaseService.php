<?php

namespace App\Services;

class DatabaseService
{
    private $driver;

    public function __construct()
    {
        $this->driver = new MySQLService();
    }

    public function query($table, $options = [])
    {
        return $this->driver->query($table, $options);
    }

    public function insert($table, $data, $token = null)
    {
        return $this->driver->insert($table, $data, $token);
    }

    public function update($table, $data, $match, $token = null)
    {
        return $this->driver->update($table, $data, $match, $token);
    }

    public function delete($table, $match, $token = null)
    {
        return $this->driver->delete($table, $match, $token);
    }

    public function getDriver()
    {
        return $this->driver;
    }
}
