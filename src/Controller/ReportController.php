<?php
namespace Controller;

use Model\Database;
use Dompdf\Dompdf;

class ReportController
{
    protected $pdo;
    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function studentRecord()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { header('Location: ' . BASE_URL . '/?controller=student&action=index'); exit; }

        $student = $this->pdo->prepare("SELECT * FROM Student WHERE StudentID = :id");
        $student->execute([':id'=>$id]);
        $student = $student->fetch();

        $incidents = $this->pdo->prepare("
            SELECT i.*, 
              (SELECT GROUP_CONCAT(CONCAT(ot.Code,': ',ot.Description) SEPARATOR '; ') 
               FROM ReportOffense ro JOIN OffenseType ot ON ot.OffenseTypeID=ro.OffenseTypeID
               WHERE ro.IncidentID = i.IncidentID) AS OffenseSummary
            FROM IncidentReport i
            WHERE i.StudentID = :id
            ORDER BY i.ReportDate DESC
        ");
        $incidents->execute([':id'=>$id]);
        $incidents = $incidents->fetchAll();

        $actions = $this->pdo->prepare("
            SELECT da.*, s.Name AS DecisionMakerName
            FROM DisciplinaryAction da
            LEFT JOIN Staff s ON s.StaffID = da.DecisionMakerID
            JOIN IncidentReport i ON i.IncidentID = da.IncidentID
            WHERE i.StudentID = :id
            ORDER BY da.ActionDate DESC
        ");
        $actions->execute([':id'=>$id]);
        $actions = $actions->fetchAll();

        ob_start();
        include __DIR__ . '/../../templates/reports/student_record.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../templates/layout.php';
    }

    public function activeCases()
    {
        $stmt = $this->pdo->query("
            SELECT i.IncidentID, i.ReportDate, i.Location, i.Status,
                   s.StudentID, s.FirstName, s.LastName,
                   (SELECT COUNT(*) FROM ReportOffense ro WHERE ro.IncidentID = i.IncidentID) AS OffenseCount,
                   (SELECT COUNT(*) FROM DisciplinaryAction da WHERE da.IncidentID = i.IncidentID) AS ActionCount
            FROM IncidentReport i
            JOIN Student s ON s.StudentID = i.StudentID
            WHERE i.Status IN ('Open','Under Review')
            ORDER BY i.ReportDate DESC
        ");
        $cases = $stmt->fetchAll();

        ob_start();
        include __DIR__ . '/../../templates/reports/active_cases.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../templates/layout.php';
    }

    public function offenseTrend()
    {
        $from = $_GET['from'] ?? date('Y-01-01');
        $to   = $_GET['to']   ?? date('Y-m-d');

        $stmt = $this->pdo->prepare("
            SELECT ot.OffenseTypeID, ot.Code, ot.Description,
                   DATE_FORMAT(i.ReportDate, '%Y-%m') AS period,
                   i.Location,
                   COUNT(*) AS occurrences
            FROM ReportOffense ro
            JOIN OffenseType ot ON ot.OffenseTypeID = ro.OffenseTypeID
            JOIN IncidentReport i ON i.IncidentID = ro.IncidentID
            WHERE i.ReportDate BETWEEN :from AND :to
            GROUP BY period, ot.OffenseTypeID, i.Location
            ORDER BY period DESC, occurrences DESC
        ");
        $stmt->execute([':from'=>$from, ':to'=>$to]);
        $rows = $stmt->fetchAll();

        ob_start();
        include __DIR__ . '/../../templates/reports/offense_trend.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../templates/layout.php';
    }

    public function disciplinaryEffectiveness()
    {
        $window = max(1, (int)($_GET['window'] ?? 6)); // months
        $sql = "
            SELECT da.ActionType,
                   COUNT(DISTINCT da.ActionID) AS action_count,
                   SUM(
                     CASE WHEN EXISTS (
                         SELECT 1 FROM IncidentReport i2
                         JOIN IncidentReport i1 ON i1.IncidentID = da.IncidentID
                         WHERE i2.StudentID = i1.StudentID
                           AND i2.ReportDate > da.ActionDate
                           AND i2.ReportDate <= DATE_ADD(da.ActionDate, INTERVAL :w MONTH)
                     ) THEN 1 ELSE 0 END
                   ) AS repeat_count_within_window
            FROM DisciplinaryAction da
            GROUP BY da.ActionType
            ORDER BY action_count DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':w' => $window]);
        $rows = $stmt->fetchAll();

        ob_start();
        include __DIR__ . '/../../templates/reports/disciplinary_effectiveness.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../templates/layout.php';
    }

    protected function streamCsv(string $filename, array $columns, array $rows)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $out = fopen('php://output', 'w');
        fputcsv($out, $columns);
        foreach ($rows as $r) {
            $line = [];
            foreach ($columns as $c) $line[] = $r[$c] ?? '';
            fputcsv($out, $line);
        }
        fclose($out);
        exit;
    }

