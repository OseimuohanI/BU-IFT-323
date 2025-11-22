<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Active Cases</title>
  <style>
    body{font-family: DejaVu Sans, Arial, sans-serif; font-size:12px}
    table{width:100%;border-collapse:collapse;margin-top:8px}
    th,td{border:1px solid #666;padding:6px;text-align:left}
    th{background:#f2f7f3}
  </style>
</head>
<body>
  <h2>Active Cases</h2>
  <table>
    <thead>
      <tr><th>ID</th><th>Date</th><th>Student</th><th>Location</th><th>Status</th><th>Offenses</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php if (!empty($cases)): foreach ($cases as $c): ?>
      <tr>
        <td><?= htmlspecialchars($c['IncidentID']) ?></td>
        <td><?= htmlspecialchars($c['ReportDate']) ?></td>
        <td><?= htmlspecialchars(($c['FirstName'] ?? '').' '.($c['LastName'] ?? '')) ?></td>
        <td><?= htmlspecialchars($c['Location']) ?></td>
        <td><?= htmlspecialchars($c['Status']) ?></td>
        <td><?= (int)($c['OffenseCount'] ?? 0) ?></td>
        <td><?= (int)($c['ActionCount'] ?? 0) ?></td>
      </tr>
      <?php endforeach; else: ?>
      <tr><td colspan="7">No active cases</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <p style="margin-top:10px;color:#666;font-size:11px">Generated: <?= date('Y-m-d H:i') ?></p>
</body>
</html>