<?php require 'conn.php';
require_once 'function.php';
require_once 'polmed_function.php';
session_start();
if (isset($_SESSION['id_user']) && detail_user_exist($conn, trim($_SESSION['id_user']))) {
    $resultData = detail_user($conn, trim($_SESSION['id_user']));
    $row1 = mysqli_fetch_assoc($resultData);
    $foto = !empty($row1['foto_profile']) ? $row1['foto_profile'] : 'GAMBAR/image.png';
    $nim = $_SESSION['id_user'];
}

if (!isset($_GET['id_berita'])) {
    header('Location: index.php');
    exit();
}

$dummyImages = [
    'GAMBAR/membaca.jpg',
    'GAMBAR/wisudawa.jpg',
    'GAMBAR/bacako.jpg',
    'GAMBAR/SATU.png',
    'GAMBAR/DUA.jpg',
    'GAMBAR/tiga.jpg',
    'GAMBAR/empat.jpg',
    'GAMBAR/lima.jpg',
    'GAMBAR/tujuh.jpg',
    'GAMBAR/delapan.jpg',
    'GAMBAR/sembilan.jpg'
];
$row_berita = detail_berita_with_author($conn, $_GET['id_berita'])->fetch_assoc();
$gambar = !empty($row_berita['gambar_berita']) ? $row_berita['gambar_berita'] : $dummyImages[array_rand($dummyImages)];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Perpustakaan POLMED</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="profile_modal.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            background: #fff;
            color: #000;
            line-height: 1.7;
            height: 100vh;
            /* ANIMASI SAAT HALAMAN DIBUKA */
            opacity: 0;
            transform: translateY(20px);
            animation: fadeSlide 1.2s ease forwards;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        /* CONTENT */
        .container.berita {
            width: 60%;
            margin: auto;
            margin-top: 35px;
        }

        .title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .meta {
            margin: 15px 0;
            color: #444;
            font-size: 14px;
        }

        .headline-img {
            width: 100%;
            border-radius: 12px;
            margin: 20px 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .content p {
            font-size: 17px;
            margin-bottom: 20px;
            text-align: justify;
        }

        /* TOMBOL KEMBALI */
        .btn-back {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 20px;
            background: #532bcc;
            color: #fff;
            border-radius: 10px;
            font-size: 15px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
        }

        .btn-back:hover {
            background: #3e20a1;
            transform: translateY(-2px);
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
                <input type="hidden" name="redirect_to" value="berita.php?id_berita=<?= $_GET['id_berita'] ?>">

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

    <!-- CONTENT -->
    <div class="container berita">
        <h1 class="title"><?= $row_berita['judul_berita'] ?></h1>

        <img src="<?= $gambar ?>" class="headline-img">

        <div class="meta">
            <span>Penulis: <b><?= $row_berita['nama'] ?></b></span><br>
            <span>Tanggal: <?= date('d F Y', strtotime($row_berita['tanggal_publish'])) ?></span>
        </div>

        <div class="content">
            <p>
                <?= $row_berita['isi_berita'] ?>
            </p>
        </div>

        <a href="index.php" class="btn-back">← Kembali</a>
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