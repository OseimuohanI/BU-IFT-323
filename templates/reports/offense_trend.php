<?php
// $rows, $from, $to provided
$grouped = [];
$periods = [];
foreach ($rows as $r) {
    $code = $r['Code'];
    $period = $r['period'];
    $grouped[$code][$period] = (int)$r['occurrences'];
    if (!in_array($period, $periods)) $periods[] = $period;
}
sort($periods);
?>
<h2>Offense Trend</h2>
<p>
  <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=offenseTrend">Back</a>
  <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=exportOffenseTrendCsv&from=<?=urlencode($from)?>&to=<?=urlencode($to)?>" style="margin-left:12px">Export CSV</a>
  <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=exportOffenseTrendPdf&from=<?=urlencode($from)?>&to=<?=urlencode($to)?>" style="margin-left:12px">Export PDF</a>
</p>

<?php if (!empty($rows)): ?>
  <h3 style="margin-top:18px">Raw Data</h3>
  <table>
    <thead>
      <tr><th>Period</th><th>Offense</th><th>Location</th><th>Occurrences</th></tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['period']) ?></td>
          <td><?= htmlspecialchars($r['Code'].' — '.$r['Description']) ?></td>
          <td><?= htmlspecialchars($r['Location']) ?></td>
          <td><?= (int)$r['occurrences'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <p class="muted">No data for selected range.</p>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  const periods = <?= json_encode($periods) ?>;
  const grouped = <?= json_encode($grouped) ?>;
  const colors = ['#2e8b57','#1f6f4d','#2aa26a','#3fbf7f','#66c28a','#95d9a7'];
  let idx=0;
  const datasets = Object.keys(grouped).map(code=>{
    const data = periods.map(p => grouped[code][p] ?? 0);
    const color = colors[idx++ % colors.length];
    return {
      label: code,
      data,
      borderColor: color,
      backgroundColor: color,
      fill: false,
      tension: 0.2
    };
  });

  const ctx = document.getElementById('offenseTrendChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: { labels: periods, datasets },
    options: {
      responsive:true,
      plugins: { legend: { position: 'top' } },
      scales: { y: { beginAtZero:true } }
    }
  });
})();