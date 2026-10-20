<?php

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected string $connectionName = 'default';

    public function __construct()
    {
        if ($this->table === '') {
            $this->table = strtolower((new ReflectionClass($this))->getShortName()) . 's';
        }
    }

    protected function db(): PDO
    {
        return Database::connection();
    }

    public function all(array $columns = ['*'], array $orderBy = []): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table;
        if (!empty($orderBy)) {
            $sql .= ' ORDER BY ' . $orderBy[0] . ' ' . ($orderBy[1] ?? 'ASC');
        }

        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id, array $columns = ['*']): ?array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE ' . $this->primaryKey . ' = ? LIMIT 1';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }

    public function where(string $column, $value, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE ' . $column . ' = ?';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first(array $conditions = [], array $columns = ['*']): ?array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table;

        if (!empty($conditions)) {
            $clauses = [];
            $values = [];

            foreach ($conditions as $column => $value) {
                $clauses[] = $column . ' = ?';
                $values[] = $value;
            }

            $sql .= ' WHERE ' . implode(' AND ', $clauses);
            $stmt = $this->db()->prepare($sql);
            $stmt->execute($values);
        } else {
            $stmt = $this->db()->prepare($sql);
            $stmt->execute();
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }

    public function create(array $data): int
    {
        return Database::insert($this->table, $data);
    }

    public function update($id, array $data): bool
    {
        return Database::update($this->table, $data, $this->primaryKey, $id);
    }

    public function delete($id): bool
    {
        return Database::delete($this->table, $this->primaryKey, $id);
    }

    public function paginate(int $page = 1, int $perPage = 20, array $conditions = []): array
    {
        $offset = ($page - 1) * $perPage;
        $sql = 'SELECT * FROM ' . $this->table;
        $params = [];

        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $column => $value) {
                $clauses[] = $column . ' = ?';
                $params[] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }

        $sql .= ' LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countSql = 'SELECT COUNT(*) FROM ' . $this->table;
        if (!empty($conditions)) {
            $countSql .= ' WHERE ' . implode(' AND ', array_map(fn($column) => $column . ' = ?', array_keys($conditions)));
            $countStmt = $this->db()->prepare($countSql);
            $countStmt->execute(array_values($conditions));
        } else {
            $countStmt = $this->db()->query($countSql);
        }

        $total = (int) $countStmt->fetchColumn();

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => (int) ceil($total / $perPage),
        ];
    }
}

