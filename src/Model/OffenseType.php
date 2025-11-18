<?php
namespace Model;

use PDO;

class OffenseType
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM OffenseType ORDER BY SeverityLevel DESC, Code ASC")->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM OffenseType WHERE OffenseTypeID = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }
}