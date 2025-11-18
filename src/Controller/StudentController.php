<?php
namespace Controller;

use Model\Student;

class StudentController
{
    protected $model;
    public function __construct()
    {
        $this->model = new Student();
    }

    public function index()
    {
        $students = $this->model->all();
        ob_start();
        include __DIR__ . '/../../templates/student/list.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../templates/layout.php';
    }

    public function create()
    {
        $student = null;
        ob_start();
        include __DIR__ . '/../../templates/student/form.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../templates/layout.php';
    }

    public function store()
    {
        $data = $_POST;
        $this->model->create($data);
        header('Location: ' . BASE_URL . '/?controller=student&action=index');
        exit;
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        $student = $this->model->find($id);
        ob_start();
        include __DIR__ . '/../../templates/student/form.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../templates/layout.php';
    }

    public function update()
    {
        $id = (int)($_POST['StudentID'] ?? 0);
        $data = $_POST;
        $this->model->update($id, $data);
        header('Location: ' . BASE_URL . '/?controller=student&action=index');
        exit;
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        $this->model->delete($id);
        header('Location: ' . BASE_URL . '/?controller=student&action=index');
        exit;
    }

    public function exportPdf()
    {
        $students = $this->model->all();
        ob_start();
        include __DIR__ . '/../../templates/student/pdf.php';
        $html = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'students_' . date('Ymd_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => 1]);
        exit;
    }
}