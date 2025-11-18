<?php 
// $rows and $window provided
$labels = [];
$percent = [];
foreach ($rows as $r) {
    $labels[] = $r['ActionType'];
    $actions = (int)$r['action_count'];
    $repeats = (int)$r['repeat_count_within_window'];
    $percent[] = $actions ? round(($repeats/$actions)*100,1) : 0;
}
?>
<h2>Disciplinary Effectiveness (window <?= (int)$window ?> months)</h2>
<p>
  <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=disciplinaryEffectiveness">Back</a>
  <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=exportEffectivenessCsv&window=<?= (int)$window ?>">Export CSV</a>
  <a class="link-inline" href="<?= BASE_URL ?>/?controller=report&action=exportEffectivenessPdf&window=<?= (int)$window ?>" style="margin-left:12px">Export PDF</a>
</p>

<canvas id="effectivenessChart" height="200"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  const labels = <?= json_encode($labels) ?>;
  const data = <?= json_encode($percent) ?>;
  const ctx = document.getElementById('effectivenessChart').getContext('2d');
  new Chart(ctx, {
    type:'bar',
    data:{ labels, datasets:[{ label: '% repeats within window', data, backgroundColor:'#2e8b57' }] },
    options:{ responsive:true, scales:{ y:{ beginAtZero:true, max:100 } } }
  });
})();