<?php
// CRUD for jurusan
function list_jurusan($conn)
{
    $sql = "SELECT * FROM jurusan ORDER BY nama_jurusan";
    return mysqli_query($conn, $sql);
}

function detail_jurusan($conn, $jurusan_id)
{
    $sql = "SELECT * FROM jurusan WHERE jurusan_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $jurusan_id);
    $stmt->execute();
    return $stmt->get_result();
}

function insert_jurusan($conn)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama_jurusan = mysqli_real_escape_string($conn, $_POST['nama_jurusan']);
        $sql = "INSERT INTO jurusan (nama_jurusan) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        $stmt->bind_param("s", $nama_jurusan);
        try {
            $stmt->execute();
            return true;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
}

function update_jurusan($conn, $jurusan_id = null)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama_jurusan = mysqli_real_escape_string($conn, $_POST['nama_jurusan']);
        $sql = "UPDATE jurusan SET nama_jurusan = ? WHERE jurusan_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        $stmt->bind_param("ss", $nama_jurusan, $jurusan_id);
        try {
            $stmt->execute();
            return true;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
}

function delete_jurusan($conn)
{
    $jurusan_id = mysqli_real_escape_string($conn, trim($_GET['jurusan_id']));
    $sql = "DELETE FROM jurusan WHERE jurusan_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $jurusan_id);
    try {
        $stmt->execute();
        return true;
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

// CRUD for program_studi (prodi)
function list_prodi($conn)
{
    $sql = "SELECT p.*, j.nama_jurusan FROM program_studi p LEFT JOIN jurusan j ON p.jurusan_id = j.jurusan_id ORDER BY p.nama_prodi";
    return mysqli_query($conn, $sql);
}

function detail_prodi($conn, $prodi_id)
{
    $sql = "SELECT p.*, j.nama_jurusan FROM program_studi p LEFT JOIN jurusan j ON p.jurusan_id = j.jurusan_id WHERE p.prodi_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $prodi_id);
    $stmt->execute();
    return $stmt->get_result();
}

function insert_prodi($conn)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama_prodi = mysqli_real_escape_string($conn, $_POST['nama_prodi']);
        $jurusan_id = mysqli_real_escape_string($conn, $_POST['jurusan_id']);
        $sql = "INSERT INTO program_studi (jurusan_id, nama_prodi) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        $stmt->bind_param("ss", $jurusan_id, $nama_prodi);
        try {
            $stmt->execute();
            return true;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
}

function update_prodi($conn, $prodi_id = null)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama_prodi = mysqli_real_escape_string($conn, $_POST['nama_prodi']);
        $jurusan_id = mysqli_real_escape_string($conn, $_POST['jurusan_id']);
        $sql = "UPDATE program_studi SET jurusan_id = ?, nama_prodi = ? WHERE prodi_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        $stmt->bind_param("sss", $jurusan_id, $nama_prodi, $prodi_id);
        try {
            $stmt->execute();
            return true;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
}

function delete_prodi($conn)
{
    $prodi_id = mysqli_real_escape_string($conn, trim($_GET['prodi_id']));
    $sql = "DELETE FROM program_studi WHERE prodi_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $prodi_id);
    try {
        $stmt->execute();
        return true;
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

// KUNJUNGAN FUNCTIONS
// Check if a user is currently checked in (has a visit with no 'lama_kunjungan')
function kunjungan_is_checked_in($conn, $nim)
{
    $today = date('Y-m-d');
    // We check if there is any row where the date part of tgl_dan_waktu_kunjungan matches today
    $sql = "SELECT id_kunjungan FROM kunjungan WHERE nim = ? AND DATE(tgl_dan_waktu_kunjungan) = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("ss", $nim, $today);
    $stmt->execute();
    $res = $stmt->get_result();
    return ($res && $res->num_rows > 0);
}

function kunjugan_status($conn, $nim)
{
    $sql = "SELECT * FROM kunjungan WHERE nim = ? ORDER BY tgl_dan_waktu_kunjungan DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        return $res->fetch_assoc();
    }
    return false;
}

// Return open visit row or false
function kunjungan_get_open_visit($conn, $nim)
{
    $sql = "SELECT * FROM kunjungan WHERE nim = ? AND lama_kunjungan IS NULL ORDER BY tgl_dan_waktu_kunjungan DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        return $res->fetch_assoc();
    }
    return false;
}

// Auto close an open visit by capping duration at $cap_seconds seconds (default 24h)
function kunjungan_auto_close_visit($conn, $id_kunjungan, $cap_seconds = 86400)
{
    // use MySQL to compute capped duration and set lama_kunjungan
    $sql_upd = "UPDATE kunjungan SET lama_kunjungan = SEC_TO_TIME(LEAST(TIMESTAMPDIFF(SECOND, tgl_dan_waktu_kunjungan, NOW()), ?)) WHERE id_kunjungan = ?";
    $stmt = mysqli_prepare($conn, $sql_upd);
    $stmt->bind_param("ii", $cap_seconds, $id_kunjungan);
    try {
        $stmt->execute();
        return true;
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

// Check-in a user: insert visit and update poin_aktivitas (streak)
function kunjungan_checkin($conn, $nim)
{
    // Auto-resolve an already-open visit: if we find a row with lama_kunjungan IS NULL,
    // we will auto-close it (cap at 24 hours) and proceed to insert a new check-in.
    $open = kunjungan_get_open_visit($conn, $nim);
    $autoClosed = false;
    if ($open) {
        $autoClosed = kunjungan_auto_close_visit($conn, $open['id_kunjungan']);
    }

    // Get last completed visit to decide streak
    $sql_last = "SELECT tgl_dan_waktu_kunjungan FROM kunjungan WHERE nim = ? AND lama_kunjungan IS NOT NULL ORDER BY tgl_dan_waktu_kunjungan DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql_last);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $res = $stmt->get_result();

    $new_streak = 1;
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $last_visit_date = date('Y-m-d', strtotime($row['tgl_dan_waktu_kunjungan']));
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // get current poin_aktivitas
        $q = "SELECT poin_aktivitas FROM pengguna WHERE nim = ? LIMIT 1";
        $s = mysqli_prepare($conn, $q);
        $s->bind_param("s", $nim);
        $s->execute();
        $r = $s->get_result();
        $current_poin = 0;
        if ($r && $r->num_rows > 0) {
            $current_poin = (int)$r->fetch_assoc()['poin_aktivitas'];
        }

        if ($last_visit_date === $today) {
            // They already had a completed visit today — keep streak
            $new_streak = $current_poin > 0 ? $current_poin : 1;
        } elseif ($last_visit_date === $yesterday) {
            // continue streak
            $new_streak = $current_poin + 1;
        } else {
            // streak broken
            $new_streak = 1;
        }
    }

    // Insert visit for the new checkin
    $sql = "INSERT INTO kunjungan (nim, tgl_dan_waktu_kunjungan) VALUES (?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $nim);
    try {
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return ['success' => false, 'message' => 'Failed to insert checkin: ' . $e->getMessage()];
    }

    // Update poin_aktivitas
    $sql_upd = "UPDATE pengguna SET poin_aktivitas = ? WHERE nim = ?";
    $s2 = mysqli_prepare($conn, $sql_upd);
    $s2->bind_param("is", $new_streak, $nim);
    try {
        $s2->execute();
    } catch (mysqli_sql_exception $e) {
        return ['success' => true, 'message' => 'Check-in recorded but failed to update poin_aktivitas: ' . $e->getMessage()];
    }

    $ret = ['success' => true, 'message' => 'Check-in successful', 'poin_aktivitas' => $new_streak];
    if ($autoClosed) {
        $ret['auto_closed_previous_visit'] = true;
    }
    return $ret;
}

// Check-out a user: compute duration and store it in lama_kunjungan
function kunjungan_checkout($conn, $nim)
{
    // Find active visit
    $sql = "SELECT id_kunjungan, tgl_dan_waktu_kunjungan FROM kunjungan WHERE nim = ? AND lama_kunjungan IS NULL ORDER BY tgl_dan_waktu_kunjungan DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res || $res->num_rows === 0) {
        return ['success' => false, 'message' => 'No active check-in found.'];
    }
    $row = $res->fetch_assoc();
    $id_kunjungan = $row['id_kunjungan'];
    $tgl_in = $row['tgl_dan_waktu_kunjungan'];

    // Compute duration as string (HH:MM:SS) using TIMEDIFF
    $sql_upd = "UPDATE kunjungan SET tgl_dan_waktu_keluar = NOW(), lama_kunjungan = TIMEDIFF(NOW(), tgl_dan_waktu_kunjungan) WHERE id_kunjungan = ?";
    $su = mysqli_prepare($conn, $sql_upd);
    $su->bind_param("i", $id_kunjungan);
    try {
        $su->execute();
    } catch (mysqli_sql_exception $e) {
        return ['success' => false, 'message' => 'Failed to update checkout: ' . $e->getMessage()];
    }

    // Fetch updated record to return duration
    $sqld = "SELECT lama_kunjungan FROM kunjungan WHERE id_kunjungan = ?";
    $sd = mysqli_prepare($conn, $sqld);
    $sd->bind_param("i", $id_kunjungan);
    $sd->execute();
    $rd = $sd->get_result();
    $duration = null;
    if ($rd && $rd->num_rows > 0) {
        $duration = $rd->fetch_assoc()['lama_kunjungan'];
    }

    return ['success' => true, 'message' => 'Check-out successful', 'duration' => $duration, 'id_kunjungan' => $id_kunjungan];
}

// List kunjungan for a user
function list_kunjungan_user($conn, $nim)
{
    $sql = "SELECT * FROM kunjungan WHERE nim = ? ORDER BY tgl_dan_waktu_kunjungan DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    return $stmt->get_result();
}

// List all kunjungan (with user name)
function list_all_kunjungan($conn)
{
    $sql = "SELECT k.*, u.nama, u.kontak, ps.nama_prodi FROM kunjungan k LEFT JOIN pengguna u ON k.nim = u.nim LEFT JOIN program_studi ps ON u.prodi_id = ps.prodi_id ORDER BY k.tgl_dan_waktu_kunjungan DESC";
    return mysqli_query($conn, $sql);
}

// PEMINJAMAN FUNCTIONS
// Insert new peminjaman (borrow)
function insert_peminjaman($conn)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nim = mysqli_real_escape_string($conn, trim($_POST['nim']));
        $buku_id = mysqli_real_escape_string($conn, trim($_POST['buku_id']));

        // Check book availability
        $q = "SELECT jumlah_eksemplar FROM buku WHERE buku_id = ? LIMIT 1";
        $s = mysqli_prepare($conn, $q);
        $s->bind_param("s", $buku_id);
        $s->execute();
        $r = $s->get_result();
        if (!$r || $r->num_rows == 0) {
            return ['success' => false, 'message' => 'Book not found'];
        }
        $row = $r->fetch_assoc();
        $stock = (int)$row['jumlah_eksemplar'];
        if ($stock <= 0) {
            return ['success' => false, 'message' => 'No copies available'];
        }

        // Check if user already has this book borrowed and not returned
        $qcheck = "SELECT peminjaman_id FROM peminjaman WHERE nim = ? AND buku_id = ? AND status = 'dipinjam' LIMIT 1";
        $scheck = mysqli_prepare($conn, $qcheck);
        $scheck->bind_param("ss", $nim, $buku_id);
        $scheck->execute();
        $rcheck = $scheck->get_result();
        if ($rcheck && $rcheck->num_rows > 0) {
            return ['success' => false, 'message' => 'You already have this book borrowed'];
        }

        // Decrease stock atomically (handles concurrent borrow requests)
        $upd = "UPDATE buku SET jumlah_eksemplar = jumlah_eksemplar - 1 WHERE buku_id = ? AND jumlah_eksemplar > 0";
        $su = mysqli_prepare($conn, $upd);
        $su->bind_param("s", $buku_id);
        try {
            $su->execute();
            if ($su->affected_rows <= 0) {
                return ['success' => false, 'message' => 'No copies available (concurrent request)'];
            }
        } catch (mysqli_sql_exception $e) {
            return ['success' => false, 'message' => 'Failed to update book stock: ' . $e->getMessage()];
        }

        $sql = "INSERT INTO peminjaman (nim, buku_id, tanggal_pinjam, status) VALUES (?, ?, NOW(), 'dipinjam')";
        $stmt = mysqli_prepare($conn, $sql);
        $stmt->bind_param("ss", $nim, $buku_id);
        try {
            $stmt->execute();
            $peminjaman_id = mysqli_insert_id($conn);
            return ['success' => true, 'peminjaman_id' => $peminjaman_id];
        } catch (mysqli_sql_exception $e) {
            return ['success' => false, 'message' => 'Failed to create peminjaman: ' . $e->getMessage()];
        }
    }
}

