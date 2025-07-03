<?php
include '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_event'];
    $tanggal = $_POST['tanggal'];

    mysqli_query($conn, "INSERT INTO events (nama_event, tanggal) VALUES ('$nama', '$tanggal')");
    header('Location: ../index.php');
}
?>
<h2>Tambah Event</h2>
<form method="post">
    <input type="text" name="nama_event" placeholder="Nama Event" required><br>
    <input type="date" name="tanggal" required><br>
    <button type="submit">Simpan</button>
</form>
<a href="../index.php">Kembali</a>