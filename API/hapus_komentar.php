<?php
require_once '../conn.php';
require_once '../function.php';
require_once '../polmed_function.php';
session_start();
if(!isset($_SESSION['id_user']) || !detail_user_exist($conn, trim($_SESSION['id_user']))) {
    header('Location: ../login.php');
    exit();
} elseif(!isset($_GET['id_komentar']) || !isset($_GET['id_buku'])) {
    header('Location: ../index.php');
    exit();
} else {
    if(delete_komentar($conn)){
        echo "<script>alert('Komentar berhasil dihapus.'); window.location.href='../buku.php?id_buku=".$_GET['id_buku']."';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menghapus komentar.'); window.location.href='../buku.php?id_buku=".$_GET['id_buku']."';</script>";
        exit();
    }
    // $id_komentar = trim($_GET['id_komentar']);
    // $id_buku = trim($_GET['id_buku']);
    // $resultData = detail_user($conn, trim($_SESSION['id_user']));
    // $row1 = mysqli_fetch_assoc($resultData);

    // $komentarData = get_komentar_by_id($conn, $id_komentar);
    // if ($komentarData && $komentarData['nim'] === $row1['nim']) {
    //     delete_komentar($conn, $id_komentar);
    // }
    // header('Location: ../buku.php?id_buku='.$id_buku);
    // exit();
}
?>