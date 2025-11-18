<?php
namespace Model;

use PDO;

class ReportOffense
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listByIncident(int $incidentId): array
    {
        $sql = "SELECT ro.ReportOffenseID, ro.Notes, ot.OffenseTypeID, ot.Code, ot.Description, ot.SeverityLevel
                FROM ReportOffense ro
                JOIN OffenseType ot ON ot.OffenseTypeID = ro.OffenseTypeID
                WHERE ro.IncidentID = :iid
                ORDER BY ot.SeverityLevel DESC, ot.Code ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iid' => $incidentId]);
        return $stmt->fetchAll();
    }

    public function create(int $incidentId, int $offenseTypeId, ?string $notes = null): int
    {
        $stmt = $this->db->prepare("INSERT INTO ReportOffense (IncidentID, OffenseTypeID, Notes) VALUES (:iid, :otid, :notes)");
        $stmt->execute([':iid' => $incidentId, ':otid' => $offenseTypeId, ':notes' => $notes]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM ReportOffense WHERE ReportOffenseID = :id");
        return $stmt->execute([':id' => $id]);
    }
}