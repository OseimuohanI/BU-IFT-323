<?php
?>
<h2>Students</h2>
<p>
    <a href="<?= BASE_URL ?>/?controller=student&action=create">Add Student</a>
    <!-- PDF export link -->
    <a href="<?= BASE_URL ?>/?controller=student&action=exportPdf" style="margin-left:12px">Download PDF</a>
</p>
<table>
    <tr><th>ID</th><th>Enrollment</th><th>Name</th><th>Email</th><th>Actions</th></tr>
    <?php foreach ($students as $s): ?>
    <tr>
        <td><?= htmlspecialchars($s['StudentID']) ?></td>
        <td><?= htmlspecialchars($s['EnrollmentNo']) ?></td>
        <td><?= htmlspecialchars($s['FirstName'].' '.$s['LastName']) ?></td>
        <td><?= htmlspecialchars($s['Email']) ?></td>
        <td>
            <a href="<?= BASE_URL ?>/?controller=report&action=studentRecord&id=<?= $s['StudentID'] ?>" class="link-inline">View Record</a> |
            <a href="<?= BASE_URL ?>/?controller=student&action=edit&id=<?= $s['StudentID'] ?>" class="link-inline">Edit</a> |
            <a href="<?= BASE_URL ?>/?controller=student&action=delete&id=<?= $s['StudentID'] ?>" class="link-inline" onclick="return confirm('Delete?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>