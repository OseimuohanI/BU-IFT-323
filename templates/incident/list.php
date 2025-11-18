<?php
?>
<h2>Incidents</h2>
<p>
    <a href="<?= BASE_URL ?>/?controller=incident&action=create">Report Incident</a>
    <a href="<?= BASE_URL ?>/?controller=incident&action=exportPdf" style="margin-left:12px">Download PDF</a>
    <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=activeCases" style="margin-left:12px">Active Cases (Reports)</a>
</p>
<table>
    <tr><th>ID</th><th>Date</th><th>Student</th><th>Location</th><th>Status</th><th>#Offenses</th><th>#Actions</th><th>Actions</th></tr>
    <?php
    $pdo = \Model\Database::getConnection();
    foreach ($incidents as $i):
        $stmt1 = $pdo->prepare("SELECT COUNT(*) FROM ReportOffense WHERE IncidentID = :id");
        $stmt1->execute([':id' => $i['IncidentID']]);
        $offCount = $stmt1->fetchColumn();
        $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM DisciplinaryAction WHERE IncidentID = :id");
        $stmt2->execute([':id' => $i['IncidentID']]);
        $actCount = $stmt2->fetchColumn();
    ?>
    <tr>
        <td><?= htmlspecialchars($i['IncidentID']) ?></td>
        <td><?= htmlspecialchars($i['ReportDate']) ?></td>
        <td><?= htmlspecialchars($i['FirstName'].' '.$i['LastName']) ?></td>
        <td><?= htmlspecialchars($i['Location']) ?></td>
        <td><strong><?= htmlspecialchars($i['Status']) ?></strong></td>
        <td><?= (int)$offCount ?></td>
        <td><?= (int)$actCount ?></td>
        <td class="table-actions">
            <a href="<?= BASE_URL ?>/?controller=incident&action=view&id=<?= $i['IncidentID'] ?>" class="link-inline">View</a>
            <a href="<?= BASE_URL ?>/?controller=incident&action=edit&id=<?= $i['IncidentID'] ?>" class="link-inline">Edit</a>
            <a href="<?= BASE_URL ?>/?controller=incident&action=delete&id=<?= $i['IncidentID'] ?>" class="link-inline" onclick="return confirm('Delete incident?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>