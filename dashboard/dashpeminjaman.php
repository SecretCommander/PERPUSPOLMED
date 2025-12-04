<?php
include '../conn.php';
include '../function.php';
require_once '../polmed_function.php';
check_petugas();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'];

  if ($action === 'add') {
    $bookInput = $_POST['book_name'];
    $userInput = $_POST['user_nim'];

    $bookId = explode(' - ', $bookInput)[0];
    $userNim = explode(' - ', $userInput)[0];
    insert_peminjaman($conn, $bookId, $userNim);

    echo "<script>alert('Peminjaman berhasil ditambahkan!'); window.location.href='dashpeminjaman.php';</script>";
  } elseif ($action === 'update') {
    $loanId = $_POST['loan_id'];
    $status = $_POST['loanStatus'];

    if ($status === 'kembali') {
      return_peminjaman($conn, $loanId);
    }

    echo "<script>alert('Status berhasil diperbarui!'); window.location.href='dashpeminjaman.php';</script>";
    exit;
  }
}

$dataPetugas = detail_petugas($conn, $_SESSION['petugas_id'])->fetch_assoc();
$queryPeminjaman = list_all_peminjaman($conn);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin - Peminjaman</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    input:disabled {
      background-color: #f0f0f0;
      color: #888;
      cursor: not-allowed;
    }
  </style>
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
      <li><a href="dashboardberita.php">Berita</a></li>
      <li class="active"><a href="dashpeminjaman.php">Peminjaman</a></li>
      <li><a href="dashboardbuku.php">Buku</a></li>
    </ul>

    <div class="sidebar-footer">
      <a href="logout.php" class="btn-logout">Logout</a>
    </div>
  </div>

  <div class="main-content">
    <div class="content-container">
      <div class="header-actions">
        <h1>Manajemen Peminjaman</h1>
        <div class="add-button" id="addLoanButton"></div>
      </div>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Buku</th>
              <th>Foto Sampul</th>
              <th>NIM</th>
              <th>Nama</th>
              <th>Tgl Pinjam</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($queryPeminjaman)) : ?>
              <?php
              $statusClass = ($row['status'] == 'kembali') ? 'badge-success' : 'badge-danger';
              $statusLabel = ($row['status'] == 'kembali') ? 'Sudah Kembali' : 'Masih Dipinjam';
              $imgSrc = !empty($row['sampul']) ? $row['sampul'] : '../GAMBAR/BUKU REKOMEN/BUMII MANUSIA.jpg';
              ?>
              <tr data-id="<?= $row['peminjaman_id'] ?>">
                <td class="book-name"><?= htmlspecialchars($row['judul']) ?></td>
                <td class="centered">
                  <img src="<?= htmlspecialchars($imgSrc) ?>" alt="Foto Sampul" class="img-standard img-book">
                </td>
                <td class="user-nim"><?= htmlspecialchars($row['nim']) ?></td>
                <td class="user-name"><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= date('Y-m-d', strtotime($row['tanggal_pinjam'])) ?></td>
                <td>
                  <div class="action-buttons">
                    <span class="badge <?= $statusClass ?> status-badge"><?= $statusLabel ?></span>
                    <!-- UPDATE BUTTON -->
                    <button class="btn btn-update btn-sm btn-edit">Update</button>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal for Adding/Updating Loan -->
  <div class="modal-overlay" id="loanModal">
    <div class="modal">
      <div class="modal-header">
        <div class="modal-title" id="modalTitle">Tambah Peminjaman</div>
        <button class="close-modal" id="closeLoanModal">&times;</button>
      </div>

      <form id="loanForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="loan_id" id="loanId" value="">

        <!-- SEARCHABLE BOOK INPUT -->
        <div class="form-group">
          <label class="form-label" for="bookSearch">Buku (ID - Judul)</label>
          <input type="hidden" name="buku_id" id="hiddenbook">
          <input type="text" list="bookList" id="bookSearch" name="book_name" class="form-input" placeholder="Cari ID atau Judul Buku..." required autocomplete="off">
          <datalist id="bookList">
            <!-- Populated by JS -->
          </datalist>
        </div>

        <!-- IMAGE PREVIEW -->
        <div class="form-group">
          <label class="form-label">Preview Sampul</label>
          <img src="../GAMBAR/BUKU REKOMEN/ATOMIC HABITS.jpg" alt="Preview Buku" class="img-preview" id="previewImage" style="display:block; margin: 0 auto;" />
        </div>

        <!-- SEARCHABLE USER INPUT -->
        <div class="form-group">
          <label class="form-label" for="userSearch">Peminjam (NIM - Nama)</label>
          <input type="hidden" name="nim" id="hiddennim">
          <input type="text" list="userList" id="userSearch" name="user_nim" class="form-input" placeholder="Cari NIM atau Nama..." required autocomplete="off">
          <datalist id="userList">
            <!-- Populated by JS -->
          </datalist>
        </div>

        <!-- Status Radio Buttons -->
        <div class="form-group radio-group">
          <label class="form-label">Status Peminjaman</label>
          <div class="radio-option">
            <input type="radio" id="statusDipinjam" name="loanStatus" value="dipinjam" checked />
            <label for="statusDipinjam">Dipinjam</label>
          </div>
          <div class="radio-option">
            <input type="radio" id="statusKembali" name="loanStatus" value="kembali" />
            <label for="statusKembali">Kembali</label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn-cancel" id="cancelLoanButton">Batal</button>
          <button type="submit" class="btn-save" id="saveLoanButton">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const modal = document.getElementById("loanModal");
    const form = document.getElementById("loanForm");
    const modalTitle = document.getElementById("modalTitle");
    const previewImage = document.getElementById("previewImage");
    const userList = document.getElementById("userList");
    const bookList = document.getElementById("bookList");
    const bookSearchInput = document.getElementById("bookSearch");
    const userSearchInput = document.getElementById("userSearch");

    // Store book data globally to access images later
    let booksData = [];

    // --- 0. LOAD DATA FOR DROPDOWNS ---
    function loadUsers() {
      fetch('../API/user.php') // Ensure this file exists
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            userList.innerHTML = '';
            data.data.forEach(user => {
              const option = document.createElement('option');
              option.value = user.label;
              userList.appendChild(option);
            });
          }
        })
        .catch(err => console.error("Error fetching users:", err));
    }

    function loadBooks() {
      fetch('../API/books.php') // Ensure this file exists
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            booksData = data.data; // Store for image lookup
            bookList.innerHTML = '';
            booksData.forEach(book => {
              const option = document.createElement('option');
              option.value = book.label;
              bookList.appendChild(option);
            });
          }
        })
        .catch(err => console.error("Error fetching books:", err));
    }

    loadUsers();
    loadBooks();

    // --- 1. AUTO-UPDATE IMAGE ON BOOK SELECT ---
    bookSearchInput.addEventListener('input', function() {
      const selectedVal = this.value;
      const book = booksData.find(b => b.label === selectedVal);

      if (book) {
        previewImage.src = book.cover;
        previewImage.style.display = 'block';

        // IMPORTANT → fill buku_id
        document.getElementById("hiddenbook").value = book.id;
      } else {
        if (selectedVal === "") {
          previewImage.src = "../GAMBAR/BUKU REKOMEN/ATOMIC HABITS.jpg";
        }
      }
    });

    userSearchInput.addEventListener('input', function() {
      const selectedVal = this.value;
      const user = Array.from(userList.options).find(o => o.value === selectedVal);

      if (user) {
        // user.label = "2405181064 - Farrel Ardan Awaly"
        const nim = selectedVal.split(" - ")[0];

        // IMPORTANT → fill nim
        document.getElementById("hiddennim").value = nim;
      }
    });


    // --- 2. HANDLE "ADD" BUTTON (+) ---
    document.getElementById("addLoanButton").addEventListener("click", function() {
      form.reset();
      modalTitle.innerText = "Tambah Peminjaman";
      previewImage.src = "../GAMBAR/BUKU REKOMEN/ATOMIC HABITS.jpg";
      document.getElementById("formAction").value = "add";
      document.getElementById("loanId").value = "";

      // ENABLE All Fields
      bookSearchInput.disabled = false;
      userSearchInput.disabled = false;

      // Status defaults to 'dipinjam'
      document.getElementById("statusDipinjam").checked = true;

      modal.style.display = "flex";
    });

    // --- 3. HANDLE "UPDATE" BUTTONS ---
    document.querySelector("tbody").addEventListener("click", function(e) {
      if (e.target.classList.contains("btn-edit")) {
        const btn = e.target;
        const row = btn.closest("tr");
        const loanId = row.getAttribute("data-id");

        const bookName = row.querySelector(".book-name").innerText;
        const imgSrc = row.querySelector(".img-book").src;
        const nim = row.querySelector(".user-nim").innerText;
        const name = row.querySelector(".user-name").innerText;
        const statusText = row.querySelector(".status-badge").innerText.toLowerCase();

        document.getElementById("formAction").value = "update";
        document.getElementById("loanId").value = loanId;

        // Populate Inputs (Note: exact ID matching is hard when disabled, 
        // so we just show the name for visual confirmation)
        bookSearchInput.value = bookName;
        document.getElementById("hiddenbook").value = bookName.split(' - ')[0];
        userSearchInput.value = nim + " - " + name;
        document.getElementById("hiddennim").value = nim;

        previewImage.src = imgSrc;
        previewImage.style.display = "block";

        // Set Status
        if (statusText.includes("kembali")) {
          document.getElementById("statusKembali").checked = true;
        } else {
          document.getElementById("statusDipinjam").checked = true;
        }

        // DISABLE fields (Only allow Status change)
        bookSearchInput.disabled = true;
        userSearchInput.disabled = true;

        modalTitle.innerText = "Update Status Peminjaman";
        modal.style.display = "flex";
      }
    });

    // --- 4. CLOSING LOGIC ---
    function closeModal() {
      modal.style.display = "none";
    }

    document.getElementById("closeLoanModal").addEventListener("click", closeModal);
    document.getElementById("cancelLoanButton").addEventListener("click", closeModal);
    window.addEventListener("click", function(e) {
      if (e.target === modal) {
        closeModal();
      }
    });
  </script>
</body>

</html>