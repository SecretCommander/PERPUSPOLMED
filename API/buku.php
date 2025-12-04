<?php
include '../conn.php';
include '../function.php';

header('Content-Type: application/json');

$dummyImages = [
    'GAMBAR/membaca.jpg',
    'GAMBAR/BUKU REKOMEN/ATOMIC HABITS.jpg',
    'GAMBAR/BUKU REKOMEN/BUMII MANUSIA.jpg',
    'GAMBAR/BUKU REKOMEN/LAUT BERCERITA .jpg',
    'GAMBAR/BUKU REKOMEN/PERPUSTAKAAN TENGAH MALAM.jpg',
];

if (!isset($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Missing buku ID parameter.']);
    exit();
}

$buku_id = trim($_GET['id']);

$result = detail_book($conn, $buku_id);

if ($result && $result->num_rows > 0) {
    $buku = $result->fetch_assoc();
    
    if (isset($buku['sampul']) && $buku['sampul']) {
    } else {

        $buku['sampul'] = '';
    }

    echo json_encode(['success' => true, 'data' => $buku]);
    
} else {
    // Buku tidak ditemukan
    http_response_code(404); 
    echo json_encode(['success' => false, 'message' => 'Book not found with the given ID.']);
}

$conn->close();
?>