<?php
// IMAGE FUNCTION
function upload($file, $tipe)
{
    if ($file['error'] === 4) {
        echo "<script>alert('Please select a file to upload.');</script>";
        return false;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        echo "<script>alert('Invalid file type. Please upload an image file (jpg, jpeg, png).');</script>";
        return false;
    }

    if ($file['size'] > 15 * 1024 * 1024) {
        echo "<script>alert('File size exceeds the limit of 15MB.');</script>";
        return false;
    }

    $newFileName = uniqid() . '.' . $fileExtension;
    $dirName = '';
    if ($tipe == 'berita') {
        $dirName = 'berita';
    } elseif ($tipe == 'buku') {
        $dirName = 'buku';
    } elseif ($tipe == 'profile') {
        $dirName = 'profile';
    } else {
        echo "<script>alert('Invalid upload type.');</script>";
        return false;
    }

    $uploadPath = $dirName . '/' . $newFileName;

    if (!is_dir('../'.$dirName . "/")) {
        mkdir('../'.$dirName, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], '../'.$uploadPath)) {
        return $uploadPath;
    } else {
        echo "<script>alert('Failed to upload the file.');</script>";
        return false;
    }
}

// BOOK FUNCTION

function newest_book($conn)
{
    $sql = "SELECT * FROM buku ORDER BY buku_id DESC LIMIT 10";
    $result = mysqli_query($conn, $sql);
    return $result;
}

function insert_book($conn)
{
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $pengarang = mysqli_real_escape_string($conn, $_POST['pengarang']);
    $tahun_terbit = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $jumlah_eksemplar = mysqli_real_escape_string($conn, $_POST['eksemplar']);
    $deskripsi_buku = mysqli_real_escape_string($conn, $_POST['deskripsi_buku']);

    $cover_buku = upload($_FILES['cover_buku'], 'buku');
    if (!$cover_buku) {
        return false;
    }

    $sql = "INSERT INTO buku (judul, pengarang, tahun_terbit, jumlah_eksemplar, sampul, kategori, deskripsi_buku) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("sssisss", $judul, $pengarang, $tahun_terbit, $jumlah_eksemplar, $cover_buku, $kategori, $deskripsi_buku);
    try {
        $stmt->execute();
        echo "<script>alert('Book added successfully.'); window.location.href='dashboard_petugas.php';</script>";
    } catch (mysqli_sql_exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='add_book.php';</script>";
    }
}

function detail_book($conn, $buku_id)
{
    $sql = "SELECT * FROM buku WHERE buku_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $buku_id);
    $stmt->execute();
    $resultData = $stmt->get_result();
    return $resultData;
}

function update_book($conn, $buku_id)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $judul = mysqli_real_escape_string($conn, $_POST['judul']);
        $pengarang = mysqli_real_escape_string($conn, $_POST['pengarang']);
        $tahun_terbit = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
        $jumlah_eksemplar = mysqli_real_escape_string($conn, $_POST['eksemplar']);
        $deskripsi_buku = mysqli_real_escape_string($conn, $_POST['deskripsi_buku']);

        $book_lama = detail_book($conn, $buku_id)->fetch_assoc();
        $sampul = $book_lama['sampul'];
        if ($_FILES['cover_buku']['error'] !== 4) {
            $new_sampul = upload($_FILES['cover_buku'], 'buku');
            if ($new_sampul) {
                $sampul = $new_sampul;
            } else {
                return false;
            }
        }
        $sql = "UPDATE buku SET judul = ?, pengarang = ?, tahun_terbit = ?, jumlah_eksemplar = ?, sampul = ?, kategori = ?, deskripsi_buku = ? WHERE buku_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        $stmt->bind_param("sssisiss", $judul, $pengarang, $tahun_terbit, $jumlah_eksemplar, $sampul, $kategori, $deskripsi_buku, $buku_id);
        try {
            $stmt->execute();
            echo "<script>alert('Book updated successfully.'); window.location.href='dashboard_petugas.php';</script>";
        } catch (mysqli_sql_exception $e) {
            echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='edit_book.php?buku_id=" . $buku_id . "';</script>";
        }
    }
}

