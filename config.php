<?php
$koneksi = mysqli_connect("localhost", "root", "", "pelindo");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
