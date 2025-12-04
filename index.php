<!DOCTYPE html>
<html lang="en">

<head>
  <?php require 'conn.php';
  require_once 'function.php';
  require_once 'polmed_function.php';
  session_start();
  if (isset($_SESSION['id_user']) && detail_user_exist($conn, trim($_SESSION['id_user']))) {
    $resultData = detail_user($conn, trim($_SESSION['id_user']));
    $row1 = mysqli_fetch_assoc($resultData);
    $nim = $_SESSION['id_user'];
  }
  $foto = !empty($row1['foto_profile']) ? $row1['foto_profile'] : 'GAMBAR/image.png';
  ?>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Library Polmed</title>

  <link rel="stylesheet" href="style1.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="profile_modal.css">
</head>

<body>
  <nav>
    <div class="container">
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
        <input type="hidden" name="redirect_to" value="index.php">

        <img src="<?= $foto ?>" alt="Preview Profil" id="preview-pp">

        <div class="upload-btn-wrapper">
          <button type="button" class="btn-upload">Pilih Foto Baru</button>
          <input type="file" name="new_profile_pic" id="profile-input" accept="image/*" required>
        </div>

        <button type="submit">Simpan Perubahan</button>
      </form>
    </div>
  </div>


  <div class="layout">
    <section id="kiri">
      <div class="berita">
        <h1>News</h1>
        <hr>

        <div class="news-scroll-wrapper">
          <?php
          // dynamic berita loader
          require_once 'function.php';

          // dummy images (choose randomly for each item)
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
          $list_berita = news($conn);
          while ($berita = mysqli_fetch_assoc($list_berita)) {
            $gambar = !empty($berita['gambar_berita']) ? $berita['gambar_berita'] : $dummyImages[array_rand($dummyImages)];
          ?>

            <div class="berita1">
              <img src="<?php echo $gambar; ?>" alt="">
              <a href="berita.php?id_berita=<?php echo $berita['berita_id']; ?>">
                <h3><?php echo $berita['judul_berita']; ?></h3>
              </a>
              <p></p>
              <p><?php echo substr($berita['isi_berita'], 0, 200) . '...'; ?></p>
              <br>
              <hr>
            </div>
          <?php
          }
          ?>
        </div>

      </div>
    </section>

    <div class="kanan">

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



      <section id="hero">
        <div class="hero">
          <div class="headline">
            <h1>Ayo Jadi Pengunjung <br><span>Paling Aktif! </span></h1>
            <p>Kunjungi perpustakaan lebih sering dan dapatkan <br>posisi terbaik di leaderboard.</p>
            <a href="#bukurekomen" class="cta-btn">Lihat Buku Rekomendasi</a>
          </div>
          <img src="GAMBAR/hero.png" alt="">
        </div>

      </section>



      <section id="bukurekomen">
        <div class="h1buku">
          <h1>Buku Masuk Terbaru</h1>
        </div>

        <?php
        require_once 'function.php';

        // images array (choose randomly for each item)
        $dummyImages = [
          'GAMBAR/membaca.jpg',
          'GAMBAR/BUKU REKOMEN/ATOMIC HABITS.jpg',
          'GAMBAR/BUKU REKOMEN/BUMII MANUSIA.jpg',
          'GAMBAR/BUKU REKOMEN/LAUT BERCERITA.jpg',
          'GAMBAR/BUKU REKOMEN/PERPUSTAKAAN TENGAH MALAM.jpg',
        ];

        // try to use a helper function if it exists, otherwise fallback to a simple query
        if (function_exists('newest_book')) {
          $res = newest_book($conn); // expected to return mysqli_result
        }

        echo '<div class="book-grid">';

        if ($res && mysqli_num_rows($res) > 0) {
          while ($buku = mysqli_fetch_assoc($res)) {
            // adapt these keys to your actual column names
            // pick a random image from the array for every card
            $cover = isset($buku['sampul']) ? $buku['sampul'] : $dummyImages[array_rand($dummyImages)];
            // If you prefer to use DB cover when available, replace previous line with:
            // $cover = !empty($buku['cover']) ? $buku['cover'] : $dummyImages[array_rand($dummyImages)];

            $pengarang  = !empty($buku['pengarang']) ? htmlspecialchars($buku['pengarang']) : 'LibraryPolmed';
            $title  = htmlspecialchars($buku['judul'] ?? $buku['judul'] ?? 'Untitled');

            echo '<div class="book-card">';
            echo '<a href="buku.php?id_buku=' . $buku['buku_id'] . '"><img src="' . htmlspecialchars($cover) . '" alt="' . $title . '"></a>';
            echo '<p class="brand">' . $pengarang . '</p>';
            echo '<h3 class="book-title"><a href="buku.php?id_buku=' . $buku['buku_id'] . '" style="color: #333; text-decoration: none;">' . $title . '</a></h3>';
            echo '<div class="book-actions">';
            // echo '<span class="action-icon">❤️</span>';
            echo '<span class="action-icon">💬</span>';
            echo '</div>';
            echo '</div>';
          }
        } else {
          // fallback: show a few static placeholders when no data
          for ($i = 0; $i < 4; $i++) {
            $randImg = $dummyImages[array_rand($dummyImages)];
            echo '<div class="book-card">';
            echo '<div class="rating-badge">⭐ 4.' . rand(0, 9) . '</div>';
            echo '<img src="' . htmlspecialchars($randImg) . '" alt="Placeholder">';
            echo '<p class="brand">LibraryPolmed</p>';
            echo '<h3 class="book-title">Buku Rekomendasi</h3>';
            echo '<div class="book-actions"><span class="action-icon">❤️</span><span class="action-icon">💬</span></div>';
            echo '</div>';
          }
        }

        echo '</div>';
        ?>
      </section>

      <section id="leaderboard">
        <h1 class="leader-title">Leaderboard Streak</h1>

        <div class="leaderboard-container">

          <?php
          require_once 'function.php';

          $res = top_streak($conn);

          // collect all users first
          $users = [];
          if ($res && mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
              $users[] = $row;
            }
          }

          // if empty, show fallback
          if (count($users) === 0) {
            echo '<p>No leaders yet.</p>';
          } else {
            // build display order so that:
            // - when at least 3 users exist, show 2nd, 1st, 3rd (then the rest)
            // - otherwise keep natural order
            $ordered = [];
            if (count($users) >= 3) {
              $indices = [1, 0, 2]; // output order: 2nd, 1st, 3rd
              foreach ($indices as $idx) {
                if (isset($users[$idx])) {
                  $ordered[] = ['data' => $users[$idx], 'rank' => $idx + 1];
                }
              }
              for ($i = 3; $i < count($users); $i++) {
                $ordered[] = ['data' => $users[$i], 'rank' => $i + 1];
              }
            } else {
              foreach ($users as $i => $u) {
                $ordered[] = ['data' => $u, 'rank' => $i + 1];
              }
            }

            $badges = ['gold', 'silver', 'bronze'];
            $positions = ['first', 'second', 'third'];

            // render cards using the original rank for badge/position classes
            foreach ($ordered as $item) {
              $origRank = $item['rank'];
              $user = $item['data'];

              $badge = $badges[$origRank - 1] ?? 'participant';
              $position = $positions[$origRank - 1] ?? 'participant';
              $name = htmlspecialchars($user['nama'] ?? 'Unknown');
              $prodi = htmlspecialchars($user['nama_prodi'] ?? 'Prodi');
              $streak = intval($user['poin_aktivitas'] ?? 0);

              echo '<div class="leader-card ' . $position . '">';
              echo '<div class="rank-badge ' . $badge . '">' . $origRank . '</div>';
              echo '<img src="GAMBAR/membaca.jpg" class="leader-photo" alt="' . $name . '">';
              echo '<h3 class="leader-name">' . $name . '</h3>';
              echo '<p class="leader-score">' . $prodi . '</p>';
              echo '<p class="leader-streak">🔥 ' . $streak . ' hari streak</p>';
              echo '</div>';
            }
          }
          ?>

        </div>
      </section>

      <section id="leaderboardprodi">
        <h1>Leaderboard Prodi</h1>
        <div class="board">
          <div class="header">
            <div>Rank</div>
            <div>Program Studi</div>
            <div>Jumlah Peminjaman</div>
          </div>

          <?php
          // try to reuse helper if available, otherwise run a local query
          if (function_exists('top_prodi')) {
            $res = top_prodi($conn);
          }

          if ($res && mysqli_num_rows($res) > 0) {
            $rank = 1;
            while ($row = mysqli_fetch_assoc($res)) {
              $rankStr = str_pad($rank, 2, '0', STR_PAD_LEFT);
              $nama_prodi = htmlspecialchars($row['nama_prodi'] ?? 'Unknown');
              $total = intval($row['total_peminjaman'] ?? 0);

              echo '<div class="row">';
              echo '<div class="rank">' . $rankStr . '</div>';
              echo '<div>' . $nama_prodi . '</div>';
              echo '<div class="center">' . $total . '</div>';
              echo '</div>';

              $rank++;
            }
          } else {
            // fallback static rows when no data
            $placeholders = [
              ['TEKNOLOGI REKAYASA PERANGKAT LUNAK', 120],
              ['TEKNOLOGI REKAYASA MULTIMEDIA GRAFIS', 110],
              ['TEKNOLOGI REKAYASA JARINGAN', 104],
              ['TEKNOLOGI REKAYASA ELEKTRONIKA', 98],
              ['TEKNIK KOMPUTER', 92],
            ];
            $rank = 1;
            foreach ($placeholders as $p) {
              $rankStr = str_pad($rank, 2, '0', STR_PAD_LEFT);
              echo '<div class="row">';
              echo '<div class="rank">' . $rankStr . '</div>';
              echo '<div>' . htmlspecialchars($p[0]) . '</div>';
              echo '<div>' . intval($p[1]) . '</div>';
              echo '</div>';
              $rank++;
            }
          }
          ?>
        </div>
      </section>


      <section id="testimoni">
        <h2 class="title-testimoni">Cerita Peminjaman</h2>

        <div class="testimonial-wrapper">
          <?php
          require_once 'polmed_function.php';
          $list_peminjaman = list_all_peminjaman_index($conn);
          $index = 1;

          // Dummy book covers
          $dummy_covers = [
            'GAMBAR/BUKU REKOMEN/BUMII MANUSIA.jpg',
            'GAMBAR/BUKU REKOMEN/ATOMIC HABITS.jpg',
            'GAMBAR/BUKU REKOMEN/PERPUSTAKAAN TENGAH MALAM.jpg',
            'GAMBAR/BUKU REKOMEN/LAUT BERCERITA.jpg'
          ];

          while ($peminjaman = mysqli_fetch_assoc($list_peminjaman)) {
            $pid = (int)$peminjaman['peminjaman_id'];
            $is_even = (ceil($index % 2) == 0);
            $foto_profil = !empty($peminjaman['foto_profil']) ? 'GAMBAR/yabes.jpg' : 'GAMBAR/yabes.jpg';
            $sampul_buku = !empty($peminjaman['sampul']) ? $peminjaman['sampul'] : $dummy_covers[array_rand($dummy_covers)];
            $testimoni_text = get_random_testimoni_template(
              $peminjaman['nama'],
              $peminjaman['nama_prodi'],
              $peminjaman['judul']
            );

            $total_likes = count_likes_peminjaman($conn, $pid);

            $user_has_liked = false;
            if (isset($_SESSION['id_user'])) {
              $user_has_liked = has_liked_peminjaman($conn, $pid, trim($_SESSION['id_user']));
            }

            $btn_class = $user_has_liked ? 'liked' : '';
            $heart_style = $user_has_liked ? 'color: red;' : 'color: #ccc;';
            $index++;
          ?>

            <!-- Testimonial Card -->
            <div class="testimonial small <?php echo $is_even ? 'even' : 'odd'; ?>" data-id="<?php echo $pid; ?>">

              <div class="profilecover">
                <img src="<?php echo $foto_profil; ?>" alt="Foto" class="testi-img-round">
                <img src="<?php echo $sampul_buku; ?>" alt="Sampul Buku" class="book-cover-testi">
              </div>

              <div class="testi-card-small">
                <h3><?php echo htmlspecialchars($peminjaman['nama']); ?></h3>
                <p class="job-small">Mahasiswa (<?php echo htmlspecialchars($peminjaman['nama_prodi']); ?>)</p>
                <p class="text-small"><?php echo htmlspecialchars($testimoni_text); ?></p>

                <div class="testi-actions">
                  <button class="like-btn <?php echo $btn_class; ?>"
                    id="btn-like-<?php echo $pid; ?>"
                    onclick="likeTestimoni(<?php echo $pid; ?>)">

                    <span class="heart-icon" style="<?php echo $user_has_liked ? 'color: red;' : ''; ?>">
                      <?php echo $user_has_liked ? '❤️' : '🤍'; ?>
                    </span>

                    <span class="like-count" id="count-<?php echo $pid; ?>">
                      <?php echo $total_likes; ?>
                    </span>
                  </button>
                </div>
              </div>
            </div>

          <?php } ?>
        </div>
      </section>


      <section id="mitra">
        <h2 class="mitra-title">Informasi Mitra</h2>

        <div class="mitra-wrapper">

          <!-- Kiri: Logo -->
          <div class="mitra-left">
            <img src="GAMBAR/POLMED.png" alt="Logo Mitra" class="logo-mitra">
          </div>

          <!-- Kanan: Detail Informasi -->
          <div class="mitra-right">
            <h3 class="mitra-name">Perpustakaan Politeknik Negeri Medan</h3>
            <p class="mitra-desc">
              Kami adalah Perpustakaan Politeknik Negeri Medan yang berkomitmen menyediakan sumber daya informasi berkualitas untuk mendukung proses belajar mengajar dan penelitian di lingkungan kampus.
            </p>

            <div class="mitra-info">
              <p><strong>Alamat:</strong> Jl. Almamater No. 1 , Gedung P Politeknik Negeri Medan, Kampus USU, Pd. Bulan, Medan</p>
              <p><strong>Email:</strong> library@polmed.ac.id</p>
              <p><strong>No. Telp:</strong> +62 61 8211235</p>
            </div>
          </div>

        </div>
      </section>


    </div>
  </div>

  <footer id="footer">
    <div class="footer-container">

      <div class="footer-title">
        <h2>Kelompok YAREKFI</h2>
        <p>Pembuat Website</p>
      </div>

      <div class="footer-members">
        <h3>Anggota Kelompok:</h3>
        <ul>
          <li>• Farrel Ardan Awali</li>
          <li>• Yabes Raja Iman Manalu</li>
          <li>• Daffi Armansyah</li>
        </ul>
      </div>

    </div>

    <div class="footer-bottom">
      <p>© 2025 Kelompok Kreativa Digital — All Rights Reserved.</p>
    </div>
  </footer>

  <script>
    /* ================================
       ANIMASI BUKU REKOMENDASI
    ================================ */
    const section = document.querySelector("#bukurekomen");
    const cards = document.querySelectorAll(".book-card");

    if (section) { // Cek apakah section ada
      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            section.classList.add("show");
            cards.forEach((card, index) => {
              setTimeout(() => {
                card.classList.add("show");
              }, index * 200);
            });
          }
        });
      }, {
        threshold: 0.3
      });
      observer.observe(section);
    }

    // Smooth scroll (Cek apakah link ada)
    const scrollLink = document.querySelector('a[href="#bukurekomen"]');
    if (scrollLink && section) {
      scrollLink.addEventListener("click", function(e) {
        e.preventDefault();
        section.scrollIntoView({
          behavior: "smooth"
        });
      });
    }


    /* ================================
       ANIMASI LEADERBOARD (STREAK)
    ================================ */
    const leaderboard = document.querySelector("#leaderboard");
    const leaderCards = document.querySelectorAll("#leaderboard .leader-card");

    if (leaderboard) {
      const leaderObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            leaderboard.classList.add("show");
            leaderCards.forEach((card, i) => {
              setTimeout(() => {
                card.classList.add("show");
              }, i * 200);
            });
          }
        });
      }, {
        threshold: 0.3
      });
      leaderObserver.observe(leaderboard);
    }


    /* ================================
       SIDEBAR 
    ================================ */
    const toggleBtn = document.getElementById("toggleBtn");
    const sidebar = document.getElementById("sidebar");

    // HANYA JALANKAN EVENT LISTENER JIKA TOMBOL ADA (SUDAH LOGIN)
    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("active");
      });
    }


    /* ================================
       ANIMASI LEADERBOARD PRODI
    ================================ */
    const leaderboardProdi = document.querySelector("#leaderboardprodi");

    if (leaderboardProdi) {
      const lbProdiObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            // Menambahkan class .show agar opacity CSS berubah jadi 1
            leaderboardProdi.classList.add("show");
          }
        });
      }, {
        threshold: 0.2
      });
      lbProdiObserver.observe(leaderboardProdi);
    }


    /* ================================
       ANIMASI TESTIMONI
    ================================ */
    const testimoni = document.querySelector("#testimoni");
    if (testimoni) {
      const testimoniObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            testimoni.classList.add("show");
          }
        });
      }, {
        threshold: 0.2
      });
      testimoniObserver.observe(testimoni);
    }


    /* ================================
       ANIMASI MITRA
    ================================ */
    const mitra = document.querySelector("#mitra");
    if (mitra) {
      const mitraObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            mitra.classList.add("show");
          }
        });
      }, {
        threshold: 0.2
      });
      mitraObserver.observe(mitra);
    }

    function likeTestimoni(peminjamanId) {
      // 1. Siapkan Data
      const data = {
        id: peminjamanId
      };

      // 2. Kirim ke PHP Handler (ajax_like.php)
      fetch('API/like.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
          if (result.success) {
            // 3. Update Tampilan (DOM) jika sukses
            const btn = document.getElementById('btn-like-' + peminjamanId);
            const countSpan = document.getElementById('count-' + peminjamanId);
            const heartIcon = btn.querySelector('.heart-icon');

            // Update Angka
            countSpan.innerText = result.new_count;

            // Update Warna/Icon
            if (result.action === 'liked') {
              btn.classList.add('liked');
              heartIcon.innerHTML = '❤️'; // Ubah icon jadi merah/isi
              heartIcon.style.color = 'red';
            } else {
              btn.classList.remove('liked');
              heartIcon.innerHTML = '🤍'; // Ubah icon jadi putih/kosong
              heartIcon.style.color = '';
            }

            // Opsional: Animasi kecil
            btn.style.transform = "scale(1.2)";
            setTimeout(() => btn.style.transform = "scale(1)", 200);

          } else {
            // Jika gagal (misal belum login)
            alert(result.message);
            if (result.message.includes('login')) {
              alert('Silahkan login terlebih dahulu untuk menyukai cerita ini.');
            }
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }
  </script>


  <script>
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