    protected function renderPdf(string $templatePath, array $vars, string $filename)
    {
        extract($vars, EXTR_SKIP);
        ob_start();
        include $templatePath;
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, ['Attachment' => 1]);
        exit;
    }

    public function exportStudentRecordCsv()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) exit('Invalid id');
        $studentStmt = $this->pdo->prepare("SELECT * FROM Student WHERE StudentID = :id");
        $studentStmt->execute([':id'=>$id]);
        $student = $studentStmt->fetch();

        $incStmt = $this->pdo->prepare("
            SELECT i.IncidentID as 'IncidentID', i.ReportDate as 'ReportDate', i.Location as 'Location', i.Status as 'Status',
              (SELECT GROUP_CONCAT(CONCAT(ot.Code,': ',ot.Description) SEPARATOR '; ') 
               FROM ReportOffense ro JOIN OffenseType ot ON ot.OffenseTypeID=ro.OffenseTypeID
               WHERE ro.IncidentID = i.IncidentID) AS 'OffenseSummary'
            FROM IncidentReport i
            WHERE i.StudentID = :id
            ORDER BY i.ReportDate DESC
        ");
        $incStmt->execute([':id'=>$id]);
        $incidents = $incStmt->fetchAll();

        $cols = ['IncidentID','ReportDate','Location','OffenseSummary','Status'];
        $this->streamCsv('student_'.$id.'_incidents.csv', $cols, $incidents);
    }

    public function exportStudentRecordPdf()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) exit('Invalid id');
        $studentStmt = $this->pdo->prepare("SELECT * FROM Student WHERE StudentID = :id");
        $studentStmt->execute([':id'=>$id]);
        $student = $studentStmt->fetch();

        $incStmt = $this->pdo->prepare("
            SELECT i.*, 
              (SELECT GROUP_CONCAT(CONCAT(ot.Code,': ',ot.Description) SEPARATOR '; ') 
               FROM ReportOffense ro JOIN OffenseType ot ON ot.OffenseTypeID=ro.OffenseTypeID
               WHERE ro.IncidentID = i.IncidentID) AS OffenseSummary
            FROM IncidentReport i
            WHERE i.StudentID = :id
            ORDER BY i.ReportDate DESC
        ");
        $incStmt->execute([':id'=>$id]);
        $incidents = $incStmt->fetchAll();

        $tpl = __DIR__ . '/../../templates/reports/pdf_student_record.php';
        $this->renderPdf($tpl, ['student'=>$student,'incidents'=>$incidents], 'student_'.$id.'_record.pdf');
    }

    public function exportActiveCasesCsv()
    {
        $stmt = $this->pdo->query("
            SELECT i.IncidentID, i.ReportDate, i.Location, i.Status,
                   s.StudentID, s.FirstName, s.LastName,
                   (SELECT COUNT(*) FROM ReportOffense ro WHERE ro.IncidentID = i.IncidentID) AS OffenseCount,
                   (SELECT COUNT(*) FROM DisciplinaryAction da WHERE da.IncidentID = i.IncidentID) AS ActionCount
            FROM IncidentReport i
            JOIN Student s ON s.StudentID = i.StudentID
            WHERE i.Status IN ('Open','Under Review')
            ORDER BY i.ReportDate DESC
        ");
        $cases = $stmt->fetchAll();
        $cols = ['IncidentID','ReportDate','StudentID','FirstName','LastName','Location','Status','OffenseCount','ActionCount'];
        $this->streamCsv('active_cases.csv', $cols, $cases);
    }

    public function exportActiveCasesPdf()
    {
        $stmt = $this->pdo->query("
            SELECT i.IncidentID, i.ReportDate, i.Location, i.Status,
                   s.StudentID, s.FirstName, s.LastName,
                   (SELECT COUNT(*) FROM ReportOffense ro WHERE ro.IncidentID = i.IncidentID) AS OffenseCount,
                   (SELECT COUNT(*) FROM DisciplinaryAction da WHERE da.IncidentID = i.IncidentID) AS ActionCount
            FROM IncidentReport i
            JOIN Student s ON s.StudentID = i.StudentID
            WHERE i.Status IN ('Open','Under Review')
            ORDER BY i.ReportDate DESC
        ");
        $cases = $stmt->fetchAll();
        $tpl = __DIR__ . '/../../templates/reports/pdf_active_cases.php';
        $this->renderPdf($tpl, ['cases'=>$cases], 'active_cases.pdf');
    }

    public function exportOffenseTrendCsv()
    {
        $from = $_GET['from'] ?? date('Y-01-01');
        $to   = $_GET['to']   ?? date('Y-m-d');
        $stmt = $this->pdo->prepare("
            SELECT ot.OffenseTypeID, ot.Code, ot.Description,
                   DATE_FORMAT(i.ReportDate, '%Y-%m') AS period,
                   i.Location,
                   COUNT(*) AS occurrences
            FROM ReportOffense ro
            JOIN OffenseType ot ON ot.OffenseTypeID = ro.OffenseTypeID
            JOIN IncidentReport i ON i.IncidentID = ro.IncidentID
            WHERE i.ReportDate BETWEEN :from AND :to
            GROUP BY period, ot.OffenseTypeID, i.Location
            ORDER BY period DESC, occurrences DESC
        ");
        $stmt->execute([':from'=>$from, ':to'=>$to]);
        $rows = $stmt->fetchAll();
        $cols = ['period','Code','Description','Location','occurrences'];
        $this->streamCsv('offense_trend.csv', $cols, $rows);
    }

    public function exportOffenseTrendPdf()
    {
        $from = $_GET['from'] ?? date('Y-01-01');
        $to   = $_GET['to']   ?? date('Y-m-d');
        $stmt = $this->pdo->prepare("
            SELECT ot.OffenseTypeID, ot.Code, ot.Description,
                   DATE_FORMAT(i.ReportDate, '%Y-%m') AS period,
                   i.Location,
                   COUNT(*) AS occurrences
            FROM ReportOffense ro
            JOIN OffenseType ot ON ot.OffenseTypeID = ro.OffenseTypeID
            JOIN IncidentReport i ON i.IncidentID = ro.IncidentID
            WHERE i.ReportDate BETWEEN :from AND :to
            GROUP BY period, ot.OffenseTypeID, i.Location
            ORDER BY period DESC, occurrences DESC
        ");
        $stmt->execute([':from'=>$from, ':to'=>$to]);
        $rows = $stmt->fetchAll();
        $tpl = __DIR__ . '/../../templates/reports/pdf_offense_trend.php';
        $this->renderPdf($tpl, ['rows'=>$rows,'from'=>$from,'to'=>$to], 'offense_trend.pdf');
    }

    public function exportEffectivenessCsv()
    {
        $window = max(1, (int)($_GET['window'] ?? 6));
        $sql = "
            SELECT da.ActionType,
                   COUNT(DISTINCT da.ActionID) AS action_count,
                   SUM(
                     CASE WHEN EXISTS (
                         SELECT 1 FROM IncidentReport i2
                         JOIN IncidentReport i1 ON i1.IncidentID = da.IncidentID
                         WHERE i2.StudentID = i1.StudentID
                           AND i2.ReportDate > da.ActionDate
                           AND i2.ReportDate <= DATE_ADD(da.ActionDate, INTERVAL :w MONTH)
                     ) THEN 1 ELSE 0 END
                   ) AS repeat_count_within_window
            FROM DisciplinaryAction da
            GROUP BY da.ActionType
            ORDER BY action_count DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':w' => $window]);
        $rows = $stmt->fetchAll();
        $cols = ['ActionType','action_count','repeat_count_within_window'];
        $this->streamCsv('disciplinary_effectiveness.csv', $cols, $rows);
    }

    public function exportEffectivenessPdf()
    {
        $window = max(1, (int)($_GET['window'] ?? 6));
        $sql = "
            SELECT da.ActionType,
                   COUNT(DISTINCT da.ActionID) AS action_count,
                   SUM(
                     CASE WHEN EXISTS (
                         SELECT 1 FROM IncidentReport i2
                         JOIN IncidentReport i1 ON i1.IncidentID = da.IncidentID
                         WHERE i2.StudentID = i1.StudentID
                           AND i2.ReportDate > da.ActionDate
                           AND i2.ReportDate <= DATE_ADD(da.ActionDate, INTERVAL :w MONTH)
                     ) THEN 1 ELSE 0 END
                   ) AS repeat_count_within_window
            FROM DisciplinaryAction da
            GROUP BY da.ActionType
            ORDER BY action_count DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':w' => $window]);
        $rows = $stmt->fetchAll();
        $tpl = __DIR__ . '/../../templates/reports/pdf_effectiveness.php';
        $this->renderPdf($tpl, ['rows'=>$rows,'window'=>$window], 'disciplinary_effectiveness.pdf');
    }
}