<?php
include '../includes/koneksi.php';
$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM events WHERE id=$id");
$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_event'];
    $tanggal = $_POST['tanggal'];
    mysqli_query($conn, "UPDATE events SET nama_event='$nama', tanggal='$tanggal' WHERE id=$id");
    header('Location: ../index.php');
}
?>
<h2>Edit Event</h2>
<form method="post">
    <input type="text" name="nama_event" value="<?= $data['nama_event'] ?>" required><br>
    <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" required><br>
    <button type="submit">Update</button>
</form>
<a href="../index.php">Kembali</a>