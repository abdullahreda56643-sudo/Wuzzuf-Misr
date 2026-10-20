<?php

class Post extends Model
{
    protected string $table = 'posts';
    protected string $primaryKey = 'id';

    public function published(array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . " WHERE status = 'published' ORDER BY published_at DESC, created_at DESC";
        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function byAuthor(int $authorId, array $columns = ['*']): array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' WHERE author_id = ? ORDER BY created_at DESC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$authorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBySlug(string $slug, array $columns = ['*']): ?array
    {
        $select = implode(', ', $columns);
        $sql = 'SELECT ' . $select . ' FROM ' . $this->table . " WHERE slug = ? AND status = 'published' LIMIT 1";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }
}

