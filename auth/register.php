<?php
include '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('$username', '$password')");
    header('Location: login.php');
}
?>
<h2>Register</h2>
<form method="post">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Daftar</button>
</form>
<a href="login.php">Sudah punya akun?</a>