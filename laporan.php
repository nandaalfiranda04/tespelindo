<?php
include 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Laporan Penggunaan BBM</title>
  <link rel="stylesheet" href="style.css">
  <head>
  <title>Dashboard Pelindo</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="navbar">
    <img src="logo_pelindo.png" alt="Pelindo Logo">
  </div>
  <div class="sidebar">
  <a href="index.php?role=admin"><i class="fas fa-home"></i> Home Admin</a>
  <a href="index.php?role=anggota"><i class="fas fa-user-circle"></i> Anggota</a>
  <a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan Bulanan</a>
</div>


<div class="main">
  <h2>Laporan Penggunaan BBM oleh Anggota</h2>
  <table>
    <tr>
      <th>Nama</th>
      <th>Jenis Kendaraan</th>
      <th>Tanggal</th>
      <th>Pengeluaran (Liter)</th>
    </tr>
    <?php
    $histori = mysqli_query($koneksi, "SELECT nama, jenis_kendaraan, tanggal_input, pengeluaraan_liter FROM anggota WHERE role='anggota' ORDER BY tanggal_input DESC");
    while ($row = mysqli_fetch_assoc($histori)) {
        echo "<tr>
                <td>{$row['nama']}</td>
                <td>{$row['jenis_kendaraan']}</td>
           <td>" . date('d-m-Y H:i:s', strtotime($row['tanggal_input'])) . "</td>
                <td>{$row['pengeluaraan_liter']}</td>
            </tr>";
    }
    ?>
  </table>
</div>

</body>
</html>
