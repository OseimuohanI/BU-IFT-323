<?php ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Offense Trend</title>
  <style>
    body{font-family: DejaVu Sans, Arial, sans-serif; font-size:12px}
    table{width:100%;border-collapse:collapse;margin-top:10px}
    th,td{border:1px solid #666;padding:6px;text-align:left}
    th{background:#f2f7f3}
  </style>
</head>
<body>
  <h2>Offense Trend Report</h2>
  <p>From: <?= htmlspecialchars($from ?? '') ?> To: <?= htmlspecialchars($to ?? '') ?></p>
  <table>
    <thead>
      <tr><th>Period</th><th>Code</th><th>Description</th><th>Location</th><th>Occurrences</th></tr>
    </thead>
    <tbody>
      <?php if (!empty($rows)): ?>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['period']) ?></td>
          <td><?= htmlspecialchars($r['Code']) ?></td>
          <td><?= htmlspecialchars($r['Description']) ?></td>
          <td><?= htmlspecialchars($r['Location']) ?></td>
          <td><?= (int)$r['occurrences'] ?></td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5">No data</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <p style="margin-top:12px;font-size:11px;color:#666">Generated: <?= date('Y-m-d H:i') ?></p>
</body>
</html>