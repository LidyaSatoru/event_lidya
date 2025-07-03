<?php
include '../includes/koneksi.php';
$result = mysqli_query($conn, "SELECT peserta.*, events.nama_event FROM peserta 
    LEFT JOIN events ON peserta.id_event = events.id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Peserta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Data Peserta</h2>
    <a href="add_peserta.php">Tambah Peserta</a>
    <table>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Event</th>
            <th>Aksi</th>
        </tr>
        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['nama_event'] ?></td>
            <td>
                <a href="edit_peserta.php?id=<?= $row['id'] ?>">Edit</a> |
                <a href="delete_peserta.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    <a href="../index.php">Kembali ke Dashboard</a>
</div>
</body>
</html>
