<?php
require '../conn.php';
require_once '../function.php';
session_start();

// Cek autentikasi
if (!isset($_SESSION['id_user']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../login.php");
    exit();
}

$nim = $_POST['nim'];
$redirect_to = $_POST['redirect_to'] ?? 'index.php'; 
$base_url = "../" . $redirect_to;
$result = update_profile_picture($conn, $nim, $_FILES["new_profile_pic"]); 

if ($result['success']) {
    header("Location: " . $base_url . "?success=photo_updated");
    exit();
} else {
    header("Location: " . $base_url . "?error=" . urlencode($result['message']));
    exit();
}
?>