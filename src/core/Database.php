<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $configFile = ROOT_PATH . '/src/config/database.php';
        if (!file_exists($configFile)) {
            throw new RuntimeException('Database configuration file not found.');
        }

        $config = require $configFile;
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        $this->pdo = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            $config['options'] ?? []
        );
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function connection(): PDO
    {
        return self::getInstance()->pdo;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll($fetchMode);
    }

    public static function first(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch($fetchMode);
        return $result === false ? null : $result;
    }

    public static function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(',', $columns),
            implode(',', $placeholders)
        );

        $stmt = self::connection()->prepare($sql);
        $stmt->execute(array_values($data));
        return (int) self::connection()->lastInsertId();
    }

    public static function update(string $table, array $data, string $whereColumn, $whereValue): bool
    {
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = $column . ' = ?';
        }

        $sql = sprintf('UPDATE %s SET %s WHERE %s = ?', $table, implode(', ', $sets), $whereColumn);
        $values = array_values($data);
        $values[] = $whereValue;

        $stmt = self::connection()->prepare($sql);
        return $stmt->execute($values);
    }

    public static function delete(string $table, string $whereColumn, $whereValue): bool
    {
        $sql = sprintf('DELETE FROM %s WHERE %s = ?', $table, $whereColumn);
        $stmt = self::connection()->prepare($sql);
        return $stmt->execute([$whereValue]);
    }
}

