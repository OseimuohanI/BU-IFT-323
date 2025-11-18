<?php
namespace Controller;

use Model\Incident;
use Model\Student;

class IncidentController
{
    protected $model;
    protected $studentModel;
    public function __construct()
    {
        $this->model = new Incident();
        $this->studentModel = new Student();
    }

    public function index()
    {
        $incidents = $this->model->all();
        ob_start();
        include __DIR__ . '/../../templates/incident/list.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../templates/layout.php';
    }

    public function create()
    {
        $students = $this->studentModel->all();
        $incident = null;
        ob_start();
        include __DIR__ . '/../../templates/incident/form.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../templates/layout.php';
    }

    public function store()
    {
        $data = $_POST;
        $this->model->create($data);
        header('Location: ' . BASE_URL . '/?controller=incident&action=index');
        exit;
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        $incident = $this->model->find($id);
        $students = $this->studentModel->all();
        ob_start();
        include __DIR__ . '/../../templates/incident/form.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../templates/layout.php';
    }

    public function update()
    {
        $id = (int)($_POST['IncidentID'] ?? 0);
        $data = $_POST;
        $this->model->update($id, $data);
        header('Location: ' . BASE_URL . '/?controller=incident&action=index');
        exit;
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        $this->model->delete($id);
        header('Location: ' . BASE_URL . '/?controller=incident&action=index');
        exit;
    }

    public function exportPdf()
    {
        $incidents = $this->model->all();
        ob_start();
        include __DIR__ . '/../../templates/incident/pdf.php';
        $html = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'incidents_' . date('Ymd_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => 1]);
        exit;
    }

    // new: change status (expects POST)
    public function changeStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/?controller=incident&action=index');
            exit;
        }

        $id = (int)($_POST['IncidentID'] ?? 0);
        $status = trim($_POST['Status'] ?? '');

        if ($id <= 0 || $status === '') {
            header('Location: ' . BASE_URL . '/?controller=incident&action=index');
            exit;
        }

        $this->model->updateStatus($id, $status);
        header('Location: ' . BASE_URL . '/?controller=incident&action=index');
        exit;
    }
}