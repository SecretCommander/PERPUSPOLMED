<?php 
require '../conn.php';
require '../function.php';
require '../polmed_function.php'; 

check_petugas($conn); 

if(!isset($_GET['buku_id'])){
    echo "<script>alert('Invalid request. No buku ID provided.'); window.location.href='dashboardbuku.php';</script>";
    exit();
}

if(!empty($_GET['buku_id'])){
    $buku_id = (int)$_GET['buku_id'];
    
    if(delete_book($conn)){ 
        echo "<script>alert('Buku deleted successfully.'); window.location.href='dashboardbuku.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to delete buku.'); window.location.href='dashboardbuku.php';</script>";
    }
} else {
    echo "<script>alert('Invalid buku ID.'); window.location.href='dashboardbuku.php';</script>";
    exit();
}

?>