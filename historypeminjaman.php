<?php
require_once 'conn.php';
require_once 'polmed_function.php';
require_once 'function.php';
session_start();

// 1. Pengecekan sesi dan autentikasi
if (!isset($_SESSION['id_user'])) { // Hapus !isset($_SESSION['loggedin']) karena sudah ditangani oleh id_user
    header("Location: login.php");
    exit();
}

// 2. Mengambil data pengguna dan data peminjaman
$nim = $_SESSION['id_user'];
$user_data = detail_user($conn, $nim);
$row1 = mysqli_fetch_assoc($user_data);

// Mengambil data peminjaman menggunakan fungsi yang disediakan
$peminjamanUser = list_peminjaman_user($conn, $nim);
$user_nama = $row1['nama'] ?? 'Pengguna';

$dummyImages = [
    'GAMBAR/membaca.jpg',
    'GAMBAR/BUKU REKOMEN/ATOMIC HABITS.jpg',
    'GAMBAR/BUKU REKOMEN/BUMII MANUSIA.jpg',
    'GAMBAR/BUKU REKOMEN/LAUT BERCERITA.jpg',
    'GAMBAR/BUKU REKOMEN/PERPUSTAKAAN TENGAH MALAM.jpg',
];
$foto = !empty($row1['foto_profile']) ? $row1['foto_profile'] : 'GAMBAR/image.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Peminjaman <?= $user_nama ?></title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="profile_modal.css">
    <style>
        .section-title {
            text-align: center;
            font-size: 30px;
            font-weight: 700;
            color: #6c3cff;
            margin: 40px 0 20px;
        }

        .history-table {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        .history-table th,
        .history-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .history-table th {
            background-color: #6c3cff;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }

        .history-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .history-table tr:hover {
            background-color: #f1f1f1;
        }

        .book-cover {
            width: 50px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 12px;
            white-space: nowrap;
        }

        .dipinjam {
            background-color: #ffcccc;
            color: #cc0000;
            border: 1px solid #cc0000;
        }

        .kembali {
            background-color: #ccffcc;
            color: #008000;
            border: 1px solid #008000;
        }

        /* ================================== */
        /* CSS RESPONSIF UNTUK TABEL */
        /* ================================== */
        @media (max-width: 768px) {
            .history-table {
                border-radius: 0;
                box-shadow: none;
                width: 100%;
                margin: 0 auto;
            }

            .history-table thead {
                display: none;
            }

            .history-table tbody,
            .history-table tr {
                display: block;
                width: 100%;
            }

            .history-table tr {
                margin-bottom: 20px;
                border: 1px solid #ddd;
                background: #fff;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            }

            .history-table td {
                display: block;
                text-align: right;
                padding: 10px 15px;
                border-bottom: 1px dotted #eee;
                position: relative;
            }

            .history-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                font-weight: 600;
                color: #444;
                /* Warna label */
            }

            .history-table td[data-label="Sampul"] {
                text-align: left;
            }

            .history-table td[data-label="Sampul"]::before {
                content: none;
            }

            .history-table tr td:last-child {
                border-bottom: none;
            }
        }
    </style>
</head>

<body>
    <nav>
        <div class="container" style="margin-top: 0px;">
            <div class="navbar">
                <div class="logo">
                    <img src="GAMBAR/POLMED.png" alt="">
                    Library <span>Polmed</span>
                </div>

                <div class="menu">
                    <a href="index.php#leaderboard">Leaderboard</a>
                    <a href="index.php#kiri">Berita</a>
                    <a href="index.php#bukurekomen">Buku</a>
                </div>

                <div class="tombol">
                    <?php
                    if (empty($_SESSION['id_user'])) {
                        // ... (bagian login/signup) ...
                    ?>
                        <div class="login-btn">
                            <a href="login.php" class="btn">Login</a>
                        </div>
                        <div class="sign-up"><a href="sign-up.php">SignUp</a></div>
                    <?php
                    } else {
                        $streak = isset($row1['poin_aktivitas']) ? (int)$row1['poin_aktivitas'] : 0;
                        $sudah_absen_hari_ini = kunjungan_is_checked_in($conn, $row1['nim']);

                        $fire_class = ($streak > 0 && $sudah_absen_hari_ini) ? 'fire' : 'nofire';
                    ?>
                        <div class="loggedin">
                            <div class="<?php echo $fire_class; ?>"
                                title="<?php echo $sudah_absen_hari_ini ? "$streak Hari Streak (Aktif)" : "$streak Hari Streak (Belum Check-in Hari Ini)"; ?>">
                                <i class="bi bi-fire"></i>
                                <?php echo $streak; ?>
                            </div>

                            <div class="foto-profile" onclick="openProfileModal()">
                                <img src="<?php echo $foto; ?>" alt="Foto Profil" id="pp" style="cursor: pointer;">
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeProfileModal()">&times;</span>
            <h3>Ubah Foto Profil</h3>
            <hr>

            <form action="API/upload_profile.php" method="POST" enctype="multipart/form-data" class="profile-upload-area">

                <input type="hidden" name="nim" value="<?= $nim ?>">
                <input type="hidden" name="redirect_to" value="historypeminjaman.php">

                <img src="<?= $foto ?>" alt="Preview Profil" id="preview-pp">

                <div class="upload-btn-wrapper">
                    <button type="button" class="btn-upload">Pilih Foto Baru</button>
                    <input type="file" name="new_profile_pic" id="profile-input" accept="image/*" required>
                </div>

                <button type="submit">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    <?php if (isset($_SESSION['id_user'])) { ?>

        <div class="sidebar-toggle" id="toggleBtn">
            <i class="icon">≡</i>
            <span class="tooltip">Buka Menu</span>
        </div>



        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="<?= $foto ?>" class="profile-img">
                <div class="profile-info">
                    <h3><?= $row1['nama'] ?></h3>
                    <p><?= $row1['nama_prodi'] ?></p>
                </div>
            </div>

            <div class="card-perpus">
                <a href="digitalcard.php" class="perpus-btn">
                    Kartu Digital Perpus
                </a>
            </div>
            <div class="card-perpus">
                <a href="historypeminjaman.php" class="perpus-btn">
                    History Peminjaman
                </a>
            </div>
            <div class="card-perpus">
                <a href="komentarsaya.php" class="perpus-btn">
                    Komentar Buku
                </a>
            </div>

            <button class="logout-btn" onclick="location.href='logout.php';">Logout</button>
        </div>
    <?php } ?>
    <h2 class="section-title">Riwayat Peminjaman Buku</h2>

    <table class="history-table">
        <thead>
            <tr>
                <th>Sampul</th>
                <th>Judul Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($peminjamanUser && mysqli_num_rows($peminjamanUser) > 0) {
                while ($peminjaman = mysqli_fetch_assoc($peminjamanUser)) {
                    // Tentukan kelas CSS untuk status
                    $status_class = ($peminjaman['status'] == 'dipinjam') ? 'dipinjam' : 'kembali';
                    // Tentukan tanggal kembali (jika NULL, tampilkan '-')
                    $tgl_kembali = $peminjaman['tanggal_kembali'] ? date('d-m-Y', strtotime($peminjaman['tanggal_kembali'])) : '-';
                    // Tentukan sampul buku (placeholder jika NULL)
                    $sampul_buku = !empty($peminjaman['sampul']) ? $peminjaman['sampul'] : $dummyImages[array_rand($dummyImages)];
            ?>
                    <tr>
                        <td data-label="Sampul">
                            <img src="<?= $sampul_buku ?>" alt="Sampul Buku" class="book-cover">
                        </td>
                        <td data-label="Judul Buku">
                            <a href="buku.php?id_buku=<?= $peminjaman['buku_id'] ?>"><?= $peminjaman['judul'] ?></a>
                        </td>
                        <td data-label="Tanggal Pinjam"><?= date('d-m-Y', strtotime($peminjaman['tanggal_pinjam'])) ?></td>
                        <td data-label="Tanggal Kembali"><?= $tgl_kembali ?></td>
                        <td data-label="Status">
                            <span class="status-badge <?= $status_class ?>">
                                <?= ucfirst($peminjaman['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Anda belum memiliki riwayat peminjaman buku.</td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <div style="text-align: center; margin: 30px;">
        <a href="index.php" class="cta-btn" style="background: #6c3cff; color: #fff; text-decoration: none;">Kembali ke Beranda</a>
    </div>
    <script>
        //SIDEBAR
        const toggleBtn = document.getElementById("toggleBtn");
        const sidebar = document.getElementById("sidebar");

        // HANYA JALANKAN EVENT LISTENER JIKA TOMBOL ADA (SUDAH LOGIN)
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener("click", () => {
                sidebar.classList.toggle("active");
            });
        }

        // Mendapatkan elemen modal
        var modal = document.getElementById("profileModal");
        var previewPP = document.getElementById("preview-pp");
        var profileInput = document.getElementById("profile-input");

        // Simpan URL foto saat ini (diambil dari PHP)
        var currentPhoto = "<?= $foto ?>";

        // Fungsi untuk membuka modal
        function openProfileModal() {
            modal.style.display = "block";
            // Pastikan preview gambar kembali ke foto saat ini saat modal dibuka
            previewPP.src = currentPhoto;
            if (profileInput) {
                profileInput.value = ''; // Reset input file
            }
        }

        // Fungsi untuk menutup modal
        function closeProfileModal() {
            modal.style.display = "none";
            // Reset preview jika pengguna menutup modal tanpa menyimpan
            previewPP.src = currentPhoto;
            if (profileInput) {
                profileInput.value = '';
            }
        }

        // Menutup modal jika pengguna mengklik di luar area modal
        window.onclick = function(event) {
            if (event.target == modal) {
                closeProfileModal();
            }
        }

        // Fungsi untuk menampilkan preview gambar
        if (profileInput) {
            profileInput.addEventListener('change', function(event) {
                if (event.target.files && event.target.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        previewPP.src = e.target.result;
                    }

                    reader.readAsDataURL(event.target.files[0]);
                } else {
                    // Jika file dibatalkan/dihapus, kembalikan ke foto saat ini
                    previewPP.src = currentPhoto;
                }
            });
        }
    </script>
</body>

</html>