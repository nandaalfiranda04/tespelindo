<?php
include 'config.php';

$query = "
  SELECT 
    DATE_FORMAT(tanggal_input, '%Y-%m-%d %H:%i') AS waktu,
    SUM(pengeluaraan_liter) AS total,
    GROUP_CONCAT(nama SEPARATOR ', ') AS nama_penginput
  FROM anggota
  GROUP BY waktu
  ORDER BY waktu ASC
";

$result = mysqli_query($koneksi, $query);

$labels = [];
$values = [];
$tooltips = [];

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['waktu'];                  // "2025-07-13 09:00"
    $values[] = (float) $row['total'];          // Liter
    $tooltips[] = $row['nama_penginput'];       // "Rudi, Santi"
}

header('Content-Type: application/json');
echo json_encode([
    'labels' => $labels,
    'values' => $values,
    'tooltips' => $tooltips
]);
