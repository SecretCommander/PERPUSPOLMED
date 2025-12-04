<?php require 'conn.php';
require_once 'function.php';
require_once 'polmed_function.php';
session_start();
if (isset($_SESSION['id_user']) && detail_user_exist($conn, trim($_SESSION['id_user']))) {
  $resultData = detail_user($conn, trim($_SESSION['id_user']));
  $row1 = mysqli_fetch_assoc($resultData);
  $foto = !empty($row1['foto_profile']) ? $row1['foto_profile'] : 'GAMBAR/image.png';
}

if (!isset($_GET['id_buku'])) {
  header('Location: index.php');
  exit();
} else {
  $id_buku = trim($_GET['id_buku']);
  $row = detail_book($conn, $id_buku)->fetch_assoc();
  $komentar = list_komentar_by_buku($conn, $id_buku);
  $isKomen = false;
  $nim_user_saat_ini = isset($row1['nim']) ? $row1['nim'] : null;
  $nim = $_SESSION['id_user'];

  $dummyImages = [
    'GAMBAR/membaca.jpg',
    'GAMBAR/BUKU REKOMEN/ATOMIC HABITS.jpg',
    'GAMBAR/BUKU REKOMEN/BUMII MANUSIA.jpg',
    'GAMBAR/BUKU REKOMEN/LAUT BERCERITA.jpg',
    'GAMBAR/BUKU REKOMEN/PERPUSTAKAAN TENGAH MALAM.jpg',
  ];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Perpustakaan POLMED - Buku Rekomendasi</title>
  <link rel="stylesheet" href="styledetail.css">
  <link rel="stylesheet" href="style1.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="profile_modal.css">
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
        <input type="hidden" name="redirect_to" value="buku.php?id_buku=<?= $_GET['id_buku'] ?>">

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

  <div class="section-title">DETAIL BUKU</div>

  <!-- Atomic Habits -->
  <div class="container card-book-detail">
    <div class="book-header">
      <div class="left"><img src="<?= $row['sampul'] != null ? $row['sampul'] : $dummyImages[array_rand($dummyImages)] ?>" class="book-img"></div>
      <div class="right">
        <h2><?= $row['judul'] ?></h2>
        <div class="rating"><?= $row['kategori'] ?></div>
        <table>
          <tr>
            <th>Penulis</th>
            <td><?= $row['pengarang'] ?></td>
          </tr>
          <tr>
            <th>Tahun Terbit</th>
            <td><?= $row['tahun_terbit'] ?></td>
          </tr>
          <tr>
            <th>Jumlah Eksemplar di Perpus</th>
            <td><?= $row['jumlah_eksemplar'] ?></td>
          </tr>
        </table>
      </div>
    </div>

    <div class="deskripsi"><?= $row['deskripsi_buku'] ?></div>

    <div class="comment-section" id="comments-atomic">
      <h3>Komentar Pengguna</h3>
      <?php while ($kom = $komentar->fetch_assoc()) {
        if ($nim_user_saat_ini == $kom['nim']) {
          // Set isKomen ke true jika komentar milik user saat ini ditemukan
          $isKomen = true;
        }
      ?>
        <div class="komentar-box">
          <div class="komentar-nama"><?= $kom['nama'] . " • " . date('d-m-Y', strtotime($kom['tgl_komentar'])) ?></div>
          <div class="komentar-teks"><?= $kom['isi_komentar'] ?></div>
          <?php if ($nim_user_saat_ini == $kom['nim']) {
            echo '<button class="delete-komen" onclick="if(confirm(\'Apakah Anda yakin ingin menghapus komentar ini?\')) { window.location.href=\'API/hapus_komentar.php?id_komentar=' . $kom['komentar_id'] . '&id_buku=' . $id_buku . '\'; }">Hapus</button>';
          } ?>
        </div>
      <?php } ?>

      <div class="add-comment">
        <?php if (!isset($row1['nim'])) {
          echo '<small style="color: red;">Silakan login untuk memberikan komentar.</small>';
        } elseif ($isKomen) {
          echo '<small style="color: red;">Anda sudah memberikan komentar pada buku ini.</small>';
        } else { ?>
          <form action="API/tambah_komentar.php" method="POST">
            <input type="hidden" name="nim" value="<?= $row1['nim'] ?>">
            <input type="hidden" name="buku_id" value="<?= $id_buku ?>">

            <p>Mengirim sebagai: <strong><?= $row1['nama'] ?></strong></p>

            <textarea class="komentar" style="margin-top: 12px;" rows="3" name="isi_komentar" placeholder="Tulis komentar Anda..." required></textarea>
            <button type="submit">Kirim Komentar</button>
          </form>
        <?php } ?>
        <a href="index.php" class="btn-kembali">Kembali</a>
      </div>
    </div>
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