<?php
namespace Model;

use PDO;

class Incident
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function all(): array
    {
        $sql = "SELECT i.*, s.FirstName, s.LastName
                FROM IncidentReport i
                JOIN Student s ON s.StudentID = i.StudentID
                ORDER BY i.ReportDate DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM IncidentReport WHERE IncidentID = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO IncidentReport (ReportDate, Location, ReporterStaffID, StudentID, Description, Status) VALUES (:rd, :loc, :rep, :sid, :desc, :status)");
        $stmt->execute([
            ':rd' => $data['ReportDate'],
            ':loc' => $data['Location'] ?: null,
            ':rep' => $data['ReporterStaffID'] ?: null,
            ':sid' => $data['StudentID'],
            ':desc' => $data['Description'] ?: null,
            ':status' => $data['Status'] ?: 'Open'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE IncidentReport SET ReportDate=:rd, Location=:loc, ReporterStaffID=:rep, StudentID=:sid, Description=:desc, Status=:status WHERE IncidentID = :id");
        return $stmt->execute([
            ':rd' => $data['ReportDate'],
            ':loc' => $data['Location'] ?: null,
            ':rep' => $data['ReporterStaffID'] ?: null,
            ':sid' => $data['StudentID'],
            ':desc' => $data['Description'] ?: null,
            ':status' => $data['Status'] ?: 'Open',
            ':id' => $id
        ]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['Open','Under Review','Actioned','Closed'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE IncidentReport SET Status = :status WHERE IncidentID = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM IncidentReport WHERE IncidentID = :id");
        return $stmt->execute([':id' => $id]);
    }
}