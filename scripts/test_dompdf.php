<?php
require __DIR__ . '/../vendor/autoload.php';
echo class_exists('Dompdf\\Dompdf') ? "Dompdf OK\n" : "Dompdf NOT found\n";