function delete_book($conn)
{
    $buku_id = mysqli_real_escape_string($conn, trim($_GET['buku_id']));

    $queryshow = "SELECT sampul FROM buku WHERE buku_id = ?";
    $stmtshow = mysqli_prepare($conn, $queryshow);
    $stmtshow->bind_param("s", $buku_id);
    $stmtshow->execute();
    $resultshow = $stmtshow->get_result();
    if ($resultshow->num_rows > 0) {
        $row = $resultshow->fetch_assoc();
        $sampul = $row['sampul'];
        $filepath = 'buku/' . $sampul;
        if ($row['sampul'] != "" || $row['sampul'] != null) {
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
    }
    $stmtshow->close();

    $sql = "DELETE FROM buku WHERE buku_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        $stmt->bind_param("s", $buku_id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

function list_all_books($conn)
{
    $sql = "SELECT * FROM buku ORDER BY buku_id DESC";
    $result = mysqli_query($conn, $sql);
    return $result;
}


//LEADERBOARD FUNCTION
function top_borrowed($conn)
{
    $sql = "SELECT buku.*, COUNT(peminjaman.buku_id) AS total_peminjaman 
            FROM buku 
            JOIN peminjaman ON buku.buku_id = peminjaman.buku_id 
            GROUP BY buku.buku_id 
            ORDER BY total_peminjaman DESC 
            LIMIT 10";
    $result = mysqli_query($conn, $sql);
    return $result;
}


function top_prodi($conn)
{
    $sql = "SELECT ps.prodi_id, ps.nama_prodi, COUNT(pm.peminjaman_id) AS total_peminjaman FROM program_studi ps JOIN pengguna u ON u.prodi_id = ps.prodi_id LEFT JOIN peminjaman pm ON pm.nim = u.nim GROUP BY ps.prodi_id, ps.nama_prodi ORDER BY total_peminjaman DESC LIMIT 10";
    $result = mysqli_query($conn, $sql);
    return $result;
}

function top_streak($conn)
{
    $sql = "SELECT * FROM pengguna p INNER JOIN program_studi ps ON p.prodi_id = ps.prodi_id ORDER BY poin_aktivitas DESC LIMIT 3";
    $result = mysqli_query($conn, $sql);
    return $result;
}

// USER FUNCTIONS

function register_user($conn)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nim = mysqli_real_escape_string($conn, $_POST['nim']);
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $kontak = mysqli_real_escape_string($conn, $_POST['kontak']);
        $jurusan_id = mysqli_real_escape_string($conn, $_POST['jurusan']);
        $prodi_id = mysqli_real_escape_string($conn, $_POST['prodi']);

        $sql = "INSERT INTO pengguna (nim, nama, password, kontak, jurusan_id, prodi_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        $stmt->bind_param("ssssii", $nim, $nama, $password, $kontak, $jurusan_id, $prodi_id);

        try {
            $stmt->execute();
            echo "<script>alert('Registration successful. You can now login.'); window.location.href='login.php';</script>";
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                echo "<script>alert('NIM already registered. Please use a different NIM.'); window.location.href='register.php';</script>";
            } else {
                echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='register.php';</script>";
            }
        }
    }
}

function login($conn, $nim, $password)
{
    $sql = "SELECT nim FROM pengguna WHERE nim =? AND password =?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("ss", $nim, $password);
    $stmt->execute();
    $resultData = $stmt->get_result();
    $stmt->close();

    if ($resultData->num_rows > 0) {
        $row = $resultData->fetch_assoc();
        $_SESSION['id_user'] = $row['nim'];
        return true;
    } else {
        return false;
    }
}

function outer_login($conn)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nim = mysqli_real_escape_string($conn, $_POST['nim']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $remember = isset($_POST['remember']);

        if (login($conn, $nim, $password)) {
            if ($remember) {
                setcookie('nim', $nim, time() + (86400 * 30), "/");
                setcookie('password', $password, time() + (86400 * 30), "/");
            }
            $_SESSION['id_user'] = $nim;
            $_SESSION['loggedin'] = true;
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('NIM atau Password Salah')</script>.";
        }
    }
}

