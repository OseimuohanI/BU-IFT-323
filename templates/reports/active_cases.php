<?php ?>
<h2>Active Cases</h2>
<p>
  <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=exportActiveCasesCsv">Export CSV</a>
  <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=exportActiveCasesPdf" style="margin-left:12px">Export PDF</a>
  <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=offenseTrend" style="margin-left:12px">Offense Trend</a>
</p>
<?php if (empty($cases)): ?>
  <p class="muted">No active cases.</p>
<?php else: ?>
  <table>
    <thead><tr><th>ID</th><th>Date</th><th>Student</th><th>Location</th><th>Status</th><th>Offenses</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($cases as $c): ?>
      <tr>
        <td><?= $c['IncidentID'] ?></td>
        <td><?= htmlspecialchars($c['ReportDate']) ?></td>
        <td><?= htmlspecialchars($c['FirstName'].' '.$c['LastName']) ?></td>
        <td><?= htmlspecialchars($c['Location']) ?></td>
        <td><?= htmlspecialchars($c['Status']) ?></td>
        <td><?= (int)$c['OffenseCount'] ?></td>
        <td><?= (int)$c['ActionCount'] ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>