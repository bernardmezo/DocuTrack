<?php

namespace Core;

use mysqli;
use mysqli_sql_exception;
use Exception;

class Database
{
    private static ?Database $instance = null;
    private ?mysqli $connection = null;
    private array $config;

    private function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    public static function getInstance(?array $config = null): Database
    {
        if (self::$instance === null) {
            if ($config === null) {
                throw new Exception('Database configuration required on first call');
            }
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    private function connect(): void
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $this->connection = new mysqli(
                $this->config['host'],
                $this->config['user'],
                $this->config['pass'],
                $this->config['name']
            );

            $this->connection->set_charset('utf8mb4');

        } catch (mysqli_sql_exception $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            
            if (getenv('APP_ENV') === 'production') {
                throw new Exception('Database connection failed.');
            }
            
            throw $e;
        }
    }

    public function getConnection(): mysqli
    {
        if ($this->connection === null || !$this->connection->ping()) {
            $this->connect();
        }

        return $this->connection;
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public function __destruct()
    {
        if ($this->connection !== null) {
            $this->connection->close();
        }
    }
}