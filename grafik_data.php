<?php
include 'config.php';

// Ambil data pemasukan BBM berdasarkan waktu lengkap (tanggal + jam)
$query = "
  SELECT 
    DATE_FORMAT(tanggal, '%Y-%m-%d %H:%i') AS waktu,
    SUM(jumlah_liter) AS total
  FROM log_minyak
  WHERE jenis = 'pemasukan'
  GROUP BY waktu
  ORDER BY waktu ASC
";

$result = mysqli_query($koneksi, $query);

$labels = [];
$values = [];

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['waktu'];         // Sama format: 2025-07-13 09:00
    $values[] = (float) $row['total']; // Total pemasukan pada waktu itu
}

header('Content-Type: application/json');
echo json_encode([
    'labels' => $labels,
    'values' => $values
]);
