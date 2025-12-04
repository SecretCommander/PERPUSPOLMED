<?php
include '../conn.php';
include '../function.php';
require_once '../polmed_function.php';
check_petugas();

// Asumsi: insert_book, update_book, dan delete_book ada di function.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] == 'add') {
        $result = insert_book($conn);
        // Tambahkan alert/redirect jika diperlukan
    } elseif ($_POST['action'] == 'update') {
        $result = update_book($conn, $_POST['buku_id']);
        // Tambahkan alert/redirect jika diperlukan
    }
    // Redirect setelah POST untuk mencegah resubmission
    header("Location: dashboardbuku.php");
    exit();
}
// Data Petugas
$dataPetugas = detail_petugas($conn, $_SESSION['petugas_id'])->fetch_assoc();

// Query Semua Buku (Looping)
$queryBuku = list_all_books($conn); // Asumsi: list_all_books mengembalikan objek result
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Buku</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ... (CSS Anda yang sudah ada) ... */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #121212;
            color: #ffffff;
            display: flex;
            height: 100vh;
        }

        .main-content {
            flex-grow: 1;
            background-color: #e0e0e0;
            padding: 20px;
            overflow-y: auto;
        }

        .content-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 100%;
            border-top: 4px solid #4f2ab5;
        }

        h1 {
            margin-bottom: 20px;
            color: #4f2ab5;
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ccc;
            color: black;
            vertical-align: top;
        }

        td {
            padding-top: 20px;
        }

        td.centered {
            vertical-align: middle;
        }

        td.centered img {
            margin-top: -8px;
        }
        td.centered {
            padding-top: 12px;
        }

        th {
            background-color: #4f2ab5;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        .centered {
            text-align: center;
        }

        /* Modifikasi Header Actions untuk tombol Tambah */
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        /* Styling Tombol Tambah */
        .add-button {
            display: inline-flex;
            background-color: #4f2ab5;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 24px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .add-button:hover {
            background-color: #381f7d;
        }

        .add-button::after {
            content: '+';
        }

        .table-container {
            overflow-x: auto;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: nowrap;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;

        }

        .btn-update {
            background-color: #4f2ab5;
            color: white;
        }

        .btn-delete {
            background-color: #e0e0e0;
            color: #4f2ab5;
            text-decoration: none;
        }

        .btn-delete:hover {
            background-color: #d0d0d0;

        }

        .btn-update:hover {
            background-color: #381f7d;
        }


        img {
            width: 100px;
            height: 150px;
            object-fit: cover;
        }

        /* Modal Styling */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal {
            background-color: white;
            border-radius: 8px;
            width: 750px;
            max-width: 90%;
            padding: 20px;
            color: #121212;
            max-height: 90vh;
            overflow-y: auto;
        }

        /* Image Preview Style */
        #previewImage {
            max-width: 100px;
            height: auto;
            display: none;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }


        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .form-group.radio-group,
        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .modal-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 18px;
            font-weight: bold;
            color: #4f2ab5;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: color 0.2s;
        }

        .close-modal:hover {
            color: #4f2ab5;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #4f2ab5;
        }

        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            min-height: 100px;
            resize: vertical;
        }

        .form-file {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background-color: #f9f9f9;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .btn-save {
            background-color: #4f2ab5;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-save:hover {
            background-color: #3a1f8c;
        }

        .btn-cancel {
            background-color: #e0e0e0;
            color: #4f2ab5;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-cancel:hover {
            background-color: #d0d0d0;
        }

        /* Radio button styling */
        .radio-group {
            margin-bottom: 15px;
            color: #121212;
        }

        .radio-option {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .radio-option input[type="radio"] {
            margin-right: 8px;
            accent-color: #4f2ab5;
        }

        /* Responsif */
        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.radio-group,
            .form-group.full-width {
                grid-column: 1;
            }

            .modal {
                max-width: 95%;
            }
        }

        /* ... (Media query yang sudah ada) ... */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .sidebar-header {
                padding: 15px;
                font-size: 16px;
            }

            .sidebar-menu li {
                padding: 12px 15px;
                font-size: 14px;
            }

            .content-container {
                padding: 15px;
            }

            .modal {
                width: 90%;
                margin: 0 5%;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 180px;
            }

            .sidebar-header {
                padding: 10px 15px;
                font-size: 14px;
            }

            .sidebar-menu li {
                padding: 10px 15px;
                font-size: 13px;
            }

            .modal {
                width: 95%;
                margin: 0 2.5%;
            }
        }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
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
            <li><a href="dashpeminjaman.php">Peminjaman</a></li>
            <li class="active"><a href="dashboardbuku.php">Buku</a></li>
        </ul>

        <div class="sidebar-footer">
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="content-container">
            <div class="header-actions">
                <h1>Manajemen Buku</h1>
                <!-- GANTI ID: addLoanButton -> addBookButton -->
                <div class="add-button" id="addBookButton"></div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Sampul</th>
                            <th>Pengarang</th>
                            <th>Tahun Terbit</th>
                            <th>Kategori</th>
                            <th>Jumlah Eksemplar</th>
                            <th>Deskripsi Buku</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Looping Data Buku
                        if ($queryBuku && mysqli_num_rows($queryBuku) > 0) {
                            while ($buku = mysqli_fetch_assoc($queryBuku)) {
                                // Tentukan sampul buku (placeholder jika NULL)
                                $sampul = !empty($buku['sampul']) ? '../' . $buku['sampul'] : '';
                        ?>
                                <tr>
                                    <td><?= htmlspecialchars($buku['judul']) ?></td>
                                    <td class="centered"><img src="<?= $sampul ?>" alt="Sampul Buku"></td>
                                    <td><?= htmlspecialchars($buku['pengarang']) ?></td>
                                    <td><?= htmlspecialchars($buku['tahun_terbit']) ?></td>
                                    <td><?= htmlspecialchars($buku['kategori']) ?></td>
                                    <td class="centered"><?= htmlspecialchars($buku['jumlah_eksemplar']) ?></td>
                                    <!-- Potong deskripsi agar tidak terlalu panjang di tabel -->
                                    <td><?= substr(htmlspecialchars($buku['deskripsi_buku']), 0, 80) ?>...</td>
                                    <td>
                                        <div class="action-buttons">
                                            <!-- Panggil fungsi JS editBook() -->
                                            <button class="btn btn-update" onclick="editBook(<?= $buku['buku_id'] ?>)">Edit</button>
                                            <!-- Tombol Delete, panggil fungsi JS deleteBook() -->
                                            <a href="delete_buku.php?buku_id=<?= $buku['buku_id'] ?>"
                                                class="btn btn-delete"
                                                onclick="return confirm('Yakin ingin menghapus berita ini?');">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="8" class="centered">Tidak ada data buku yang tersedia.</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for adding/updating book -->
    <!-- GANTI ID: loanModal -> bookModal -->
    <div class="modal-overlay" id="bookModal">
        <div class="modal">
            <div class="modal-header">
                <!-- GANTI ID: modalTitle -> modalTitle (tetap, tapi pastikan ini adalah elemen pembungkus teks) -->
                <div class="modal-title" id="modalTitle">Input Buku</div>
                <!-- GANTI ID: closeLoanModal -> closeBookModal -->
                <button class="close-modal" id="closeBookModal">&times;</button>
            </div>
            <!-- GANTI ID: loanForm -> bookForm -->
            <form id="bookForm" method="POST" enctype="multipart/form-data" action="dashboardbuku.php">
                <!-- Hidden inputs untuk aksi dan ID -->
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="buku_id" id="bookId">

                <div class="modal-body form-grid">
                    <!-- Ganti ID dan Name sesuai JS: bookName -> bookTitle, newsImage -> bookAuthor, borrowerEksemplar -> bookCopies, borrowerTerbit -> bookYear, Sampul -> bookCover, deskripsi -> bookDescription, Kategori -> kategori -->

                    <div class="form-group full-width">
                        <label class="form-label" for="bookTitle">Judul Buku</label>
                        <input type="text" id="bookTitle" name="judul" class="form-input" placeholder="Masukkan judul buku" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="bookAuthor">Nama Pengarang</label>
                        <input type="text" id="bookAuthor" name="pengarang" class="form-input" placeholder="Masukkan nama pengarang">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="bookYear">Tahun Terbit</label>
                        <input type="number" id="bookYear" name="tahun_terbit" class="form-input" placeholder="Masukkan tahun terbit" min="1900" max="<?= date('Y') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="bookCopies">Jumlah Eksemplar</label>
                        <input type="number" id="bookCopies" name="eksemplar" class="form-input" placeholder="Masukkan jumlah eksemplar" required min="1">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="bookCover">Input Sampul</label>
                        <input type="file" id="bookCover" name="cover_buku" class="form-file" accept="image/*">
                        <!-- Image Preview -->
                        <img id="previewImage" src="" alt="Sampul Preview">
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label" for="bookDescription">Deskripsi</label>
                        <textarea id="bookDescription" name="deskripsi_buku" class="form-textarea" placeholder="Masukkan Deskripsi Buku"></textarea>
                    </div>

                    <div class="form-group radio-group">
                        <label class="form-label">Kategori</label>
                        <div>
                            <div class="radio-option">
                                <input type="radio" id="BukuFiksi" name="kategori" value="FIKSI" required>
                                <label for="BukuFiksi">FIKSI</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="BukuNonFiksi" name="kategori" value="NON FIKSI">
                                <label for="BukuNonFiksi">NON FIKSI</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="BukuPendidikan" name="kategori" value="PENDIDIKAN">
                                <label for="BukuPendidikan">PENDIDIKAN</label>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal-footer">
                    <!-- GANTI ID: cancelLoanButton -> cancelBookButton -->
                    <button type="button" class="btn-cancel" id="cancelBookButton">Batal</button>
                    <!-- GANTI ID: saveLoanButton -> saveBookButton (dan ganti teks tombol jika perlu, tapi biarkan 'Simpan' untuk konsistensi) -->
                    <button type="submit" class="btn-save" id="saveBookButton">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Form for Deletion -->
    <form id="deleteForm" method="POST" action="dashboardbuku.php" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="buku_id" id="deleteBookId">
    </form>


    <!-- JAVASCRIPT -->
    <!-- Hapus script lama yang tidak terpakai -->
    <script>
        const modal = document.getElementById("bookModal");
        const form = document.getElementById("bookForm");
        const modalTitle = document.getElementById("modalTitle");
        const previewImg = document.getElementById("previewImage");
        const actionInput = document.getElementById("formAction");
        const idInput = document.getElementById("bookId");
        const titleInput = document.getElementById("bookTitle");
        const authorInput = document.getElementById("bookAuthor");
        const yearInput = document.getElementById("bookYear");
        const copiesInput = document.getElementById("bookCopies");
        const coverInput = document.getElementById("bookCover");
        const descInput = document.getElementById("bookDescription");
        const deleteForm = document.getElementById("deleteForm");
        const deleteBookId = document.getElementById("deleteBookId");

        // --- API Fictional URL ---
        const BOOK_API_URL = '../API/buku.php';

        // --- GLOBAL MODAL LOGIC ---
        function closeModalFunc() {
            modal.style.display = "none";
            form.reset();
            previewImg.style.display = "none";
            previewImg.src = "";
            idInput.value = "";
            coverInput.required = false; // Reset required state
        }
        document.getElementById("closeBookModal").addEventListener("click", closeModalFunc);
        document.getElementById("cancelBookButton").addEventListener("click", closeModalFunc);

        // Logic penutup modal ketika mengklik overlay
        document.getElementById('bookModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModalFunc();
            }
        });


        // --- 1. HANDLE ADD BUTTON ---
        document.getElementById("addBookButton").addEventListener("click", function() {
            form.reset();
            actionInput.value = "add";
            modalTitle.innerText = "Tambah Buku Baru";
            previewImg.style.display = "none";
            previewImg.src = "";
            // Set input file menjadi wajib saat ADD
            coverInput.required = true;
            modal.style.display = "flex";
        });

        // --- 2. HANDLE EDIT (AJAX RETRIEVE) ---
        function editBook(id) {
            modalTitle.innerText = "Loading...";
            modal.style.display = "flex";

            // Set input file menjadi opsional saat UPDATE
            coverInput.required = false;

            // AJAX Fetch Data
            fetch(BOOK_API_URL + '?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const buku = data.data;
                        idInput.value = buku.buku_id;
                        actionInput.value = "update";
                        titleInput.value = buku.judul;
                        authorInput.value = buku.pengarang;
                        yearInput.value = buku.tahun_terbit;
                        copiesInput.value = buku.jumlah_eksemplar;
                        descInput.value = buku.deskripsi_buku;
                        modalTitle.innerText = "Edit Buku: " + buku.judul;

                        // Set Radio Button
                        const radio = document.querySelector(`input[name="kategori"][value="${buku.kategori}"]`);
                        if (radio) {
                            radio.checked = true;
                        }

                        // Handle Image Preview
                        if (buku.sampul) {
                            // Path di dashboardbuku.php membutuhkan '../'
                            previewImg.src = "../" + buku.sampul;
                            previewImg.style.display = "block";
                        } else {
                            previewImg.style.display = "none";
                            previewImg.src = "";
                        }
                    } else {
                        alert("Gagal mengambil data buku.");
                        closeModalFunc();
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Terjadi kesalahan koneksi.");
                    closeModalFunc();
                });
        }

        // --- 3. HANDLE DELETE ---
        function deleteBook(id) {
            if (confirm("Apakah Anda yakin ingin menghapus buku ini? Aksi ini tidak dapat dibatalkan!")) {
                deleteBookId.value = id;
                deleteForm.submit();
            }
        }

        // --- 4. IMAGE PREVIEW ON FILE SELECT ---
        coverInput.addEventListener("change", function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = "block";
                }
                reader.readAsDataURL(file);
            } else if (actionInput.value === 'add') {
                previewImg.style.display = "none";
                previewImg.src = "";
            }
        });
    </script>
</body>

</html>