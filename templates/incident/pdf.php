<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Incident Reports</title>
  <style>
    body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size:12px; color:#111 }
    h1 { font-size:16px; margin-bottom:8px }
    table { border-collapse: collapse; width:100% }
    th, td { border:1px solid #444; padding:6px; text-align:left; vertical-align:top }
    th { background:#f2f7f3; font-weight:700 }
    .muted { color:#666; font-size:11px }
  </style>
</head>
<body>
  <h1>Incident Reports</h1>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Report Date</th>
        <th>Student</th>
        <th>Location</th>
        <th>Description</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($incidents as $it): ?>
      <tr>
        <td><?= htmlspecialchars($it['IncidentID']) ?></td>
        <td><?= htmlspecialchars($it['ReportDate']) ?></td>
        <td><?= htmlspecialchars($it['FirstName'].' '.$it['LastName']) ?></td>
        <td><?= htmlspecialchars($it['Location']) ?></td>
        <td><?= nl2br(htmlspecialchars($it['Description'])) ?></td>
        <td><?= htmlspecialchars($it['Status']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p class="muted">Generated: <?= date('Y-m-d H:i') ?></p>
</body>
</html>