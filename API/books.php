<?php
$dummyImages = [
    'GAMBAR/membaca.jpg',
    'GAMBAR/BUKU REKOMEN/ATOMIC HABITS.jpg',
    'GAMBAR/BUKU REKOMEN/BUMII MANUSIA.jpg',
    'GAMBAR/BUKU REKOMEN/LAUT BERCERITA.jpg',
    'GAMBAR/BUKU REKOMEN/PERPUSTAKAAN TENGAH MALAM.jpg',
];

include '../conn.php';
header('Content-Type: application/json');

$query = "SELECT buku_id, judul, sampul FROM buku ORDER BY judul ASC";
$result = mysqli_query($conn, $query);

$books = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        // use actual cover if exists, else fallback
        $coverPath = !empty($row['cover_buku']) 
            ? '../GAMBAR/' . $row['cover_buku'] 
            : '../GAMBAR/default_book.jpg';

        $books[] = [
            'id'    => $row['buku_id'],
            'judul' => $row['judul'],
            'cover' => '../'.$dummyImages[array_rand($dummyImages)],
            'label' => $row['buku_id'] . ' - ' . $row['judul']
        ];
    }

    echo json_encode(['success' => true, 'data' => $books]);
} else {
    echo json_encode(['success' => false, 'message' => 'No books found']);
}
?>
