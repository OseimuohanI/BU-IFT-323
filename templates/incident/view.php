<?php
/** available variables: $incident, $offenses, $actions, $offenseTypes, $staff */
?>
<h2>Incident #<?= htmlspecialchars($incident['IncidentID']) ?></h2>
<p>
    <a href="<?= BASE_URL ?>/?controller=incident&action=index" class="link-inline">Back to Incidents</a>
    <a href="<?= BASE_URL ?>/?controller=incident&action=edit&id=<?= $incident['IncidentID'] ?>" class="link-inline" style="margin-left:8px">Edit</a>
</p>

<section style="margin-top:12px">
    <h3>Details</h3>
    <p><strong>Reported:</strong> <?= htmlspecialchars($incident['ReportDate']) ?></p>
    <p><strong>Student:</strong> <?= htmlspecialchars($incident['EnrollmentNo'].' - '.$incident['FirstName'].' '.$incident['LastName']) ?></p>
    <p><strong>Reporter:</strong> <?= htmlspecialchars($incident['ReporterName'] ?? '—') ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($incident['Location'] ?? '') ?></p>
    <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($incident['Description'] ?? '')) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($incident['Status']) ?></p>
</section>

<section style="margin-top:18px">
    <h3>Offenses</h3>
    <?php if (empty($offenses)): ?>
        <p class="muted">No offenses linked to this incident.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Code</th><th>Description</th><th>Severity</th><th>Notes</th></tr></thead>
            <tbody>
                <?php foreach ($offenses as $of): ?>
                <tr>
                    <td><?= htmlspecialchars($of['Code']) ?></td>
                    <td><?= htmlspecialchars($of['Description']) ?></td>
                    <td><?= htmlspecialchars($of['SeverityLevel']) ?></td>
                    <td><?= htmlspecialchars($of['Notes']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h4 style="margin-top:12px">Add Offense</h4>
    <form method="post" action="<?= BASE_URL ?>/?controller=incident&action=addOffense">
        <input type="hidden" name="IncidentID" value="<?= (int)$incident['IncidentID'] ?>">
        <label>Offense Type<br>
            <select name="OffenseTypeID" required>
                <option value="">-- select --</option>
                <?php foreach ($offenseTypes as $ot): ?>
                    <option value="<?= $ot['OffenseTypeID'] ?>"><?= htmlspecialchars($ot['Code'].' — '.$ot['Description']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Notes (optional)<br>
            <input name="Notes" style="width:60%" />
        </label><br><br>
        <button type="submit" class="link-inline">Attach Offense</button>
    </form>
</section>

<section style="margin-top:18px">
    <h3>Disciplinary Actions</h3>
    <?php if (empty($actions)): ?>
        <p class="muted">No actions recorded.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Date</th><th>Action</th><th>Duration</th><th>Decision Maker</th><th>Notes</th></tr></thead>
            <tbody>
                <?php foreach ($actions as $ac): ?>
                <tr>
                    <td><?= htmlspecialchars($ac['ActionDate']) ?></td>
                    <td><?= htmlspecialchars($ac['ActionType']) ?></td>
                    <td><?= (int)$ac['DurationDays'] ?> days</td>
                    <td><?= htmlspecialchars($ac['DecisionMakerName'] ?? '—') ?></td>
                    <td><?= nl2br(htmlspecialchars($ac['Notes'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h4 style="margin-top:12px">Add Disciplinary Action</h4>
    <form method="post" action="<?= BASE_URL ?>/?controller=incident&action=addAction">
        <input type="hidden" name="IncidentID" value="<?= (int)$incident['IncidentID'] ?>">
        <label>Action Type<br><input name="ActionType" required></label><br>
        <label>Action Date<br><input type="date" name="ActionDate" value="<?= date('Y-m-d') ?>"></label><br>
        <label>Duration (days)<br><input type="number" name="DurationDays" min="0" value="0"></label><br>
        <label>Decision Maker<br>
            <select name="DecisionMakerID">
                <option value="">-- select --</option>
                <?php foreach ($staff as $st): ?>
                    <option value="<?= $st['StaffID'] ?>"><?= htmlspecialchars($st['Name'].' ('.$st['Role'].')') ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Notes<br><textarea name="Notes" rows="3"></textarea></label><br><br>
        <button type="submit" class="link-inline">Record Action</button>
    </form>
</section>