// Return peminjaman details
function detail_peminjaman($conn, $peminjaman_id)
{
    $sql = "SELECT pm.*, b.judul, u.nama FROM peminjaman pm LEFT JOIN buku b ON pm.buku_id = b.buku_id LEFT JOIN pengguna u ON pm.nim = u.nim WHERE pm.peminjaman_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("i", $peminjaman_id);
    $stmt->execute();
    return $stmt->get_result();
}

// List peminjaman for a user
function list_peminjaman_user($conn, $nim)
{
    $sql = "SELECT pm.*, b.judul, b.sampul FROM peminjaman pm LEFT JOIN buku b ON pm.buku_id = b.buku_id WHERE pm.nim = ? ORDER BY pm.tanggal_pinjam DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    return $stmt->get_result();
}

// List all peminjaman (admin)
function list_all_peminjaman($conn)
{
    $sql = "SELECT pm.*, b.judul, b.sampul, u.nama, ps.nama_prodi FROM peminjaman pm LEFT JOIN buku b ON pm.buku_id = b.buku_id LEFT JOIN pengguna u ON pm.nim = u.nim LEFT JOIN program_studi ps ON u.prodi_id = ps.prodi_id ORDER BY pm.tanggal_pinjam DESC";
    return mysqli_query($conn, $sql);
}


