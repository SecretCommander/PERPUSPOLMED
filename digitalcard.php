<?php require 'conn.php';
require_once 'function.php';
require_once 'polmed_function.php';
session_start();
if (isset($_SESSION['id_user']) && detail_user_exist($conn, trim($_SESSION['id_user']))) {
    $resultData = detail_user($conn, trim($_SESSION['id_user']));
    $row1 = mysqli_fetch_assoc($resultData);
} else {
    header('Location: login.php');
    exit();
}
$nim = $_SESSION['id_user'];
$foto = !empty($row1['foto_profile']) ? $row1['foto_profile'] : 'GAMBAR/image.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Digital Perpustakaan</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="profile_modal.css">
</head>
<style>
    /* Override navbar styles for this page only */
    body {
        background: #6c3cff;
        font-family: poppins;
        animation: fadeIn 0.5s ease-in forwards;
    }

    nav .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 10px 20px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Digital Card Styles */
    .card-container {
        min-height: calc(100vh - 100px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .card-title {
        text-align: center;
        font-family: poppins;
        color: white;
        margin: 30px 0;
        font-size: 28px;
        font-weight: 700;
    }

    .digital-card {
        background: white;
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }

    /* TOP AREA: LOGO + INFO + FOTO */
    .top-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .logo-area {
        display: flex;
        gap: 10px;
    }

    .logo-digital {
        width: 60px;
        height: 60px;
    }

    .logo-area h4 {
        font-size: 12px;
        margin: 0;
        font-weight: bold;
    }

    .logo-area p {
        font-size: 11px;
        margin: 0;
        color: #444;
    }

    .sub-title {
        font-size: 12px;
        font-weight: bold;
        margin-top: 3px;
    }

    .photo {
        width: 80px;
        height: 95px;
        border-radius: 6px;
        object-fit: cover;
        background: #ddd;
    }

    /* NAME */
    .user-name {
        margin-top: 20px;
        font-size: 18px;
        text-align: left;
    }

    /* ID + EXP */
    .info-section {
        margin-top: 15px;
        display: flex;
        justify-content: space-between;
    }

    .label {
        font-size: 12px;
        color: #555;
    }

    .value {
        font-size: 14px;
        font-weight: bold;
        margin-top: 3px;
    }

    /* BARCODE */
    .barcode {
        margin-top: 20px;
        text-align: right;
    }

    .barcode img {
        width: auto;
        height: 60px;
        max-width: 80%;
        margin-left: auto;
    }

    /* Back Button */
    .back-btn {
        display: inline-block;
        background: white;
        color: #6c3cff;
        padding: 10px 20px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        margin-top: 20px;
        transition: 0.3s;
    }

    .back-btn:hover {
        background: #f0f0f0;
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-title {
            font-size: 22px;
        }

        .digital-card {
            padding: 15px;
        }

        .photo {
            width: 70px;
            height: 85px;
        }

        .logo-digital {
            width: 50px;
            height: 50px;
        }
    }
</style>

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
                <input type="hidden" name="redirect_to" value="digitalcard.php">

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
    <div class="card-container">
        <h1 class="card-title">KARTU DIGITAL PERPUSTAKAAN</h1>

        <div class="digital-card">
            <div class="top-row">
                <div class="logo-area">
                    <img src="GAMBAR/POLMED.png" class="logo-digital">
                    <div>
                        <h4>PERPUSTAKAAN POLMED</h4>
                        <p>NPP. 1261162C2000002</p>
                        <span class="sub-title">KARTU ANGGOTA</span>
                    </div>
                </div>

                <?php
                $foto_card = !empty($row1['foto_profil']) ? $row1['foto_profil'] : 'GAMBAR/YABES.jpg';
                ?>
                <img src="<?php echo $foto_card; ?>" class="photo">
            </div>

            <h2 class="user-name"><?= htmlspecialchars($row1['nama']) ?></h2>

            <div class="info-section">
                <div>
                    <p class="label">ID Anggota</p>
                    <p class="value"><?= htmlspecialchars($row1['nim']) ?></p>
                </div>
                <div>
                    <p class="label">Program Studi</p>
                    <p class="value"><?= htmlspecialchars($row1['nama_prodi']) ?></p>
                </div>
            </div>

            <div class="barcode">
                <img src="https://barcode.tec-it.com/barcode.ashx?data=<?= urlencode($row1['nim']) ?>&code=Code128&translate-esc=on" alt="Barcode">
            </div>
        </div>

        <a href="index.php" class="back-btn">← Kembali ke Beranda</a>
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