function detail_user($conn, $nim)
{
    $sql = "SELECT * FROM pengguna JOIN program_studi ON pengguna.prodi_id = program_studi.prodi_id WHERE nim = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $resultData = $stmt->get_result();
    return $resultData;
}

function detail_user_exist($conn, $nim)
{
    $sql = "SELECT * FROM pengguna WHERE nim = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $resultData = $stmt->get_result();
    
    if ($resultData && $resultData->num_rows == 1) {
        return true;
    } else {
        return false;
    }
}

function update_user($conn, $nim)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
    }

    $user_lama = detail_user($conn, $nim)->fetch_assoc();
    $foto_profile = $user_lama['foto_profile'];

    if ($_FILES['foto_profile']['error'] !== 4) {
        $new_foto = upload($_FILES['foto_profile'], 'profile');
        if ($new_foto) {
            $foto_profile = $new_foto;
        } else {
            return false;
        }
    }

    $sql = "UPDATE pengguna SET nama = ?, password = ?, foto_profile = ? WHERE nim = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("ssss", $nama, $password, $foto_profile, $nim);
    try {
        $stmt->execute();
        echo "<script>alert('Profile updated successfully.'); window.location.href='dashboard.php';</script";
    } catch (mysqli_sql_exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='edit_profile.php?nim=" . $nim . "';</script>";
    }
}

function update_profile_picture($conn, $nim, $file)
{
    // Validate if user exists
    $check_sql = "SELECT nim FROM pengguna WHERE nim = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'User not found'];
    }
    $stmt->close();
    
    // Validate file upload
    if (!isset($file) || $file['error'] === 4) {
        return ['success' => false, 'message' => 'No file selected'];
    }
    
    // Upload new profile picture
    $new_foto = upload($file, 'profile');
    if (!$new_foto) {
        return ['success' => false, 'message' => 'File upload failed'];
    }
    
    // Get old photo to delete
    $get_old_sql = "SELECT foto_profile FROM pengguna WHERE nim = ?";
    $stmt = mysqli_prepare($conn, $get_old_sql);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $old_result = $stmt->get_result();
    $old_row = $old_result->fetch_assoc();
    $old_foto = $old_row['foto_profile'];
    $stmt->close();
    
    // Update profile picture in database
    $update_sql = "UPDATE pengguna SET foto_profile = ? WHERE nim = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    $stmt->bind_param("ss", $new_foto, $nim);
    
    if ($stmt->execute()) {
        // Delete old profile picture file if it exists
        if (!empty($old_foto)) {
            $old_filepath = '../' . $old_foto;
            if (file_exists($old_filepath)) {
                unlink($old_filepath);
            }
        }
        $stmt->close();
        return ['success' => true, 'message' => 'Profile picture updated successfully', 'filename' => $new_foto];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Database update failed'];
    }
}

function delete_user($conn)
{
    $nim = mysqli_real_escape_string($conn, trim($_GET['nim']));
    $query = "SELECT foto_profile FROM pengguna WHERE nim = ?";
    $stmt = mysqli_prepare($conn, $query);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $foto_profile = $row['foto_profile'];
        $filepath = 'profile/' . $foto_profile;
        if ($row['foto_profile'] != "" || $row['foto_profile'] != null) {
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
    }
    $stmt->close();

    $sql = "DELETE FROM pengguna WHERE nim = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $nim);
    try {
        $stmt->execute();
        return true;
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}


// PETUGAS FUNCTIONS

function outer_login_petugas($conn)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $result = login_petugas($conn, $username, $password);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['petugas_id'] = $row['petugas_id'];
            $_SESSION['adminloggedin'] = true;
            header("Location: dashboardkunjungan.php");
            exit();
        } else {
            echo "<script>alert('Username atau Password Salah')</script>.";
        }
    }
}

