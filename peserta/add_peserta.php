<?php
include 'includes/koneksi.php';

$events = mysqli_query($conn, "SELECT * FROM events");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $id_event = $_POST['id_event'];

    mysqli_query($conn, "INSERT INTO peserta (nama, email, id_event) VALUES ('$nama', '$email', $id_event)");
    echo "<p>Pendaftaran berhasil!</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Peserta</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Form Pendaftaran Peserta</h2>
    <form method="post">
        <input type="text" name="nama" placeholder="Nama" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <select name="id_event" required>
            <option value="">Pilih Event</option>
            <?php while ($event = mysqli_fetch_assoc($events)) { ?>
                <option value="<?= $event['id'] ?>"><?= $event['nama_event'] ?> (<?= $event['tanggal'] ?>)</option>
            <?php } ?>
        </select><br>
        <button type="submit">Daftar</button>
    </form>
    <a href="index.php">Kembali ke Dashboard</a>
</div>
</body>
</html>
