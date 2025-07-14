<?php
include 'config.php';
header('Content-Type: application/json');

$interval = $_GET['interval'] ?? 'day';

switch ($interval) {
    case 'hour':
        $group = "DATE_FORMAT(tanggal_input, '%Y-%m-%d %H:00')";
        break;
    case 'day':
        $group = "DATE(tanggal_input)";
        break;
    case 'week':
        $group = "YEARWEEK(tanggal_input)";
        break;
    case 'month':
        $group = "DATE_FORMAT(tanggal_input, '%Y-%m')";
        break;
    default:
        $group = "DATE(tanggal_input)";
}

$query = "SELECT $group AS label, SUM(pengeluaraan_liter) AS total
          FROM anggota
          WHERE role='anggota'
          GROUP BY label
          ORDER BY label ASC
          LIMIT 20";

$data = mysqli_query($koneksi, $query);
$labels = [];
$values = [];

while ($row = mysqli_fetch_assoc($data)) {
    $labels[] = $row['label'];
    $values[] = $row['total'];
}

echo json_encode([
    "labels" => $labels,
    "values" => $values
]);
