<?php
?>
<h2>Incidents</h2>
<p>
    <a href="<?= BASE_URL ?>/?controller=incident&action=create">Report Incident</a>
    <a href="<?= BASE_URL ?>/?controller=incident&action=exportPdf" style="margin-left:12px">Download PDF</a>
</p>
<table>
    <tr><th>ID</th><th>Date</th><th>Student</th><th>Location</th><th>Status</th><th>Actions</th></tr>
    <?php foreach ($incidents as $i): ?>
    <tr>
        <td><?= htmlspecialchars($i['IncidentID']) ?></td>
        <td><?= htmlspecialchars($i['ReportDate']) ?></td>
        <td><?= htmlspecialchars($i['FirstName'].' '.$i['LastName']) ?></td>
        <td><?= htmlspecialchars($i['Location']) ?></td>
        <td><strong><?= htmlspecialchars($i['Status']) ?></strong></td>
        <td class="table-actions">
            <a href="<?= BASE_URL ?>/?controller=incident&action=edit&id=<?= $i['IncidentID'] ?>" class="link-inline">Edit</a>
            <a href="<?= BASE_URL ?>/?controller=incident&action=delete&id=<?= $i['IncidentID'] ?>" class="link-inline" onclick="return confirm('Delete incident?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>