function login_petugas($conn, $username, $password)
{
    $sql = "SELECT petugas_id FROM petugas WHERE username =? AND password =?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $resultData = $stmt->get_result();
    return $resultData;
}

function check_petugas(){
    session_start();
    if (!isset($_SESSION['petugas_id']) || $_SESSION['adminloggedin'] !== true) {
        header("Location: admin.php");
        exit();
    }
}

function detail_petugas($conn, $petugas_id)
{
    $sql = "SELECT * FROM petugas WHERE petugas_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $petugas_id);
    $stmt->execute();
    $resultData = $stmt->get_result();
    return $resultData;
}



// BERITA FUNCTIONS

function upload_berita($conn)
{
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $petugas_id = mysqli_real_escape_string($conn, trim($_POST['petugas_id']));

    $gambar = upload($_FILES['gambar'], 'berita');
    if (!$gambar) {
        return false;
    }
    $sql = "INSERT INTO berita (judul_berita, isi_berita, gambar_berita, petugas_id) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("ssss", $judul, $isi, $gambar, $petugas_id);
    try {
        $stmt->execute();
        echo "<script>alert('News article uploaded successfully.'); window.location.href='dashboardberita.php';</script>";
    } catch (mysqli_sql_exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='dashboardberita.php';</script>";
    }
}

function news($conn)
{
    $sql = "SELECT * FROM berita ORDER BY berita_id DESC";
    $result = mysqli_query($conn, $sql);
    return $result;
}

function detail_berita($conn, $berita_id)
{
    $sql = "SELECT * FROM berita WHERE berita_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $berita_id);
    $stmt->execute();
    $resultData = $stmt->get_result();
    return $resultData;
}

function detail_berita_with_author($conn, $berita_id)
{
    $sql = "SELECT b.*, p.nama FROM berita b JOIN petugas p ON b.petugas_id = p.petugas_id WHERE b.berita_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $berita_id);
    $stmt->execute();
    $resultData = $stmt->get_result();
    return $resultData;
}

function update_berita($conn, $berita_id)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $judul = mysqli_real_escape_string($conn, $_POST['judul']);
        $isi = mysqli_real_escape_string($conn, $_POST['isi']);
        $petugas_id = mysqli_real_escape_string($conn, trim($_POST['petugas_id']));
    }


    $berita_lama = detail_berita($conn, $berita_id)->fetch_assoc();
    $gambar = $berita_lama['gambar_berita'];

    if ($_FILES['gambar']['error'] !== 4) {
        $new_gambar = upload($_FILES['gambar'], 'berita');
        if ($new_gambar) {
            $gambar = $new_gambar;
        } else {
            return false;
        }
    }

    $sql = "UPDATE berita SET judul_berita = ?, isi_berita = ?, gambar_berita = ?, petugas_id = ?  WHERE berita_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("sssss", $judul, $isi, $gambar, $petugas_id, $berita_id);
    try {
        $stmt->execute();
        echo "<script>alert('News article updated successfully.'); window.location.href='dashboardberita.php';</script>";
    } catch (mysqli_sql_exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='dashbaordberita.php?berita_id=" . $berita_id . "';</script>";
    }

}

function delete_berita($conn)
    {
        $idg = mysqli_real_escape_string($conn, trim($_GET['berita_id']));

        $queryshow = "SELECT gambar_berita FROM berita WHERE berita_id = ?";
        $stmtshow = mysqli_prepare($conn, $queryshow);
        $stmtshow->bind_param("s", $idg);
        $stmtshow->execute();
        $resultshow = $stmtshow->get_result();
        if ($resultshow->num_rows > 0) {
            $row = $resultshow->fetch_assoc();
            $gambar_berita = $row['gambar_berita'];
            $filepath = 'berita/' . $gambar_berita;
            if ($row['gambar_berita'] != "" || $row['gambar_berita'] != null) {
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }
        }
        $stmtshow->close();

        $sql = "DELETE FROM berita WHERE berita_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            $stmt->bind_param("s", $idg);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }
