<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}
include 'includes/koneksi.php';
$result = mysqli_query($conn, "SELECT * FROM events");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Halo, <?php echo $_SESSION['username']; ?></h1>
    <a href="auth/logout.php">Logout</a> | 
    <a href="event/add_event.php">Tambah Event</a> | 
    <a href="peserta/peserta.php">Data Peserta</a>

    <h2>Daftar Event</h2>
    <table>
        <tr><th>Nama Event</th><th>Tanggal</th><th>Aksi</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['nama_event'] ?></td>
            <td><?= $row['tanggal'] ?></td>
            <td>
                <a href="event/edit_event.php?id=<?= $row['id'] ?>">Edit</a> |
                <a href="event/delete_event.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>