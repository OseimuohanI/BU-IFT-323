 <?php /** $student, $incidents, $actions available */ ?>
<h2>Student Record: <?= htmlspecialchars($student['FirstName'].' '.$student['LastName']) ?></h2>
<p>
  <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=exportStudentRecordCsv&id=<?= (int)$student['StudentID'] ?>">Export CSV</a>
  <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=exportStudentRecordPdf&id=<?= (int)$student['StudentID'] ?>" style="margin-left:12px">Export PDF</a>
  <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=activeCases" style="margin-left:12px">Back to reports</a>
</p>

<h3>Incidents</h3>
<?php if (empty($incidents)): ?>
  <p class="muted">No incidents found.</p>
<?php else: ?>
  <table>
    <thead><tr><th>ID</th><th>Date</th><th>Location</th><th>Offenses</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach ($incidents as $it): ?>
      <tr>
        <td><?= $it['IncidentID'] ?></td>
        <td><?= htmlspecialchars($it['ReportDate']) ?></td>
        <td><?= htmlspecialchars($it['Location']) ?></td>
        <td><?= htmlspecialchars($it['OffenseSummary'] ?? '—') ?></td>
        <td><?= htmlspecialchars($it['Status']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<h3 style="margin-top:16px">Disciplinary Actions</h3>
<?php if (empty($actions)): ?>
  <p class="muted">No actions recorded.</p>
<?php else: ?>
  <table>
    <thead><tr><th>Date</th><th>Action</th><th>Duration</th><th>Decision Maker</th></tr></thead>
    <tbody>
      <?php foreach ($actions as $a): ?>
      <tr>
        <td><?= htmlspecialchars($a['ActionDate']) ?></td>
        <td><?= htmlspecialchars($a['ActionType']) ?></td>
        <td><?= (int)$a['DurationDays'] ?> days</td>
        <td><?= htmlspecialchars($a['DecisionMakerName'] ?? '—') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>