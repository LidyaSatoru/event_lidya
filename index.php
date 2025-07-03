<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}
include 'includes/koneksi.php';
$result = mysqli_query($conn, "SELECT * FROM events");

// Hitung statistik
$total_events = mysqli_num_rows($result);
$total_peserta = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peserta"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Event Management</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Event Manager</h2>
                <div class="user-info">
                    Selamat datang, <?php echo $_SESSION['username']; ?>
                </div>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php" class="active">Dashboard</a></li>
                    <li><a href="event/add_event.php">Tambah Event</a></li>
                    <li><a href="peserta/peserta.php">Data Peserta</a></li>
                    <li><a href="peserta/add_peserta.php">Tambah Peserta</a></li>
                    <li><a href="auth/logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-content">
                    <h1>Dashboard</h1>
                    <div class="header-actions">
                        <a href="event/add_event.php" class="btn btn-primary">+ Tambah Event</a>
                        <a href="peserta/add_peserta.php" class="btn btn-outline">+ Tambah Peserta</a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_events; ?></div>
                        <div class="stat-label">Total Events</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_peserta; ?></div>
                        <div class="stat-label">Total Peserta</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo date('d'); ?></div>
                        <div class="stat-label">Hari ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo date('m'); ?></div>
                        <div class="stat-label">Bulan ini</div>
                    </div>
                </div>

                <!-- Events Table -->
                <div class="card">
                    <h2>Daftar Event</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Event</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                mysqli_data_seek($result, 0); // Reset pointer
                                while ($row = mysqli_fetch_assoc($result)) { 
                                    $event_date = strtotime($row['tanggal']);
                                    $current_date = time();
                                    $status = ($event_date > $current_date) ? 'Upcoming' : 'Completed';
                                    $status_class = ($event_date > $current_date) ? 'btn-success' : 'btn-outline';
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($row['nama_event']) ?></strong></td>
                                    <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td>
                                        <span class="btn <?= $status_class ?>" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">
                                            <?= $status ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="event/edit_event.php?id=<?= $row['id'] ?>" class="action-edit">Edit</a>
                                            <a href="event/delete_event.php?id=<?= $row['id'] ?>" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus event ini?')" 
                                               class="action-delete">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php if (mysqli_num_rows($result) == 0) { ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #666; font-style: italic;">
                                        Belum ada event yang tersedia
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; 2024 Event Management System. Dibuat dengan ❤️ untuk mengelola event dengan mudah.</p>
            </div>
        </div>
    </div>

    <script>
        // Simple JavaScript untuk interaksi
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll untuk navigasi
            const navLinks = document.querySelectorAll('.sidebar-nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Hapus class active dari semua link
                    navLinks.forEach(l => l.classList.remove('active'));
                    // Tambah class active ke link yang diklik
                    this.classList.add('active');
                });
            });

            // Konfirmasi hapus yang lebih menarik
            const deleteLinks = document.querySelectorAll('.action-delete');
            deleteLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('⚠️ Apakah Anda yakin ingin menghapus event ini?\n\nTindakan ini tidak dapat dibatalkan!')) {
                        window.location.href = this.href;
                    }
                });
            });
        });
    </script>
</body>
</html>