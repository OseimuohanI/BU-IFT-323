<?php ?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Student Record</title>
<style>body{font-family: DejaVu Sans, Arial; font-size:12px} table{width:100%;border-collapse:collapse} th,td{border:1px solid #666;padding:6px}</style>
</head><body>
<h2>Student Record: <?= htmlspecialchars($student['FirstName'].' '.$student['LastName']) ?></h2>
<table><thead><tr><th>ID</th><th>Date</th><th>Location</th><th>Offenses</th><th>Status</th></tr></thead><tbody>
<?php foreach($incidents as $it): ?>
<tr>
  <td><?= htmlspecialchars($it['IncidentID']) ?></td>
  <td><?= htmlspecialchars($it['ReportDate']) ?></td>
  <td><?= htmlspecialchars($it['Location']) ?></td>
  <td><?= htmlspecialchars($it['OffenseSummary']) ?></td>
  <td><?= htmlspecialchars($it['Status']) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table>
<p>Generated: <?= date('Y-m-d H:i') ?></p>
</body></html>