<?php
include '../includes/koneksi.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM peserta WHERE id=$id");
header('Location: peserta.php');