
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Students</title>
    <style>
        body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size:12px }
        table { border-collapse: collapse; width: 100% }
        th, td { border: 1px solid #444; padding: 6px; text-align: left }
        th { background: #f2f2f2 }
        h1 { font-size:18px; margin-bottom:8px }
    </style>
</head>
<body>
    <h1>Students List</h1>
    <table>
        <thead>
            <tr><th>ID</th><th>Enrollment</th><th>Name</th><th>DOB</th><th>Gender</th><th>Email</th></tr>
        </thead>
        <tbody>
            <?php foreach ($students as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['StudentID']) ?></td>
                <td><?= htmlspecialchars($s['EnrollmentNo']) ?></td>
                <td><?= htmlspecialchars($s['FirstName'].' '.$s['LastName']) ?></td>
                <td><?= htmlspecialchars($s['DOB']) ?></td>
                <td><?= htmlspecialchars($s['Gender']) ?></td>
                <td><?= htmlspecialchars($s['Email']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p style="margin-top:12px; font-size:10px">Generated: <?= date('Y-m-d H:i') ?></p>
</body>
</html>