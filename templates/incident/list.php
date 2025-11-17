<?php
$statuses = ['Open','Under Review','Actioned','Closed'];
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
        <td>
            <!-- current status label -->
            <strong><?= htmlspecialchars($i['Status']) ?></strong>
            <!-- inline status change form -->
            <form method="post" action="<?= BASE_URL ?>/?controller=incident&action=changeStatus" style="display:inline-block; margin-left:8px;">
                <input type="hidden" name="IncidentID" value="<?= $i['IncidentID'] ?>">
                <select name="Status" aria-label="Change status" style="padding:6px 8px; border-radius:6px;">
                    <?php foreach ($statuses as $s): ?>
                        <option value="<?= $s ?>" <?= ($s === $i['Status']) ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="link-inline" style="background:none;border:none;padding:6px 8px;cursor:pointer">Update</button>
            </form>
        </td>
        <td class="table-actions">
            <a href="<?= BASE_URL ?>/?controller=incident&action=view&id=<?= $i['IncidentID'] ?>" class="link-inline">View</a>
            <a href="<?= BASE_URL ?>/?controller=incident&action=edit&id=<?= $i['IncidentID'] ?>" class="link-inline">Edit</a>
            <a href="<?= BASE_URL ?>/?controller=incident&action=delete&id=<?= $i['IncidentID'] ?>" class="link-inline" onclick="return confirm('Delete incident?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>