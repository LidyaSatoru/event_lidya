<?php
include '../includes/koneksi.php';
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peserta WHERE id=$id"));
$events = mysqli_query($conn, "SELECT * FROM events");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $id_event = $_POST['id_event'];
    mysqli_query($conn, "UPDATE peserta SET nama='$nama', email='$email', id_event=$id_event WHERE id=$id");
    header('Location: peserta.php');
}
?>
<h2>Edit Peserta</h2>
<form method="post">
    <input type="text" name="nama" value="<?= $data['nama'] ?>" required><br>
    <input type="email" name="email" value="<?= $data['email'] ?>" required><br>
    <select name="id_event" required>
        <?php while ($row = mysqli_fetch_assoc($events)) { ?>
            <option value="<?= $row['id'] ?>" <?= $row['id'] == $data['id_event'] ? 'selected' : '' ?>>
                <?= $row['nama_event'] ?>
            </option>
        <?php } ?>
    </select><br>
    <button type="submit">Update</button>
</form>
<a href="peserta.php">Kembali</a>