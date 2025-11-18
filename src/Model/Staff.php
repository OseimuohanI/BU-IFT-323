<?php
namespace Model;

use PDO;

class Staff
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM Staff ORDER BY Name ASC")->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM Staff WHERE StaffID = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }
}