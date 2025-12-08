<?php

namespace App\Database;

use PDO;
use PDOException;
use App\Utils\Logger;

class Connection
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        
        $port = isset($config['port']) ? ';port=' . $config['port'] : '';
        $dsn = sprintf(
            "mysql:host=%s%s;dbname=%s;charset=%s",
            $config['host'],
            $port,
            $config['dbname'],
            $config['charset']
        );

        try {
            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            Logger::exception($e, [
                'host' => $config['host'],
                'dbname' => $config['dbname'],
                'username' => $config['username']
            ]);
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}