function list_all_peminjaman_index($conn)
{
    $sql = "SELECT pm.*, b.judul, u.nama, ps.nama_prodi FROM peminjaman pm LEFT JOIN buku b ON pm.buku_id = b.buku_id LEFT JOIN pengguna u ON pm.nim = u.nim LEFT JOIN program_studi ps ON u.prodi_id = ps.prodi_id ORDER BY pm.tanggal_pinjam DESC LIMIT 7";
    return mysqli_query($conn, $sql);
}

// Function untuk random template testimoni
function get_random_testimoni_template($nama, $prodi, $judul)
{
    $templates = [
        "Hai! Namaku $nama dari prodi $prodi, aku baru saja meminjam buku berjudul \"$judul\". Buku ini sangat seru loh!",
        "Halo semuanya! Aku $nama, mahasiswa $prodi. Baru aja pinjam \"$judul\" dan wow, bukunya keren banget!",
        "Salam dari $nama, $prodi! Aku menemukan buku yang amazing yaitu \"$judul\". Wajib baca deh!",
        "Heyyy! $nama disini dari $prodi. Abis baca \"$judul\" dan sungguh tidak mengecewakan. Recommended!",
        "Hi! Aku $nama, anak $prodi. Kemarin pinjam \"$judul\" dan ternyata bukunya bagus banget. Must read!"
    ];

    return $templates[array_rand($templates)];
}

