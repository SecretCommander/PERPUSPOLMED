<?php
require_once 'conn.php';
require_once 'polmed_function.php';
require_once 'function.php';
session_start();


if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}


$nim = $_SESSION['id_user'];
$user_data = detail_user($conn, $nim);
$row1 = mysqli_fetch_assoc($user_data);


$komentarUser = list_komentar_user($conn, $nim); // Menggunakan nama variabel yang lebih sesuai
$user_nama = $row1['nama'] ?? 'Pengguna';

$dummyImages = [
    'GAMBAR/membaca.jpg',
    'GAMBAR/BUKU REKOMEN/ATOMIC HABITS.jpg',
    'GAMBAR/BUKU REKOMEN/BUMII MANUSIA.jpg',
    'GAMBAR/BUKU REKOMEN/LAUT BERCERITA.jpg',
    'GAMBAR/BUKU REKOMEN/PERPUSTAKAAN TENGAH MALAM.jpg',
];
// Tentukan path foto saat ini (untuk preview dan reset)
$foto = !empty($row1['foto_profile']) ? $row1['foto_profile'] : 'GAMBAR/image.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Komentar <?= $user_nama ?></title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="profile_modal.css">
    <style>
        body {
            background: #6c3cff;
            font-family: poppins;
            animation: fadeIn 0.5s ease-in forwards;
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
                <input type="hidden" name="redirect_to" value="komentarsaya.php">

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

    <h2 class="section-title">Riwayat Komentar Saya</h2>

    <div class="komentar-list">
        <?php
        if ($komentarUser && mysqli_num_rows($komentarUser) > 0) {
            while ($komentar = mysqli_fetch_assoc($komentarUser)) {
                $sampul_buku = !empty($komentar['sampul']) ? $komentar['sampul'] : $dummyImages[array_rand($dummyImages)];
                $judul_buku = $komentar['judul'] ?? 'Judul Tidak Tersedia';
                $tgl_komentar = date('d F Y', strtotime($komentar['tgl_komentar']));
        ?>
                <div class="komentar-card">
                    <div class="book-info">
                        <img src="<?= $sampul_buku ?>" alt="Sampul Buku" title="<?= htmlspecialchars($judul_buku) ?>">
                        <a href="buku.php?id_buku=<?= $komentar['buku_id'] ?>"><?= $judul_buku ?></a>
                    </div>

                    <div class="komentar-content">
                        <div class="komentar-header">
                            <span class="komentar-date">Dikirim pada: **<?= $tgl_komentar ?>**</span>
                            <a href="API/hapus_komentar.php?id_komentar=<?= $komentar['komentar_id'] ?>&redirect_to=komentarsaya.php"
                                class="action-link"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </div>

                        <div class="komentar-text">
                            <?= htmlspecialchars($komentar['isi_komentar']) ?>
                        </div>

                    </div>
                </div>
            <?php
            }
        } else {
            ?>
            <div class="no-data">
                <p>Anda belum pernah memberikan komentar pada buku manapun.</p>
                <div style="margin-top: 20px;">
                    <a href="index.php#bukurekomen" class="cta-btn" style="text-decoration: none;">Lihat Buku Rekomendasi</a>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <div style="text-align: center; margin: 30px 0 50px;">
        <a href="index.php" class="cta-btn" style="background: #ccc; color: #000;; text-decoration: none;">Kembali ke Beranda</a>
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