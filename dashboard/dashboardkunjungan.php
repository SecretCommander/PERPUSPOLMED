<?php 
include '../conn.php';
include '../function.php';
require_once '../polmed_function.php';
check_petugas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim_action = $_POST['nim'];
    $action_type = $_POST['action_type']; // 'checkin' atau 'checkout'

    if ($action_type === 'checkin') {
        $result = kunjungan_checkin($conn, $nim_action);
        if ($result['success']) {
            echo "<script>alert('Berhasil Check-in! Streak: " . $result['poin_aktivitas'] . "'); window.location.href='dashboardkunjungan.php';</script>";
        } else {
            echo "<script>alert('Gagal Check-in: " . $result['message'] . "');</script>";
        }
    } elseif ($action_type === 'checkout') {
        $result = kunjungan_checkout($conn, $nim_action);
        if ($result['success']) {
            echo "<script>alert('Berhasil Check-out! Lama kunjungan: " . $result['duration'] . "'); window.location.href='dashboardkunjungan.php';</script>";
        } else {
            echo "<script>alert('Gagal Check-out: " . $result['message'] . "');</script>";
        }
    }
}

$dataPetugas = detail_petugas($conn, $_SESSION['petugas_id'])->fetch_assoc();
$dataKunjugan = list_all_kunjungan($conn);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="style.css">
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
            <li class="active"><a href="dashboardkunjungan.php">Kunjungan</a></li>
            <li><a href="dashboardberita.php">Berita</a></li>
            <li><a href="dashpeminjaman.php">Peminjaman</a></li>
            <li><a href="dashboardbuku.php">Buku</a></li>
        </ul>

        <div class="sidebar-footer">
            <a href="logout.php" class="btn-logout">
                Logout
            </a>
        </div>
    </div>
    <div class="main-content">
        <div class="content-container">
            <h1>Manajemen Kunjungan Perpustakaan</h1>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>NIM</th>
                            <th>Foto Profil</th>
                            <th>Nama</th>
                            <th>Prodi</th>
                            <th>Kontak</th>
                            <th>Status Kunjungan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($dataKunjugan)) : ?>
                            <?php 
                                // Cek apakah user ini sedang check-in (ada di dalam perpus)
                                $is_checked_in = kunjungan_is_checked_in($conn, $user['nim']);
                                $is_checked_out = kunjugan_status($conn, $user['nim']);
                                
                                // Setup class untuk tombol
                                // Jika checked in: Checkin disabled, Checkout active
                                // Jika NOT checked in: Checkin active, Checkout disabled
                                $btn_checkin_class = $is_checked_in ? 'btn-disabled' : '';
                                $btn_checkout_class = $is_checked_in ? '' : 'btn-disabled';
                                $btn_ischecked = $is_checked_out['tgl_dan_waktu_keluar'] != null ? 'btn-disabled' : '';
                                
                                // Foto profil fallback
                                $foto = !empty($user['foto_profil']) ? $user['foto_profil'] : '../GAMBAR/image.png';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($user['nim']) ?></td>
                                <td class="centered">
                                    <img src="<?= htmlspecialchars($foto) ?>" alt="Foto Profil" class="img-standard img-profile">
                                </td>
                                <td><?= htmlspecialchars($user['nama']) ?></td>
                                <td><?= htmlspecialchars($user['nama_prodi']) ?></td>
                                <td><?= htmlspecialchars($user['kontak']) ?></td>
                                
                                <!-- Status Badge -->
                                <td style="text-align: center;">
                                    <?php if($is_checked_in && $is_checked_out['tgl_dan_waktu_keluar'] == null): ?>
                                        <span class="badge badge-success">Sedang Berkunjung</span>
                                    <?php elseif($is_checked_in): ?>
                                        <span class="badge badge-info">Sudah Checkout</span>
                                    <?php else: ?>
                                        <span class="badge badge-no">Tidak Ada</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="action-buttons centered" >
                                        <!-- FORM CHECKIN -->
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="nim" value="<?= $user['nim'] ?>">
                                            <input type="hidden" name="action_type" value="checkin">
                                            <button type="submit" class="btn btn-checkin <?= $btn_checkin_class ?>">
                                                Checkin
                                            </button>
                                        </form>

                                        <!-- FORM CHECKOUT -->
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="nim" value="<?= $user['nim'] ?>">
                                            <input type="hidden" name="action_type" value="checkout">
                                            <button type="submit" class="btn btn-checkout <?= $btn_checkout_class ?> <?= $btn_ischecked ?>">
                                                Checkout
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>
</body>

</html>