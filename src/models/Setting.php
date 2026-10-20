<?php

class Setting extends Model
{
    protected string $table = 'settings';
    protected string $primaryKey = 'id';

    public function allByGroup(string $group): array
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE `group` = ? ORDER BY `key` ASC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$group]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get(string $key, $default = null)
    {
        $row = $this->first(['key' => $key]);
        if (!$row) {
            return $default;
        }

        $value = $row['value'];
        switch ($row['type']) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($value) ? (int) $value : $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
}

