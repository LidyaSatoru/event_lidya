<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}
include '../includes/koneksi.php';
$events = mysqli_query($conn, "SELECT * FROM events");
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $id_event = (int)$_POST['id_event'];
    
    // Validasi input
    if (empty($nama) || empty($email) || empty($id_event)) {
        $error_message = 'Semua field harus diisi!';
    } else {
        // Cek apakah email sudah terdaftar untuk event yang sama
        $check_query = "SELECT * FROM peserta WHERE email = '$email' AND id_event = $id_event";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error_message = 'Email sudah terdaftar untuk event ini!';
        } else {
            $insert_query = "INSERT INTO peserta (nama, email, id_event) VALUES ('$nama', '$email', $id_event)";
            if (mysqli_query($conn, $insert_query)) {
                $success_message = 'Pendaftaran berhasil! Peserta telah ditambahkan.';
                // Reset form
                $_POST = array();
            } else {
                $error_message = 'Gagal mendaftarkan peserta. Silakan coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peserta - Event Management</title>
    <link rel="stylesheet" href="../css/style.css">
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
                    <li><a href="../index.php">Dashboard</a></li>
                    <li><a href="../event/add_event.php">Tambah Event</a></li>
                    <li><a href="peserta.php">Data Peserta</a></li>
                    <li><a href="add_peserta.php" class="active">Tambah Peserta</a></li>
                    <li><a href="../auth/logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <div class="header">
                <div class="header-content">
                    <h1>Tambah Peserta</h1>
                    <div class="header-actions">
                        <a href="peserta.php" class="btn btn-outline">📋 Lihat Data Peserta</a>
                        <a href="../index.php" class="btn btn-primary">🏠 Dashboard</a>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="card">
                    <h2>Form Pendaftaran Peserta</h2>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            ✅ <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-error">
                            ❌ <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-container">
                        <form method="post" id="pesertaForm">
                            <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input type="text" 
                                       id="nama"
                                       name="nama" 
                                       class="form-control" 
                                       placeholder="Masukkan nama lengkap peserta"
                                       value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" 
                                       id="email"
                                       name="email" 
                                       class="form-control" 
                                       placeholder="contoh@email.com"
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="id_event">Pilih Event</label>
                                <select name="id_event" id="id_event" class="form-control" required>
                                    <option value="">-- Pilih Event --</option>
                                    <?php 
                                    mysqli_data_seek($events, 0); // Reset pointer
                                    while ($event = mysqli_fetch_assoc($events)) { 
                                        $selected = (isset($_POST['id_event']) && $_POST['id_event'] == $event['id']) ? 'selected' : '';
                                        $event_date = date('d M Y', strtotime($event['tanggal']));
                                        $is_past = strtotime($event['tanggal']) < time();
                                    ?>
                                        <option value="<?= $event['id'] ?>" <?= $selected ?> <?= $is_past ? 'disabled' : '' ?>>
                                            <?= htmlspecialchars($event['nama_event']) ?> 
                                            (<?= $event_date ?>)
                                            <?= $is_past ? ' - Event Sudah Berlalu' : '' ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success">
                                    ✅ Daftarkan Peserta
                                </button>
                                <a href="peserta.php" class="btn btn-outline">
                                    ❌ Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <h2>📋 Informasi</h2>
                    <div style="color: #666; line-height: 1.8;">
                        <p><strong>Panduan Pendaftaran:</strong></p>
                        <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
                            <li>Pastikan nama lengkap sesuai dengan identitas resmi</li>
                            <li>Email harus valid dan dapat diakses</li>
                            <li>Satu email hanya bisa mendaftar sekali per event</li>
                            <li>Event yang sudah berlalu tidak dapat dipilih</li>
                        </ul>
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
            const form = document.getElementById('pesertaForm');
            const namaInput = document.getElementById('nama');
            const emailInput = document.getElementById('email');
            const eventSelect = document.getElementById('id_event');

            namaInput.addEventListener('input', function() {
                if (this.value.length < 2) {
                    this.style.borderColor = '#ff6b6b';
                } else {
                    this.style.borderColor = '#51cf66';
                }
            });

            emailInput.addEventListener('input', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(this.value)) {
                    this.style.borderColor = '#ff6b6b';
                } else {
                    this.style.borderColor = '#51cf66';
                }
            });

            form.addEventListener('submit', function(e) {
                let isValid = true;
                const nama = namaInput.value.trim();
                const email = emailInput.value.trim();
                const event = eventSelect.value;

                if (nama.length < 2) {
                    namaInput.style.borderColor = '#ff6b6b';
                    isValid = false;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    emailInput.style.borderColor = '#ff6b6b';
                    isValid = false;
                }

                if (!event) {
                    eventSelect.style.borderColor = '#ff6b6b';
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('⚠️ Mohon lengkapi semua field dengan benar!');
                }
            });

            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>
</html>