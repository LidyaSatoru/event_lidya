<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}
include '../includes/koneksi.php';

// Query untuk mengambil data peserta dengan join ke tabel events
$result = mysqli_query($conn, "SELECT peserta.*, events.nama_event, events.tanggal as tanggal_event 
                              FROM peserta
                              LEFT JOIN events ON peserta.id_event = events.id
                              ORDER BY peserta.id DESC");

// Hitung statistik
$total_peserta = mysqli_num_rows($result);
$stats_query = "SELECT COUNT(DISTINCT id_event) as total_events_with_participants FROM peserta";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peserta - Event Management</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Event Manager</h2>
                <div class="user-info">
                    Selamat datang, <?php echo $_SESSION['username']; ?>
                </div>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../index.php">Dashboard</a></li>
                    <li><a href="../event/add_event.php">Tambah Event</a></li>
                    <li><a href="peserta.php" class="active">Data Peserta</a></li>
                    <li><a href="add_peserta.php">Tambah Peserta</a></li>
                    <li><a href="../auth/logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <div class="header">
                <div class="header-content">
                    <h1>Data Peserta</h1>
                    <div class="header-actions">
                        <a href="add_peserta.php" class="btn btn-primary">+ Tambah Peserta</a>
                        <a href="../index.php" class="btn btn-outline">🏠 Dashboard</a>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_peserta; ?></div>
                        <div class="stat-label">Total Peserta</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total_events_with_participants']; ?></div>
                        <div class="stat-label">Event dengan Peserta</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo date('d'); ?></div>
                        <div class="stat-label">Hari ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo date('H'); ?></div>
                        <div class="stat-label">Jam sekarang</div>
                    </div>
                </div>

                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2>🧑‍🤝‍🧑 Daftar Peserta</h2>
                        <div style="display: flex; gap: 1rem;">
                            <input type="text" 
                                   id="searchInput" 
                                   placeholder="🔍 Cari peserta..." 
                                   class="form-control" 
                                   style="width: 300px;">
                            <select id="filterEvent" class="form-control" style="width: 200px;">
                                <option value="">Semua Event</option>
                                <?php
                                $events_query = mysqli_query($conn, "SELECT DISTINCT events.id, events.nama_event FROM events 
                                                                   INNER JOIN peserta ON events.id = peserta.id_event 
                                                                   ORDER BY events.nama_event");
                                while ($event = mysqli_fetch_assoc($events_query)) {
                                    echo "<option value='{$event['id']}'>{$event['nama_event']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table id="pesertaTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Peserta</th>
                                    <th>Email</th>
                                    <th>Event</th>
                                    <th>Tanggal Event</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)) { 
                                    $event_date = strtotime($row['tanggal_event']);
                                    $current_date = time();
                                    $status = ($event_date > $current_date) ? 'Upcoming' : 'Completed';
                                    $status_class = ($event_date > $current_date) ? 'btn-success' : 'btn-outline';
                                ?>
                                <tr data-event-id="<?= $row['id_event'] ?>">
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama']) ?></strong>
                                    </td>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($row['email']) ?>" 
                                           style="color: #667eea; text-decoration: none;">
                                            <?= htmlspecialchars($row['email']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span style="font-weight: 600; color: #333;">
                                            <?= htmlspecialchars($row['nama_event']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d M Y', strtotime($row['tanggal_event'])) ?></td>
                                    <td>
                                        <span class="btn <?= $status_class ?>" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">
                                            <?= $status ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit_peserta.php?id=<?= $row['id'] ?>" class="action-edit">
                                                ✏️ Edit
                                            </a>
                                            <a href="delete_peserta.php?id=<?= $row['id'] ?>" 
                                               onclick="return confirm('⚠️ Apakah Anda yakin ingin menghapus peserta ini?')" 
                                               class="action-delete">
                                                🗑️ Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php if (mysqli_num_rows($result) == 0) { ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #666; font-style: italic; padding: 2rem;">
                                        <div style="font-size: 3rem; margin-bottom: 1rem;">🤷‍♀️</div>
                                        Belum ada peserta yang terdaftar
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <h2>⚡ Aksi Cepat</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                        <a href="add_peserta.php" class="btn btn-primary" style="text-align: center; padding: 1rem;">
                            👥 Tambah Peserta Baru
                        </a>
                        <a href="../event/add_event.php" class="btn btn-success" style="text-align: center; padding: 1rem;">
                            📅 Buat Event Baru
                        </a>
                        <a href="../index.php" class="btn btn-outline" style="text-align: center; padding: 1rem;">
                            🏠 Kembali ke Dashboard
                        </a>
                        <button onclick="exportData()" class="btn btn-outline" style="text-align: center; padding: 1rem;">
                            📊 Export Data
                        </button>
                    </div>
                </div>
            </div>

            <div class="footer">
                <p>&copy; 2024 Event Management System. Dibuat dengan ❤️ untuk mengelola event dengan mudah.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const filterEvent = document.getElementById('filterEvent');
            const table = document.getElementById('pesertaTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedEvent = filterEvent.value;

                rows.forEach(row => {
                    const eventId = row.getAttribute('data-event-id');
                    const text = row.textContent.toLowerCase();
                    
                    const matchesSearch = searchTerm === '' || text.includes(searchTerm);
                    const matchesEvent = selectedEvent === '' || eventId === selectedEvent;
                    
                    row.style.display = (matchesSearch && matchesEvent) ? '' : 'none';
                });

                updateRowNumbers();
            }

            function updateRowNumbers() {
                const visibleRows = rows.filter(row => row.style.display !== 'none');
                visibleRows.forEach((row, index) => {
                    const firstCell = row.querySelector('td:first-child');
                    if (firstCell && !firstCell.getAttribute('colspan')) {
                        firstCell.textContent = index + 1;
                    }
                });
            }

            searchInput.addEventListener('input', filterTable);
            filterEvent.addEventListener('change', filterTable);

            const deleteLinks = document.querySelectorAll('.action-delete');
            deleteLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('⚠️ Apakah Anda yakin ingin menghapus peserta ini?\n\nTindakan ini tidak dapat dibatalkan!')) {
                        const row = this.closest('tr');
                        row.style.transition = 'opacity 0.3s ease';
                        row.style.opacity = '0.5';
                        
                        setTimeout(() => {
                            window.location.href = this.href;
                        }, 300);
                    }
                });
            });

            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f0f8ff';
                    this.style.transform = 'scale(1.01)';
                    this.style.transition = 'all 0.2s ease';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                    this.style.transform = 'scale(1)';
                });
            });
        });

        function exportData() {
            alert('📊 Fitur export data akan segera tersedia!\n\nAnda bisa menggunakan fungsi print browser untuk mencetak data.');
            window.print();
        }

        setInterval(function() {
           
        }, 300000);
    </script>
</body>
</html>