<?php
include '../conn.php';
include '../function.php';
require_once '../polmed_function.php';
check_petugas();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_POST['action'] == 'add') {
    $result = upload_berita($conn);
  } elseif ($_POST['action'] == 'update') { {
      $result = update_berita($conn, $_POST['berita_id']);
    }
  }
}
// Data Petugas
$dataPetugas = detail_petugas($conn, $_SESSION['petugas_id'])->fetch_assoc();

// Query Semua Berita (Looping)
$queryBerita = news($conn);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin - Berita</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body class="dashboard-page">
  <div class="sidebar">
    <div class="sidebar-header">Dashboard Admin</div>

    <div class="admin-profile">
      <div class="admin-info">
        <div class="admin-name"><?= $dataPetugas['nama']; ?></div>
        <div class="admin-username"><?= $dataPetugas['username'] ?></div>
      </div>
    </div>

    <ul class="sidebar-menu">
      <li><a href="dashboardkunjungan.php">Kunjungan</a></li>
      <li class="active"><a href="dashboardberita.php">Berita</a></li>
      <li><a href="dashpeminjaman.php">Peminjaman</a></li>
      <li><a href="dashboardbuku.php">Buku</a></li>
    </ul>

    <div class="sidebar-footer">
      <a href="logout.php" class="btn-logout">Logout</a>
    </div>
  </div>

  <div class="main-content">
    <div class="content-container">
      <div class="header-actions">
        <h1>Manajemen Berita</h1>
        <div class="add-button" id="addNewsButton"></div>
      </div>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Gambar</th>
              <th>Judul</th>
              <th class="col-date">Tgl Berita</th>
              <th>Isi Berita</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            // LOOPING DATA BERITA
            if (mysqli_num_rows($queryBerita) > 0) {
              while ($row = mysqli_fetch_assoc($queryBerita)) :
                $imgSrc = !empty($row['gambar_berita']) ? "../" . $row['gambar_berita'] : "../GAMBAR/default.jpg";
            ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td class="centered"><a href="../berita.php?id_berita=<?= $row['berita_id'] ?>" target="_blank">
                      <img src="<?= $imgSrc ?>" alt="Gambar" class="img-standard img-news" />
                    </a>
                  </td>
                  <td><a href="../berita.php?id_berita=<?= $row['berita_id']?>" target="_blank" style="color: #000; text-decoration: none;"><?= htmlspecialchars($row['judul_berita']) ?></a></td>
                  <td class="col-date"><?= date('d-m-Y', strtotime($row['tanggal_publish'])) ?></td>
                  <td class="news-content">
                    <?= htmlspecialchars(substr($row['isi_berita'], 0, 150)) ?>...
                  </td>
                  <td>
                    <div class="action-buttons">
                      <!-- TOMBOL UPDATE (Memicu Modal via AJAX) -->
                      <button class="btn btn-update" onclick="editNews(<?= $row['berita_id'] ?>)">
                        Update
                      </button>

                      <!-- TOMBOL DELETE (Link GET Biasa) -->
                      <a href="deleteberita.php?berita_id=<?= $row['berita_id'] ?>"
                        class="btn btn-delete"
                        onclick="return confirm('Yakin ingin menghapus berita ini?');">
                        Delete
                      </a>
                    </div>
                  </td>
                </tr>
            <?php
              endwhile;
            } else {
              echo "<tr><td colspan='6' style='text-align:center;'>Belum ada berita.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal for Add/Update -->
  <div class="modal-overlay" id="newsModal">
    <div class="modal">
      <div class="modal-header">
        <div class="modal-title" id="modalTitle">Tambah Berita</div>
        <button class="close-modal" id="closeModal">&times;</button>
      </div>

      <!-- Form submits normally to process_berita.php -->
      <form method="POST" enctype="multipart/form-data" id="newsForm">
        <input type="hidden" name="petugas_id" id="petugasId" value=<?= $_SESSION['petugas_id'] ?>>
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="berita_id" id="beritaId" value="">

        <div class="form-group">
          <label class="form-label">Preview Gambar</label>
          <img id="previewImage" src="" class="img-preview" style="display:none; width: 100px; height: 100px; object-fit: cover;">
        </div>

        <div class="form-group">
          <label class="form-label" for="newsImage">Input Gambar</label>
          <input type="file" id="newsImage" name="gambar" class="form-file" accept="image/*" />
        </div>

        <div class="form-group">
          <label class="form-label" for="newsTitle">Judul Berita</label>
          <input type="text" id="newsTitle" name="judul" class="form-input" placeholder="Masukkan judul" required />
        </div>

        <div class="form-group">
          <label class="form-label" for="newsDescription">Isi Berita</label>
          <textarea id="newsDescription" name="isi" class="form-textarea" placeholder="Masukkan isi berita" required></textarea>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn-cancel" id="cancelButton">Batal</button>
          <button type="submit" class="btn-save">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const modal = document.getElementById("newsModal");
    const form = document.getElementById("newsForm");
    const modalTitle = document.getElementById("modalTitle");
    const previewImg = document.getElementById("previewImage");
    const actionInput = document.getElementById("formAction");
    const idInput = document.getElementById("beritaId");
    const titleInput = document.getElementById("newsTitle");
    const descInput = document.getElementById("newsDescription");

    // --- 1. HANDLE ADD BUTTON ---
    document.getElementById("addNewsButton").addEventListener("click", function() {
      form.reset();
      actionInput.value = "add";
      idInput.value = "";
      modalTitle.innerText = "Tambah Berita";
      previewImg.style.display = "none";
      previewImg.src = "";
      modal.style.display = "flex";
    });

    // --- 2. HANDLE EDIT (AJAX RETRIEVE) ---
    function editNews(id) {
      // Reset form UI first
      modalTitle.innerText = "Loading...";
      modal.style.display = "flex";

      // AJAX Fetch Data
      fetch('../API/berita.php?id=' + id)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Populate Form
            const berita = data.data;
            idInput.value = berita.berita_id;
            actionInput.value = "update";
            titleInput.value = berita.judul_berita;
            descInput.value = berita.isi_berita;
            modalTitle.innerText = "Edit Berita";

            // Handle Image Preview
            if (berita.gambar_berita) {
              previewImg.src = "../GAMBAR/" + berita.gambar_berita;
              previewImg.style.display = "block";
            } else {
              previewImg.style.display = "none";
            }
          } else {
            alert("Gagal mengambil data berita.");
            modal.style.display = "none";
          }
        })
        .catch(err => {
          console.error(err);
          alert("Terjadi kesalahan koneksi.");
          modal.style.display = "none";
        });
    }

    // --- 3. CLOSE MODAL LOGIC ---
    function closeModalFunc() {
      modal.style.display = "none";
    }
    document.getElementById("closeModal").addEventListener("click", closeModalFunc);
    document.getElementById("cancelButton").addEventListener("click", closeModalFunc);
    window.onclick = function(event) {
      if (event.target == modal) {
        closeModalFunc();
      }
    }

    // --- 4. IMAGE PREVIEW ON FILE SELECT ---
    document.getElementById("newsImage").addEventListener("change", function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          previewImg.src = e.target.result;
          previewImg.style.display = "block";
        }
        reader.readAsDataURL(file);
      }
    });
  </script>
</body>

</html>