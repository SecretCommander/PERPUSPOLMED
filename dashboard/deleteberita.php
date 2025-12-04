<?php 
require '../conn.php';
require '../function.php';
require '../polmed_function.php'; 

check_petugas($conn); 

if(!isset($_GET['berita_id'])){
    echo "<script>alert('Invalid request. No berita ID provided.'); window.location.href='dashboardberita.php';</script>";
    exit();
}

if(!empty($_GET['berita_id'])){
    $berita_id = (int)$_GET['berita_id'];
    
    if(delete_berita($conn)){ // Pass $berita_id as second argument if your function expects it
        echo "<script>alert('Berita deleted successfully.'); window.location.href='dashboardberita.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to delete berita.'); window.location.href='dashboardberita.php';</script>";
    }
} else {
    echo "<script>alert('Invalid berita ID.'); window.location.href='dashboardberita.php';</script>";
    exit();
}
?>