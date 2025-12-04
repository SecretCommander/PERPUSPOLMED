<?php
require '../conn.php';
require '../polmed_function.php'; 
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'Silahkan login terlebih dahulu untuk menyukai cerita ini.']);
    exit;
}

$inputData = json_decode(file_get_contents('php://input'), true);
$peminjaman_id = isset($inputData['id']) ? (int)$inputData['id'] : 0;
$nim = trim($_SESSION['id_user']);

if ($peminjaman_id > 0) {
    
    // Call your function to Toggle (Like/Unlike)
    // This function returns an array like ['success' => true, 'action' => 'liked']
    $toggleResult = toggle_like_peminjaman($conn, $peminjaman_id, $nim);

    if ($toggleResult['success']) {
        // If successful, we need the NEW total count to update the UI
        $newCount = count_likes_peminjaman($conn, $peminjaman_id);

        // Send Success Response back to JS
        echo json_encode([
            'success'   => true,
            'action'    => $toggleResult['action'], // 'liked' or 'unliked'
            'new_count' => $newCount
        ]);
    } else {
        // Database error
        echo json_encode([
            'success' => false, 
            'message' => $toggleResult['message']
        ]);
    }

} else {
    // Invalid ID error
    echo json_encode(['success' => false, 'message' => 'Invalid Peminjaman ID']);
}
?>