// Return a borrowed book (kembali)
function return_peminjaman($conn, $peminjaman_id)
{
    // Verify peminjaman exists and is dipinjam
    $q = "SELECT buku_id, status FROM peminjaman WHERE peminjaman_id = ? LIMIT 1";
    $s = mysqli_prepare($conn, $q);
    $s->bind_param("i", $peminjaman_id);
    $s->execute();
    $r = $s->get_result();
    if (!$r || $r->num_rows == 0) {
        return ['success' => false, 'message' => 'Peminjaman not found'];
    }
    $row = $r->fetch_assoc();
    if ($row['status'] !== 'dipinjam') {
        return ['success' => false, 'message' => 'Peminjaman already returned'];
    }

    // Update peminjaman set status and tanggal_kembali
    $sql = "UPDATE peminjaman SET status = 'kembali', tanggal_kembali = NOW() WHERE peminjaman_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("i", $peminjaman_id);
    try {
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        return ['success' => false, 'message' => 'Failed to set return: ' . $e->getMessage()];
    }

    // Increment book stock
    $inc = "UPDATE buku SET jumlah_eksemplar = jumlah_eksemplar + 1 WHERE buku_id = ?";
    $si = mysqli_prepare($conn, $inc);
    $si->bind_param("s", $row['buku_id']);
    try {
        $si->execute();
    } catch (mysqli_sql_exception $e) {
        // Return success but mention stock update failed
        return ['success' => true, 'message' => 'Return processed, but failed to update book stock: ' . $e->getMessage()];
    }

    return ['success' => true, 'message' => 'Return processed successfully'];
}

