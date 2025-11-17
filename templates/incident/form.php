<?php
$isEdit = !empty($incident);
$actionUrl = $isEdit ? BASE_URL . '/?controller=incident&action=update' : BASE_URL . '/?controller=incident&action=store';
$selectedStudent = $isEdit ? (int)$incident['StudentID'] : 0;
$selectedStatus = $isEdit ? ($incident['Status'] ?? 'Open') : 'Open';
$reportDateValue = $isEdit && !empty($incident['ReportDate']) ? date('Y-m-d\TH:i', strtotime($incident['ReportDate'])) : date('Y-m-d\TH:i');
?>
<h2><?= $isEdit ? 'Edit Incident' : 'Report Incident' ?></h2>
<form method="post" action="<?= $actionUrl ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="IncidentID" value="<?= htmlspecialchars($incident['IncidentID']) ?>">
    <?php endif; ?>

    <label>Report Date<br>
        <input type="datetime-local" name="ReportDate" required value="<?= $reportDateValue ?>">
    </label><br>

    <label>Student<br>
        <select name="StudentID" required>
            <option value="">-- Select student --</option>
            <?php foreach ($students as $s): ?>
                <option value="<?= $s['StudentID'] ?>" <?= ($s['StudentID'] == $selectedStudent) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['EnrollmentNo'].' - '.$s['FirstName'].' '.$s['LastName']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Reporter Staff ID (optional)<br>
        <input name="ReporterStaffID" value="<?= $isEdit ? htmlspecialchars($incident['ReporterStaffID'] ?? '') : '' ?>" placeholder="Staff ID">
    </label><br>

    <label>Location<br>
        <input name="Location" value="<?= $isEdit ? htmlspecialchars($incident['Location']) : '' ?>">
    </label><br>

    <label>Description<br>
        <textarea name="Description" rows="4"><?= $isEdit ? htmlspecialchars($incident['Description']) : '' ?></textarea>
    </label><br>

    <label>Status<br>
        <select name="Status">
            <?php
                $statuses = ['Open','Under Review','Actioned','Closed'];
                foreach ($statuses as $s):
            ?>
                <option value="<?= $s ?>" <?= ($s === $selectedStatus) ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <button type="submit" class="button"><?= $isEdit ? 'Update' : 'Save' ?></button>
    <a class="button secondary" href="<?= BASE_URL ?>/?controller=incident&action=index">Cancel</a>
</form>