<?php
include 'config.php';
date_default_timezone_set('Asia/Jakarta');
$role = $_GET['role'] ?? 'index';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard Pelindo</title>
  <link rel="stylesheet" href="style.css">
  <!-- Font Awesome Icons (wajib untuk ikon menu) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="navbar">
  <img src="logo_pelindo.png" alt="Pelindo Logo">
</div>

<div class="sidebar">
  <a href="index.php?role=admin" <?= $role == 'admin' ? 'class="active"' : '' ?>>
    <i class="fas fa-home"></i> Home Admin
  </a>
  <a href="index.php?role=anggota" <?= $role == 'anggota' ? 'class="active"' : '' ?>>
    <i class="fas fa-user-circle"></i> Anggota
  </a>
  <a href="laporan.php">
    <i class="fas fa-chart-line"></i> Laporan Bulanan
  </a>
</div>


<div class="main">

<?php if ($role == 'admin'): ?>
  <!-- ADMIN VIEW -->
  <div class="dashboard-cards">
    <div class="card">
      <h2>TOTAL PEMASUKAN</h2>
      <p>
        <?php
        $totalMasuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(jumlah_liter) AS total FROM log_minyak WHERE jenis='pemasukan'"))['total'] ?? 0;
        echo number_format($totalMasuk) . ' Liter';
        ?>
      </p>
    </div>
    <div class="card">
      <h2>TOTAL PENGELUARAN</h2>
      <p>
        <?php
        $totalKeluar = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(pengeluaraan_liter) AS total FROM anggota"))['total'] ?? 0;
        echo number_format($totalKeluar) . ' Liter';
        ?>
      </p>
    </div>
    <div class="card">
      <h2>SISA STOK</h2>
      <p><?php echo number_format($totalMasuk - $totalKeluar) . ' Liter'; ?></p>
    </div>
  </div>

  <form method="POST" action="">
    <h3>Tambah Pemasukan BBM</h3>
    <input type="date" name="tanggal" required>
    <input type="hidden" name="jenis" value="pemasukan">
    <input type="number" name="jumlah_liter" placeholder="Jumlah (Liter)" required>
    <button type="submit" name="simpan_log">Simpan</button>
  </form>

  <?php
  if (isset($_POST['simpan_log'])) {
      $tanggal = $_POST['tanggal'];
      $jenis = 'pemasukan';
      $jumlah = $_POST['jumlah_liter'];
      mysqli_query($koneksi, "INSERT INTO log_minyak(tanggal, jenis, jumlah_liter) VALUES('$tanggal','$jenis','$jumlah')");
      echo "<meta http-equiv='refresh' content='0?role=admin'>";
  }
  ?>
  <div class="chart-container">
    <h3>Grafik Batang</h3>
  </div>
  <div class="chart-container">
    <h3>Grafik Pemasukan</h3>
    <canvas id="grafikMinyak" height="80"></canvas>
    <h3>Grafik Pengeluaran</h3>
  <canvas id="grafikPengeluaran" height="80"></canvas>
  </div>

  <div class="table-box">
    <h3>Penggunaan Kendaraan</h3>
    <table>
      <tr><th>Kendaraan</th><th>Pengeluaran/L</th><th>Change</th></tr>
      <?php
      $data = mysqli_query($koneksi, "SELECT * FROM anggota");
      while ($d = mysqli_fetch_assoc($data)) {
          $random = rand(-20, 100);
          $change = $random . '%';
          echo "<tr><td>{$d['jenis_kendaraan']}</td><td>{$d['pengeluaraan_liter']}</td><td>" . ($random >= 0 ? "+$change" : $change) . "</td></tr>";
      }
      ?>
    </table>
  </div>

  <script>
fetch('grafik_data.php')
  .then(response => response.json())
  .then(data => {
    const ctx = document.getElementById('grafikMinyak').getContext('2d');
    
    new Chart(ctx, {
      type: 'bar', // Ganti 'line' jika ingin grafik garis
      data: {
        labels: data.labels, // Tanggal + Jam
        datasets: [{
          label: 'Pemasukan (Liter)',
          data: data.values,
          backgroundColor: '#00b894',
          borderColor: '#0984e3',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Pengeluaran BBM per Hari'
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Liter'
            }
          }
        }
      }
    });
  })
  .catch(error => {
    console.error('Gagal mengambil data grafik:', error);
  });
  fetch('grafik_pengeluaran.php')
  .then(res => res.json())
  .then(data => {
    new Chart(document.getElementById('grafikPengeluaran'), {
      type: 'bar',
      data: {
        labels: data.labels,
        datasets: [{
          label: 'Pengeluaran (Liter)',
          data: data.values,
          backgroundColor: '#d63031',
          customTooltips: data.tooltips // kita akan pakai ini di tooltip
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Grafik Pengeluaran BBM per Hari'
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const value = context.parsed.y;
                const index = context.dataIndex;
                const nama = data.tooltips[index];
                return [
                  `Pengeluaran: ${value} Liter`,
                  `Oleh: ${nama}`
                ];
              }
            }
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Liter'
            }
          }
        }
      }
    });
  });
</script>


<?php else: ?>
  <!-- ANGGOTA VIEW -->
  <h2>Tambah Data Anggota</h2>
  <form method="POST" action="">
    <select name="nama" required>
      <option value="">-- Pilih Nama --</option>
      <option>Rudi</option>
      <option>Santi</option>
      <option>Andi</option>
      <option>Lisa</option>
    </select>
    <select name="jenis_kendaraan" required>
      <option value="">-- Pilih Kendaraan --</option>
      <option>Motor</option>
      <option>Mobil</option>
      <option>Truk</option>
      <option>Bus</option>
    </select>
    <input type="number" name="pengeluaraan_liter" placeholder="Pengeluaran (Liter)" required>
    <button type="submit" name="simpan_anggota">Simpan</button>
  </form>

  <?php
  if (isset($_POST['simpan_anggota'])) {
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis_kendaraan'];
    $liter = $_POST['pengeluaraan_liter'];
    $tanggal = date('Y-m-d H:i:s'); // waktu lengkap
    mysqli_query($koneksi, "INSERT INTO anggota(nama, jenis_kendaraan, pengeluaraan_liter, role, tanggal_input) 
    VALUES('$nama','$jenis','$liter','anggota','$tanggal')");
    echo "<meta http-equiv='refresh' content='0?role=anggota'>";
}

  ?>
<?php endif; ?>

</div>
</body>
</html>
