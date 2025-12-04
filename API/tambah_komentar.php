<?php
require '../conn.php';
require_once '../polmed_function.php';
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['nim']) || !isset($_POST['buku_id']) || !isset($_POST['isi_komentar'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '&error=1');
        exit();
    }
    $result = insert_komentar($conn);
    
    $buku_id = trim($_POST['buku_id']);
    
    if ($result['success']) {
        header('Location: ../buku.php?id_buku=' . $buku_id . '&success=1#comments-atomic');
    } else {
        header('Location: ../buku.php?id_buku=' . $buku_id . '&error=' . urlencode($result['message']) . '#comments-atomic');
    }
    exit();
} else {
    // Jika bukan request POST, arahkan ke index
    header('Location: ../index.php');
    exit();
}

?>