// Delete peminjaman record (admin)
function delete_peminjaman($conn)
{
    $id = mysqli_real_escape_string($conn, trim($_GET['peminjaman_id']));
    $sql = "DELETE FROM peminjaman WHERE peminjaman_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("i", $id);
    try {
        $stmt->execute();
        return true;
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

// LIKE PEMINJAMAN FUNCTIONS
function has_liked_peminjaman($conn, $peminjaman_id, $nim)
{
    $sql = "SELECT like_id FROM like_peminjaman WHERE peminjaman_id = ? AND nim = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("is", $peminjaman_id, $nim);
    $stmt->execute();
    $res = $stmt->get_result();
    return ($res && $res->num_rows > 0);
}

// Toggle like: if exists delete, otherwise insert
function toggle_like_peminjaman($conn, $peminjaman_id, $nim)
{
    if (has_liked_peminjaman($conn, $peminjaman_id, $nim)) {
        $sql = "DELETE FROM like_peminjaman WHERE peminjaman_id = ? AND nim = ?";
        $stmt = mysqli_prepare($conn, $sql);
        $stmt->bind_param("is", $peminjaman_id, $nim);
        try {
            $stmt->execute();
            return ['success' => true, 'action' => 'unliked'];
        } catch (mysqli_sql_exception $e) {
            return ['success' => false, 'message' => 'Failed to unlike: ' . $e->getMessage()];
        }
    } else {
        $sql = "INSERT INTO like_peminjaman (peminjaman_id, nim, tanggal_like) VALUES (?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        $stmt->bind_param("is", $peminjaman_id, $nim);
        try {
            $stmt->execute();
            return ['success' => true, 'action' => 'liked'];
        } catch (mysqli_sql_exception $e) {
            return ['success' => false, 'message' => 'Failed to like: ' . $e->getMessage()];
        }
    }
}

// Count likes for a peminjaman
function count_likes_peminjaman($conn, $peminjaman_id)
{
    $sql = "SELECT COUNT(like_id) as total FROM like_peminjaman WHERE peminjaman_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("i", $peminjaman_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        return (int)$res->fetch_assoc()['total'];
    }
    return 0;
}

// List likes by a user
function list_like_peminjaman_user($conn, $nim)
{
    $sql = "SELECT lp.*, pm.peminjaman_id, pm.buku_id, b.judul FROM like_peminjaman lp LEFT JOIN peminjaman pm ON lp.peminjaman_id = pm.peminjaman_id LEFT JOIN buku b ON pm.buku_id = b.buku_id WHERE lp.nim = ? ORDER BY lp.tanggal_like DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    return $stmt->get_result();
}

// KOMENTAR FUNCTIONS (for buku)
// List komentar for a buku, joined with pengguna data
function list_komentar_by_buku($conn, $buku_id)
{
    $sql = "SELECT k.*, u.nama FROM komentar k LEFT JOIN pengguna u ON k.nim = u.nim WHERE k.buku_id = ? ORDER BY k.tgl_komentar DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("i", $buku_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Get detail comment by id
function detail_komentar($conn, $komentar_id)
{
    $sql = "SELECT k.*, u.nama FROM komentar k LEFT JOIN pengguna u ON k.nim = u.nim WHERE k.komentar_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("i", $komentar_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Insert comment (POST expects nim, buku_id, isi_komentar)
function insert_komentar($conn)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nim = mysqli_real_escape_string($conn, trim($_POST['nim']));
        $buku_id = mysqli_real_escape_string($conn, trim($_POST['buku_id']));
        $isi = mysqli_real_escape_string($conn, trim($_POST['isi_komentar']));

        if ($isi === '') {
            return ['success' => false, 'message' => 'Comment cannot be empty'];
        }

        $sql = "INSERT INTO komentar (nim, buku_id, isi_komentar, tgl_komentar) VALUES (?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        $stmt->bind_param("sis", $nim, $buku_id, $isi);
        try {
            $stmt->execute();
            $id = mysqli_insert_id($conn);
            return ['success' => true, 'komentar_id' => $id];
        } catch (mysqli_sql_exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

// Delete komentar (admin or owner) — here we provide a simple delete by GET param
function delete_komentar($conn)
{
    $id = mysqli_real_escape_string($conn, trim($_GET['id_komentar']));
    $sql = "DELETE FROM komentar WHERE komentar_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("i", $id);
    try {
        $stmt->execute();
        return true;
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

// Count comments for a buku
function count_komentar_by_buku($conn, $buku_id)
{
    $sql = "SELECT COUNT(komentar_id) as total FROM komentar WHERE buku_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("i", $buku_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        return (int)$res->fetch_assoc()['total'];
    }
    return 0;
}

function list_komentar_user($conn, $nim)
{
    $sql = "SELECT k.*, b.judul, b.sampul FROM komentar k LEFT JOIN buku b ON k.buku_id = b.buku_id WHERE k.nim = ? ORDER BY k.tgl_komentar DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    return $stmt->get_result();
}
