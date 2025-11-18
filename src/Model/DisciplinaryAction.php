<?php
namespace Model;

use PDO;

class DisciplinaryAction
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listByIncident(int $incidentId): array
    {
        $sql = "SELECT da.*, s.Name AS DecisionMakerName
                FROM DisciplinaryAction da
                LEFT JOIN Staff s ON s.StaffID = da.DecisionMakerID
                WHERE da.IncidentID = :iid
                ORDER BY da.ActionDate DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iid' => $incidentId]);
        return $stmt->fetchAll();
    }

    public function create(int $incidentId, array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO DisciplinaryAction (IncidentID, ActionType, ActionDate, DurationDays, DecisionMakerID, Notes) VALUES (:iid, :atype, :adate, :dur, :dm, :notes)");
        $stmt->execute([
            ':iid' => $incidentId,
            ':atype' => $data['ActionType'],
            ':adate' => $data['ActionDate'],
            ':dur' => $data['DurationDays'] ?? 0,
            ':dm' => $data['DecisionMakerID'] ?: null,
            ':notes' => $data['Notes'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }
}